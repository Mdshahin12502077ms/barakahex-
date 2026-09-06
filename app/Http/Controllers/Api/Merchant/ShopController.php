<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Account\DeliveryManAccount;
use App\Models\Merchant;
use App\Models\Shop;
use App\Models\Branch;
use App\Models\User;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Request;
use App\Http\Resources\Api\ShopResource;
use App\Http\Resources\Api\BranchResource;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Traits\SendMailTrait;
use App\Repositories\Interfaces\Merchant\MerchantInterface;
use App\Http\Resources\Api\Profile;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class ShopController extends Controller
{
    use ApiReturnFormatTrait;

    protected $merchantRepo;


    public function __construct(MerchantInterface $merchantRepo)
    {

        $this->merchantRepo     = $merchantRepo;

    }
    public function allShop(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = jwtUser();

            if ($user->user_type == 'merchant') {
                $merchant_id = $user->merchant->id;
                $shop = Shop::where('merchant_id', $merchant_id)
                                ->where('status', 1)
                                ->latest()
                                ->paginate(10);
            } elseif ($user->user_type == 'merchant_staff') {
                $shop = Shop::whereIn('id', $user->shops)
                           ->where('status', 1)
                           ->latest()
                           ->paginate(10);
            }else{
                return $this->responseWithError('Invalid user type');
            }

            $data = [
                'shop'              => ShopResource::collection($shop),
                'paginate' => [
                    'total'         => $shop->total(),
                    'current_page'  => $shop->currentPage(),
                    'per_page'      => $shop->perPage(),
                    'last_page'     => $shop->lastPage(),
                    'prev_page_url' => $shop->previousPageUrl(),
                    'next_page_url' => $shop->nextPageUrl(),
                    'path'          => $shop->path(),
                ],
            ];

            return $this->responseWithSuccess('shop_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function submitShop(Request $request, $id = null): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'shop_name'         => 'required|unique:shops,shop_name,' . $id,
            'shop_phone_number' => 'required',
            'contact_number'    => 'required',
            'address'           => 'required',
        ]);

        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {

            $user                 = jwtUser();
            $request['merchant']  = $user->merchant->id ?? $user->merchant_id;

            if ($id) {
                $shops           = Shop::findOrFail($id);
                $request['shop'] = $shops->id;
                if (!$request['shop']) {
                    return $this->responseWithError('Shop not found.');
                }
                $this->merchantRepo->shopUpdate($request);
                return $this->responseWithSuccess('Shop updated successfully');
            } else {
                $this->merchantRepo->shopStore($request);
                return $this->responseWithSuccess('Shop stored successfully');

            }

        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }


    public function allBranch()
    {
        try {
            $user = jwtUser();

            $branchs       = Branch::active()->get();

            $data = [
                'branch'              => BranchResource::collection($branchs),

            ];

            return $this->responseWithSuccess('branch_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

}
