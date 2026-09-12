<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Merchant;
use App\Models\Parcel;
use App\Models\Thana;
use App\Repositories\Interfaces\ParcelInterface;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\MerchantApiTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CourierApiController extends Controller
{
    use ApiReturnFormatTrait, MerchantApiTrait;

    protected $parcelRepo;

    public function __construct(ParcelInterface $parcelRepo)
    {
        $this->parcelRepo = $parcelRepo;
    }

    /**
     * Resolve authenticated merchant from Api-Key and Secret-Key headers
     */
    protected function authenticateMerchant(Request $request)
    {
        $apiKey = $request->header('Api-Key') ?? $request->header('api_key') ?? $request->header('api-key');
        $secretKey = $request->header('Secret-Key') ?? $request->header('secret_key') ?? $request->header('secret-key');

        if (empty($apiKey) || empty($secretKey)) {
            return null;
        }

        return Merchant::with('user', 'shops')
            ->where('api_key', $apiKey)
            ->where('secret_key', $secretKey)
            ->first();
    }

    /**
     * Create parcel / order from external eCommerce platform (Steadfast/Pathao compatible)
     * POST /api/create_order
     */
    public function createOrder(Request $request): JsonResponse
    {
        $merchant = $this->authenticateMerchant($request);
        if (!$merchant) {
            return response()->json([
                'status'  => 401,
                'message' => 'Unauthorized. Invalid Api-Key or Secret-Key header.',
            ], 401);
        }

        if ($merchant->user && ($merchant->user->status == \App\Enums\StatusEnum::INACTIVE || $merchant->user->status == 2)) {
            return response()->json([
                'status'  => 403,
                'message' => 'Merchant account is inactive or suspended.',
            ], 403);
        }

        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'customer_name'         => 'required|string|max:100',
            'customer_phone'        => 'nullable|string|between:8,30',
            'customer_phone_number' => 'nullable|string|between:8,30',
            'phone'                 => 'nullable|string|between:8,30',
            'customer_address'      => 'nullable|string',
            'address'               => 'nullable|string',
            'customer_invoice_no'   => 'nullable|string|max:50',
            'invoice_no'            => 'nullable|string|max:50',
            'order_id'              => 'nullable|string|max:50',
            'price'                 => 'nullable|numeric|min:0',
            'cod_amount'            => 'nullable|numeric|min:0',
            'amount'                => 'nullable|numeric|min:0',
            'weight'                => 'nullable|numeric|min:0.1',
            'selling_price'         => 'nullable|numeric|min:0',
            'note'                  => 'nullable|string|max:191',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Normalize fields
        $customerPhone = $request->customer_phone ?? $request->customer_phone_number ?? $request->phone;
        if (empty($customerPhone)) {
            return response()->json([
                'status'  => 422,
                'message' => 'Customer phone number is required (customer_phone, customer_phone_number, or phone).',
            ], 422);
        }

        $customerAddress = $request->customer_address ?? $request->address;
        if (empty($customerAddress)) {
            return response()->json([
                'status'  => 422,
                'message' => 'Customer delivery address is required (customer_address or address).',
            ], 422);
        }

        $invoiceNo = $request->customer_invoice_no ?? $request->invoice_no ?? $request->order_id;
        if (empty($invoiceNo)) {
            $invoiceNo = 'INV-' . $merchant->id . '-' . time() . rand(10, 99);
        }

        $price = $request->price ?? $request->cod_amount ?? $request->amount ?? 0;
        $weight = $request->weight ?? 1;

        // Resolve District: by ID or by Name
        $districtId = null;
        $districtInput = $request->district_id ?? $request->city_id ?? $request->district ?? $request->city;
        if (!empty($districtInput)) {
            if (is_numeric($districtInput)) {
                $districtModel = District::find($districtInput);
            } else {
                $districtName = trim($districtInput);
                $districtModel = District::where('name', 'LIKE', '%' . $districtName . '%')->first();
            }
            if ($districtModel) {
                $districtId = $districtModel->id;
            }
        }

        // Resolve Thana: by ID or by Name
        $thanaId = null;
        $thanaInput = $request->thana_id ?? $request->thana;
        if (!empty($thanaInput)) {
            if (is_numeric($thanaInput)) {
                $thanaModel = Thana::find($thanaInput);
            } else {
                $thanaName = trim($thanaInput);
                $thanaQuery = Thana::where('name', 'LIKE', '%' . $thanaName . '%');
                if ($districtId) {
                    $thanaQuery->where('district_id', $districtId);
                }
                $thanaModel = $thanaQuery->first() ?? Thana::where('name', 'LIKE', '%' . $thanaName . '%')->first();
            }
            if ($thanaModel) {
                $thanaId = $thanaModel->id;
                if (!$districtId && $thanaModel->district_id) {
                    $districtId = $thanaModel->district_id;
                }
            }
        }

        // Determine or normalize parcel_type (inside Dhaka vs outside Dhaka vs sub city)
        $rawType = strtolower(trim($request->parcel_type ?? ($request->delivery_type ?? '')));
        
        if (in_array($rawType, ['inside_dhaka', 'inside_city', 'dhaka', 'next_day', 'same_day'])) {
            $parcelType = ($rawType === 'same_day') ? 'same_day' : 'next_day';
        } elseif (in_array($rawType, ['outside_dhaka', 'outside_city', 'sub_urban_area'])) {
            $parcelType = 'outside_city';
        } elseif ($rawType === 'sub_city') {
            $parcelType = 'sub_city';
        } else {
            // Auto-detect from District and Thana
            $districtStr = strtolower(trim($districtModel->name ?? ($request->district ?? ($request->city ?? ''))));
            $thanaStr    = strtolower(trim($thanaModel->name ?? ($request->thana ?? '')));

            $subCities = ['savar', 'keraniganj', 'dhamrai', 'tongi', 'gazipur', 'narayanganj', 'সাভার', 'কেরানীগঞ্জ', 'টঙ্গী', 'গাজীপুর', 'নারায়ণগঞ্জ'];
            
            if (in_array($districtStr, ['gazipur', 'narayanganj', 'গাজীপুর', 'নারায়ণগঞ্জ']) || in_array($thanaStr, $subCities)) {
                $parcelType = 'sub_city';
            } elseif ($districtStr === 'dhaka' || $districtStr === 'ঢাকা' || str_contains($districtStr, 'dhaka') || str_contains($districtStr, 'ঢাকা')) {
                $parcelType = 'next_day';
            } elseif (!empty($districtStr)) {
                $parcelType = 'outside_city';
            } else {
                // Fallback: check if address mentions Dhaka
                $addressLower = strtolower($customerAddress);
                if (str_contains($addressLower, 'dhaka') || str_contains($addressLower, 'ঢাকা')) {
                    $parcelType = 'next_day';
                } else {
                    $parcelType = 'outside_city';
                }
            }
        }

        // Build internal request for repository
        $storeData = new Request([
            'merchant'              => $merchant->id,
            'created_by'            => $merchant->user_id ?? $merchant->id,
            'customer_name'         => $request->customer_name,
            'customer_phone_number' => $customerPhone,
            'customer_address'      => $customerAddress,
            'customer_invoice_no'   => $invoiceNo,
            'price'                 => (float) $price,
            'selling_price'         => (float) ($request->selling_price ?? $price),
            'weight'                => (float) $weight,
            'parcel_type'           => $parcelType,
            'district_id'           => $districtId,
            'city_id'               => $districtId,
            'thana_id'              => $thanaId,
            'note'                  => $request->note ?? '',
            'packaging'             => $request->packaging ?? 'no',
            'fragile'               => $request->fragile ?? 0,
            'open_box'              => $request->open_box ?? 0,
            'home_delivery'         => $request->home_delivery ?? 0,
            'shop'                  => $request->shop_id ?? $request->shop ?? '',
        ]);

        try {
            $parcel = $this->parcelRepo->store($storeData);

            if (!$parcel || !isset($parcel->id)) {
                return response()->json([
                    'status'  => 500,
                    'message' => 'Failed to store parcel. Please check your account settings.',
                ], 500);
            }

            return response()->json([
                'status'         => 200,
                'message'        => 'Order created successfully',
                'consignment_id' => $parcel->parcel_no,
                'parcel_no'      => $parcel->parcel_no,
                'tracking_code'  => $parcel->parcel_no,
                'invoice_no'     => $parcel->customer_invoice_no,
                'delivery_status'=> $parcel->status ?? 'pending',
                'collection_amount' => (float) $parcel->price,
                'delivery_charge'   => (float) $parcel->total_delivery_charge,
                'payable_amount'    => (float) $parcel->payable,
                'tracking_url'   => $parcel->short_url ?? url('/tracking/' . $parcel->parcel_no),
                'created_at'     => $parcel->created_at ? $parcel->created_at->toDateTimeString() : now()->toDateTimeString(),
            ], 200);

        } catch (\Exception $e) {
            Log::error('External Courier API Create Order Error: ' . $e->getMessage());
            return response()->json([
                'status'  => 500,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check order / parcel status
     * GET /api/order_status/{tracking_id}
     * or POST /api/order_status
     */
    public function orderStatus(Request $request, $id = null): JsonResponse
    {
        $merchant = $this->authenticateMerchant($request);
        if (!$merchant) {
            return response()->json(['status' => 401, 'message' => 'Unauthorized'], 401);
        }

        $queryIdentifier = $id ?? $request->consignment_id ?? $request->parcel_no ?? $request->tracking_code ?? $request->invoice_no;

        if (empty($queryIdentifier)) {
            return response()->json([
                'status'  => 422,
                'message' => 'Please provide consignment_id, parcel_no, or invoice_no.',
            ], 422);
        }

        $parcel = Parcel::where('merchant_id', $merchant->id)
            ->where(function ($q) use ($queryIdentifier) {
                $q->where('parcel_no', $queryIdentifier)
                  ->orWhere('customer_invoice_no', $queryIdentifier)
                  ->orWhere('id', $queryIdentifier);
            })
            ->with('district', 'thana')
            ->first();

        if (!$parcel) {
            return response()->json([
                'status'  => 404,
                'message' => 'Order not found with provided identifier.',
            ], 404);
        }

        return response()->json([
            'status'            => 200,
            'consignment_id'    => $parcel->parcel_no,
            'parcel_no'         => $parcel->parcel_no,
            'invoice_no'        => $parcel->customer_invoice_no,
            'delivery_status'   => $parcel->status,
            'customer_name'     => $parcel->customer_name,
            'customer_phone'    => $parcel->customer_phone_number,
            'customer_address'  => $parcel->customer_address,
            'district'          => $parcel->district->name ?? null,
            'thana'             => $parcel->thana->name ?? null,
            'collection_amount' => (float) $parcel->price,
            'delivery_charge'   => (float) $parcel->total_delivery_charge,
            'payable_amount'    => (float) $parcel->payable,
            'tracking_url'      => $parcel->short_url ?? url('/tracking/' . $parcel->parcel_no),
            'created_at'        => $parcel->created_at ? $parcel->created_at->toDateTimeString() : null,
            'delivered_at'      => $parcel->delivered_date,
        ], 200);
    }

    /**
     * Cancel order
     * POST /api/cancel_order
     */
    public function cancelOrder(Request $request): JsonResponse
    {
        $merchant = $this->authenticateMerchant($request);
        if (!$merchant) {
            return response()->json(['status' => 401, 'message' => 'Unauthorized'], 401);
        }

        $queryIdentifier = $request->consignment_id ?? $request->parcel_no ?? $request->invoice_no;
        if (empty($queryIdentifier)) {
            return response()->json(['status' => 422, 'message' => 'Missing consignment_id or invoice_no'], 422);
        }

        $parcel = Parcel::where('merchant_id', $merchant->id)
            ->where(function ($q) use ($queryIdentifier) {
                $q->where('parcel_no', $queryIdentifier)
                  ->orWhere('customer_invoice_no', $queryIdentifier);
            })
            ->first();

        if (!$parcel) {
            return response()->json(['status' => 404, 'message' => 'Parcel not found'], 404);
        }

        if (!in_array($parcel->status, ['pending', 'pickup-assigned', 're-schedule-pickup'])) {
            return response()->json([
                'status'  => 400,
                'message' => "Order cannot be cancelled. Current status is {$parcel->status}.",
            ], 400);
        }

        $parcel->status = 'cancel';
        $parcel->note = $request->reason ?? 'Cancelled by merchant via API';
        $parcel->save();

        return response()->json([
            'status'  => 200,
            'message' => 'Order cancelled successfully',
            'parcel_no' => $parcel->parcel_no,
        ], 200);
    }

    /**
     * List all active districts
     * GET /api/districts
     */
    public function getDistricts(): JsonResponse
    {
        $districts = District::active()
            ->select('id', 'name', 'division_id')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'status'    => 200,
            'districts' => $districts,
        ], 200);
    }

    /**
     * List thanas, optionally filtered by district_id
     * GET /api/thanas
     */
    public function getThanas(Request $request): JsonResponse
    {
        $query = Thana::active()->select('id', 'name', 'district_id')->orderBy('name', 'asc');

        if ($request->has('district_id') && !empty($request->district_id)) {
            $query->where('district_id', $request->district_id);
        }

        $thanas = $query->get();

        return response()->json([
            'status' => 200,
            'thanas' => $thanas,
        ], 200);
    }
}
