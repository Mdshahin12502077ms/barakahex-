<?php

namespace App\Http\Controllers\Api\Merchant\Setting;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Request;
use App\Http\Resources\Api\MerchantResource;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Traits\SendMailTrait;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\Interfaces\Merchant\MerchantInterface;
class MerchantController extends Controller
{
    use ApiReturnFormatTrait;

    protected $merchantRepo;


    public function __construct(MerchantInterface $merchantRepo)
    {

        $this->merchantRepo     = $merchantRepo;

    }
    public function merchant(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user           = jwtUser();
            if ($user->user_type == 'merchant_staff') {
                $merchant   = Merchant::where('id', $user->merchant_id)->latest()->paginate(10);
            } elseif ($user->user_type == 'merchant') {
                $merchant   = Merchant::where('user_id', $user->id)->latest()->paginate(10);
            }

            $data = [
                'merchant' => MerchantResource::collection($merchant),
                'paginate' => [
                    'total'             => $merchant->total(),
                    'current_page'      => $merchant->currentPage(),
                    'per_page'          => $merchant->perPage(),
                    'last_page'         => $merchant->lastPage(),
                    'prev_page_url'     => $merchant->previousPageUrl(),
                    'next_page_url'     => $merchant->nextPageUrl(),
                    'path'              => $merchant->path(),
                ],
            ];

            return $this->responseWithSuccess('merchant_info_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }

    public function updateMerchant(Request $request, $id = null): \Illuminate\Http\JsonResponse
    {

        $user                      = jwtUser();
        if ($user->user_type == 'merchant') {
            $request['merchant']   = $user->merchant->id;
        }elseif($user->user_type == 'merchant_staff') {
            $request['merchant']   = $user->merchant_id;
        }

        $validator = Validator::make($request->all(), [
            'company'           => 'required',
            'trade_license'     => 'mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP,pdf|max:5120',
            'nid'               => 'mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP,pdf|max:5120',
            'phone_number'      => 'required|between:8,30,'.\Request()->merchant,
            'website'           => 'url',
            'vat'               => 'numeric',
        ]);

        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $this->merchantRepo->updateMerchantByMerchant($request);
            return $this->responseWithSuccess('Merchant updated successfully');

        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }

}
