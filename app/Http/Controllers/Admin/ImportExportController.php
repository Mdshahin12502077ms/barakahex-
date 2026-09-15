<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ParcelsImport;
use App\Models\Shop;
use App\Models\User;
use App\Models\Parcel;
use App\Models\ParcelEvent;
use App\Models\Merchant;
use Brian2694\Toastr\Facades\Toastr;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;


class ImportExportController extends Controller
{
    public function importExportView()
    {
        $merchants = Merchant::with('user')->get();
        return view('admin.bulk.import', compact('merchants'));
    }
    public function getShopsByMerchant(Request $request)
    {
        $shops = Shop::where('merchant_id', $request->merchant_id)->select('id', 'shop_name')
            ->get();
        return response()->json($shops);
    }

    public function detectAddress(Request $request)
    {
        $address = $request->input('address', '');
        $district = $request->input('district', null);
        $thana = $request->input('thana', null);
        $res = \App\Services\AddressLocationDetector::detectLocation($address, $district, $thana);
        return response()->json($res);
    }

    public function export()
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $filename = (Sentinel::getUser()->user_type == 'merchant' || Sentinel::getUser()->user_type == 'merchant_staff') ? 'admin/excel/merchant-parcel-import-sample.xlsx' : 'admin/excel/staff-parcel-import-sample.xlsx';
            if (file_exists(public_path($filename))):
                $filepath = public_path($filename);
                return Response::download($filepath, basename($filepath), [
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                ]);
            else:
                return back()->with('danger', __('file_not_found'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }
    public function import()
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $extension = request()->file('file')->getClientOriginalExtension();
            if ($extension != 'xlsx' && $extension != 'csv'):
                return back()->with('danger', __('file_type_not_supported'));
            endif;
            $file = request()->file('file')->store('import');

            $import = new ParcelsImport();
            $import->import($file);
            unlink(storage_path('app/' . $file));

            return back()->with('success', __('successfully_imported'));
        } catch (ValidationException $e) {
            $failures = $e->failures();

            $messages = [];
            foreach ($failures as $failure) {
                $messages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }

            $errorMessage = implode('<br>', $messages);

            return back()->with('danger', $errorMessage);
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return back()->with('danger', $e->getMessage());
        }
    }

    public function preview(Request $request)
    {
        if (isDemoMode()) {
            return response()->json(['status' => false, 'message' => __('this_function_is_disabled_in_demo_server')], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,txt',
            'shop' => 'required',
        ]);

        $user = Sentinel::getUser();
        $merchant_id = $request->input('merchant') ?: ($user->user_type == 'merchant' ? $user->merchant?->id : ($user->user_type == 'merchant_staff' ? $user->merchant_id : null));

        if (!$merchant_id) {
            return response()->json(['status' => false, 'message' => __('select_merchant')], 422);
        }

        try {
            $file = $request->file('file');
            $data = Excel::toArray([], $file);

            if (empty($data) || empty($data[0])) {
                return response()->json(['status' => false, 'message' => __('file_is_empty')], 422);
            }

            $sheet = $data[0];
            if (count($sheet) < 2) {
                return response()->json(['status' => false, 'message' => __('no_data_rows_found_in_file')], 422);
            }

            // Extract header row
            $headerRow = array_shift($sheet);
            $normalizedHeaders = [];
            foreach ($headerRow as $colIndex => $headerText) {
                $clean = strtolower(trim((string)$headerText));
                $clean = str_replace([' ', '-', '.'], '_', $clean);
                $normalizedHeaders[$colIndex] = $clean;
            }

            $rows = [];
            $unsafeChars = ['=', '+', '-', '@'];

            foreach ($sheet as $rowIndex => $rawRow) {
                // Check if row is entirely empty
                $nonEmpty = array_filter($rawRow, function ($v) {
                    return $v !== null && trim((string)$v) !== '';
                });
                if (empty($nonEmpty)) {
                    continue;
                }

                // Map row by header or index fallback
                $mapped = [];
                foreach ($rawRow as $colIndex => $val) {
                    $headerKey = $normalizedHeaders[$colIndex] ?? ('col_' . $colIndex);
                    $mapped[$headerKey] = is_string($val) ? trim($val) : $val;
                }

                $name = $mapped['customer_name'] ?? $mapped['name'] ?? $mapped['customer'] ?? ($rawRow[0] ?? '');
                $phone = $mapped['customer_phone_number'] ?? $mapped['customer_phone'] ?? $mapped['phone_number'] ?? $mapped['phone'] ?? ($rawRow[1] ?? '');
                $address = $mapped['customer_address'] ?? $mapped['address'] ?? ($rawRow[2] ?? ($rawRow[6] ?? ($rawRow[5] ?? '')));
                $district_input = $mapped['district'] ?? $mapped['city'] ?? $mapped['district_name'] ?? ($rawRow[3] ?? ($rawRow[7] ?? ($rawRow[6] ?? null)));
                $thana_input = $mapped['thana'] ?? $mapped['upazila'] ?? $mapped['thana_name'] ?? ($rawRow[4] ?? ($rawRow[8] ?? ($rawRow[7] ?? null)));
                $total_quantity = $mapped['total_quantity'] ?? $mapped['quantity'] ?? $mapped['qty'] ?? ($rawRow[5] ?? ($rawRow[4] ?? 1));
                $price = $mapped['price'] ?? $mapped['cash_collection'] ?? $mapped['cod'] ?? ($rawRow[6] ?? ($rawRow[3] ?? ($rawRow[2] ?? ($rawRow[0] ?? 0))));
                $selling_price = $mapped['selling_price'] ?? ($rawRow[7] ?? ($rawRow[4] ?? ($rawRow[3] ?? ($rawRow[1] ?? 0))));
                $weight = $mapped['weight'] ?? ($rawRow[8] ?? ($rawRow[10] ?? ($rawRow[9] ?? 1)));
                $parcel_type = $mapped['delivery_area'] ?? $mapped['parcel_type'] ?? $mapped['type'] ?? ($rawRow[9] ?? ($rawRow[8] ?? 'same_day'));
                $invoice = $mapped['customer_invoice_no'] ?? $mapped['invoice_no'] ?? $mapped['invoice'] ?? ($rawRow[10] ?? ($rawRow[2] ?? ($rawRow[3] ?? '')));
                $packaging = $mapped['packaging'] ?? ($rawRow[11] ?? 'no');
                $open_box = $mapped['open_box'] ?? ($rawRow[12] ?? ($rawRow[11] ?? 0));
                $home_delivery = $mapped['home_delivery'] ?? ($rawRow[13] ?? ($rawRow[12] ?? 1));
                $transfer_to_branch = $mapped['transfer_to_branch'] ?? ($rawRow[14] ?? ($rawRow[13] ?? 0));
                $destination_branch = $mapped['destination_branch'] ?? $mapped['destination_branch_id'] ?? $mapped['transfer_branch'] ?? ($rawRow[15] ?? ($rawRow[14] ?? ''));
                $note = $mapped['note'] ?? ($rawRow[16] ?? ($rawRow[15] ?? ($rawRow[8] ?? '')));

                // Phone cleaning and normalization
                $cleanPhone = preg_replace('/[^0-9]/', '', (string)$phone);
                if (str_starts_with($cleanPhone, '880')) {
                    $cleanPhone = '0' . substr($cleanPhone, 3);
                } elseif (str_starts_with($cleanPhone, '88')) {
                    $cleanPhone = '0' . substr($cleanPhone, 2);
                } elseif (!str_starts_with($cleanPhone, '0') && strlen($cleanPhone) == 10) {
                    $cleanPhone = '0' . $cleanPhone;
                }

                // Check validation issues
                $errors = [];
                if (empty($name)) {
                    $errors[] = 'Missing Name';
                }
                if (empty($cleanPhone)) {
                    $errors[] = 'Missing Phone';
                } elseif (!preg_match('/^01[3-9]\d{8}$/', $cleanPhone)) {
                    $errors[] = 'Invalid BD mobile number (' . $cleanPhone . ') - must be 11 digits starting with 013-019';
                }
                if (empty($address)) {
                    $errors[] = 'Missing Address';
                }

                // Database Address Verification (District & Thana)
                $loc = \App\Services\AddressLocationDetector::detectLocation($address, $district_input, $thana_input);
                if (!$loc['is_valid']) {
                    $errors[] = $loc['error'] ?? 'Please enter a valid address containing district and thana';
                }

                // Resolve destination branch if specified
                $destBranchId = null;
                $destBranchName = '';
                if (!empty($destination_branch)) {
                    if (is_numeric($destination_branch)) {
                        $b = \App\Models\Branch::find($destination_branch);
                    } else {
                        $b = \App\Models\Branch::where('name', 'like', '%' . trim($destination_branch) . '%')->first();
                    }
                    if ($b) {
                        $destBranchId = $b->id;
                        $destBranchName = $b->name;
                        $transfer_to_branch = 1;
                    }
                }

                $rows[] = [
                    'row_no' => count($rows) + 1,
                    'customer_name' => ltrim((string)$name, implode('', $unsafeChars)),
                    'customer_phone_number' => $cleanPhone ?: (string)$phone,
                    'customer_address' => ltrim((string)$address, implode('', $unsafeChars)),
                    'customer_invoice_no' => ltrim((string)$invoice, implode('', $unsafeChars)),
                    'district_id' => $loc['district_id'],
                    'district_name' => $loc['district_name'] ?? '',
                    'thana_id' => $loc['thana_id'],
                    'thana_name' => $loc['thana_name'] ?? '',
                    'total_quantity' => (is_numeric($total_quantity) && intval($total_quantity) >= 1) ? intval($total_quantity) : 1,
                    'price' => is_numeric($price) ? floatval($price) : 0,
                    'selling_price' => is_numeric($selling_price) ? floatval($selling_price) : 0,
                    'weight' => (is_numeric($weight) && floatval($weight) > 0) ? floatval($weight) : 1,
                    'packaging' => $packaging ?: 'no',
                    'parcel_type' => in_array($parcel_type, ['same_day', 'inside_city', 'outside_city', 'sub_city', 'sub_urban_area', 'frozen', 'third_party_booking', 'next_day']) ? $parcel_type : ($loc['suggested_parcel_type'] ?? 'same_day'),
                    'open_box' => !empty($open_box) && $open_box != '0' ? 1 : 0,
                    'home_delivery' => isset($home_delivery) && ($home_delivery === '0' || $home_delivery === 0) ? 0 : 1,
                    'transfer_to_branch' => !empty($transfer_to_branch) && $transfer_to_branch != '0' ? 1 : 0,
                    'destination_branch_id' => $destBranchId,
                    'destination_branch_name' => $destBranchName,
                    'note' => ltrim((string)$note, implode('', $unsafeChars)),
                    'is_valid' => empty($errors),
                    'errors' => $errors,
                ];
            }

            return response()->json([
                'status' => true,
                'total' => count($rows),
                'valid_count' => count(array_filter($rows, fn($r) => $r['is_valid'])),
                'invalid_count' => count(array_filter($rows, fn($r) => !$r['is_valid'])),
                'parcels' => $rows
            ]);
        } catch (\Exception $e) {
            Log::error('Import Preview Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function confirm(Request $request)
    {
        if (isDemoMode()) {
            return response()->json(['status' => false, 'message' => __('this_function_is_disabled_in_demo_server')], 403);
        }

        $request->validate([
            'shop' => 'required',
            'parcels' => 'required|array|min:1',
        ]);

        $user = Sentinel::getUser();
        $merchant_id = $request->input('merchant') ?: ($user->user_type == 'merchant' ? $user->merchant?->id : ($user->user_type == 'merchant_staff' ? $user->merchant_id : null));

        if (!$merchant_id) {
            return response()->json(['status' => false, 'message' => __('select_merchant')], 422);
        }

        $merchant = Merchant::with('shops')->find($merchant_id);
        if (!$merchant) {
            return response()->json(['status' => false, 'message' => __('merchant_not_found')], 404);
        }

        $shop_id = $request->input('shop');
        $shop = $merchant->shops->where('id', $shop_id)->first();
        $defaultShop = $merchant->shops->where('default', true)->first();
        $resolved_shop_id = $shop ? $shop->id : ($defaultShop ? $defaultShop->id : null);
        $pickup_branch_id = $shop ? ($shop->pickup_branch_id ?: ($defaultShop ? $defaultShop->pickup_branch_id : null)) : ($defaultShop ? $defaultShop->pickup_branch_id : null);
        $pickup_phone = $shop ? $shop->shop_phone_number : ($defaultShop ? $defaultShop->shop_phone_number : '');
        $pickup_address = $shop ? $shop->address : ($defaultShop ? $defaultShop->address : '');

        // 1. Strict pre-validation: Verify address and required fields for EVERY row
        $invalidRows = [];
        $verifiedLocations = [];

        foreach ($request->input('parcels') as $index => $row) {
            $rowNo = $index + 1;
            $customer_name = trim($row['customer_name'] ?? '');
            $customer_phone = trim($row['customer_phone_number'] ?? '');
            $customer_address = trim($row['customer_address'] ?? '');

            $rowIssues = [];
            if (empty($customer_name)) {
                $rowIssues[] = 'Missing Customer Name';
            }
            $cleanPhone = preg_replace('/[^0-9]/', '', $customer_phone);
            if (empty($customer_phone)) {
                $rowIssues[] = 'Missing Phone Number';
            } elseif (strlen($cleanPhone) != 11) {
                $rowIssues[] = 'Phone number must be 11 digits (' . strlen($cleanPhone) . ')';
            }

            if (empty($customer_address)) {
                $rowIssues[] = 'Missing Address';
            } else {
                $loc = \App\Services\AddressLocationDetector::detectLocation($customer_address, $row['district'] ?? null, $row['thana'] ?? null);
                if (!$loc['is_valid']) {
                    $rowIssues[] = $loc['error'] ?? 'District & Thana not found in database for address';
                } else {
                    $verifiedLocations[$index] = $loc;
                }
            }

            if (!empty($rowIssues)) {
                $invalidRows[] = "Row #{$rowNo} (" . ($customer_name ?: 'Unnamed') . " - '{$customer_address}'): " . implode(', ', $rowIssues);
            }
        }

        if (!empty($invalidRows)) {
            return response()->json([
                'status' => false,
                'message' => "Cannot save parcels! The following row(s) have invalid addresses or data:\n• " . implode("\n• ", $invalidRows),
                'invalid_rows' => $invalidRows
            ], 422);
        }

        DB::beginTransaction();
        try {
            $excelToCanonical = [
                'inside_city' => 'same_day',
                'outside_city' => 'sub_urban_area',
                'sub_city' => 'sub_city',
                'frozen' => 'frozen',
                'third_party_booking' => 'third_party_booking',
                'next_day' => 'next_day',
                'same_day' => 'same_day',
                'sub_urban_area' => 'sub_urban_area',
            ];

            $savedCount = 0;
            foreach ($request->input('parcels') as $index => $row) {
                $customer_name = trim($row['customer_name'] ?? '');
                $customer_phone = trim($row['customer_phone_number'] ?? '');
                $customer_address = trim($row['customer_address'] ?? '');
                $price = floatval($row['price'] ?? 0);
                $weight = floatval($row['weight'] ?? 1);
                if ($weight <= 0) $weight = 1;

                $cleanCustomerPhone = preg_replace('/[^0-9]/', '', $customer_phone);
                if (str_starts_with($cleanCustomerPhone, '880')) {
                    $cleanCustomerPhone = '0' . substr($cleanCustomerPhone, 3);
                } elseif (str_starts_with($cleanCustomerPhone, '88')) {
                    $cleanCustomerPhone = '0' . substr($cleanCustomerPhone, 2);
                } elseif (!str_starts_with($cleanCustomerPhone, '0') && strlen($cleanCustomerPhone) == 10) {
                    $cleanCustomerPhone = '0' . $cleanCustomerPhone;
                }

                if (empty($customer_name) || empty($cleanCustomerPhone) || empty($customer_address)) {
                    throw new \Exception("Row #" . ($index + 1) . ": Customer Name, Phone, and Address are required.");
                }

                if (!preg_match('/^01[3-9]\d{8}$/', $cleanCustomerPhone)) {
                    throw new \Exception("Row #" . ($index + 1) . ": Invalid mobile number ($customer_phone). Must be a valid 11-digit Bangladeshi mobile number (013-019).");
                }
                $customer_phone = $cleanCustomerPhone;

                $raw_type = $row['delivery_area'] ?? $row['parcel_type'] ?? 'same_day';
                $parcel_type = $excelToCanonical[$raw_type] ?? 'same_day';

                if ($parcel_type == "same_day" || $parcel_type == "next_day" || $parcel_type == "frozen") {
                    $location = 'inside_city';
                } elseif ($parcel_type == "sub_city") {
                    $location = 'sub_city';
                } elseif ($parcel_type == "sub_urban_area") {
                    $location = 'sub_urban_area';
                } else {
                    $location = 'inside_city';
                }

                // Charges
                $charge = data_get($merchant->charges, $weight . '.' . $parcel_type) ?? 0;
                $cod_charge = data_get($merchant->cod_charges, $location) ?? 0;
                $vat = $merchant->vat ?? 0.00;
                $total_delivery_charge = $charge + ($price / 100 * $cod_charge);
                $total_delivery_charge += ($total_delivery_charge / 100 * $vat);
                $payable = $price - $total_delivery_charge;

                // Dates
                if ($parcel_type == 'same_day') {
                    if (date('H') >= settingHelper('pickup_accept_start') && date('H') <= settingHelper('pickup_accept_end')) {
                        $pickup_date = date('Y-m-d', strtotime('+1 days'));
                        $delivery_date = date('Y-m-d', strtotime('+1 days'));
                    } else {
                        $pickup_date = date('Y-m-d');
                        $delivery_date = date('Y-m-d');
                    }
                } elseif ($parcel_type == 'sub_urban_area') {
                    $days = settingHelper('outside_dhaka_days') ?: 2;
                    if (date('H') >= settingHelper('pickup_accept_start') && date('H') <= settingHelper('pickup_accept_end')) {
                        $pickup_date = date('Y-m-d', strtotime('+1 days'));
                        $delivery_date = date('Y-m-d', strtotime('+' . ($days + 1) . ' days'));
                    } else {
                        $pickup_date = date('Y-m-d');
                        $delivery_date = date('Y-m-d', strtotime('+' . $days . ' days'));
                    }
                } else {
                    $pickup_date = date('Y-m-d');
                    $delivery_date = date('Y-m-d', strtotime('+1 days'));
                }

                // Resolve District & Thana
                $loc = $verifiedLocations[$index] ?? \App\Services\AddressLocationDetector::detectLocation($customer_address, $row['district'] ?? null, $row['thana'] ?? null);
                $districtId = $loc['district_id'] ?: (!empty($row['district_id']) ? $row['district_id'] : null);
                $thanaId = $loc['thana_id'] ?: (!empty($row['thana_id']) ? $row['thana_id'] : null);

                $total_quantity = isset($row['total_quantity']) && is_numeric($row['total_quantity']) && intval($row['total_quantity']) >= 1 ? intval($row['total_quantity']) : 1;
                $open_box = !empty($row['open_box']) && $row['open_box'] != '0' ? 1 : 0;
                $home_delivery = isset($row['home_delivery']) && ($row['home_delivery'] === '0' || $row['home_delivery'] === 0) ? 0 : 1;

                $destBranchId = !empty($row['destination_branch_id']) ? $row['destination_branch_id'] : null;
                if (!$destBranchId && !empty($row['destination_branch'])) {
                    if (is_numeric($row['destination_branch'])) {
                        $destBranchId = $row['destination_branch'];
                    } else {
                        $b = \App\Models\Branch::where('name', 'like', '%' . trim($row['destination_branch']) . '%')->first();
                        $destBranchId = $b ? $b->id : null;
                    }
                }

                $parcelNo = make_unique_parcel_id();
                $invoiceNo = !empty($row['customer_invoice_no']) ? $row['customer_invoice_no'] : ('inv-' . $merchant->id . rand(1000, 9999));

                $parcel = Parcel::create([
                    'parcel_no' => $parcelNo,
                    'merchant_id' => $merchant->id,
                    'short_url' => url('/tracking/' . $parcelNo),
                    'price' => $price,
                    'selling_price' => floatval($row['selling_price'] ?? 0),
                    'total_quantity' => $total_quantity,
                    'district_id' => $districtId,
                    'thana_id' => $thanaId,
                    'open_box' => $open_box,
                    'home_delivery' => $home_delivery,
                    'destination_branch_id' => $destBranchId,
                    'customer_name' => $customer_name,
                    'customer_invoice_no' => $invoiceNo,
                    'customer_phone_number' => $customer_phone,
                    'customer_address' => $customer_address,
                    'note' => $row['note'] ?? '',
                    'packaging' => $row['packaging'] ?? 'no',
                    'weight' => $weight,
                    'parcel_type' => $parcel_type,
                    'charge' => $charge,
                    'cod_charge' => $cod_charge,
                    'vat' => $vat,
                    'total_delivery_charge' => floor($total_delivery_charge),
                    'payable' => ceil($payable),
                    'location' => $location,
                    'pickup_shop_phone_number' => $pickup_phone,
                    'pickup_address' => $pickup_address,
                    'pickup_branch_id' => $pickup_branch_id,
                    'shop_id' => $resolved_shop_id,
                    'pickup_date' => $pickup_date,
                    'date' => date('Y-m-d'),
                    'delivery_date' => $delivery_date,
                    'user_id' => $user->id,
                    'status' => 'pending'
                ]);

                ParcelEvent::create([
                    'parcel_id' => $parcel->id,
                    'user_id' => $user->id,
                    'title' => 'parcel_create_event',
                ]);

                $savedCount++;
            }

            DB::commit();

            $redirectUrl = ($user->user_type == 'merchant')
                ? route('merchant.parcel')
                : (($user->user_type == 'merchant_staff') ? route('merchant.staff.parcel') : route('parcel'));

            return response()->json([
                'status' => true,
                'message' => $savedCount . ' ' . __('parcels_successfully_imported'),
                'redirect' => $redirectUrl
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import Confirm Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
