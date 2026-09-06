<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Account\DeliveryManAccount;
use App\Models\Merchant;
use App\Models\Shop;
use App\Models\User;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Request;
use App\Http\Resources\Api\StaffResource;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Log;
use App\Traits\SendMailTrait;
use App\Repositories\Interfaces\MerchantStaffInterface;
use App\Http\Resources\Api\Profile;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class StaffController extends Controller
{
    use ApiReturnFormatTrait;

    protected $staffRepo;


    public function __construct(MerchantStaffInterface $staffRepo)
    {

        $this->staffRepo     = $staffRepo;

    }
    public function allStaff(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = jwtUser();

            if ($user->user_type == 'merchant') {
                $merchant = Merchant::where('user_id', $user->id)->first();
            } elseif ($user->user_type == 'merchant_staff') {
                $merchant = Merchant::where('id', $user->merchant_id)->first();
            } else {
                return $this->responseWithError('Invalid user type');
            }
            $staff = $merchant->staffs()->paginate(10);

            $data = [
                'staff'              => StaffResource::collection($staff),
                'paginate' => [
                    'total'         => $staff->total(),
                    'current_page'  => $staff->currentPage(),
                    'per_page'      => $staff->perPage(),
                    'last_page'     => $staff->lastPage(),
                    'prev_page_url' => $staff->previousPageUrl(),
                    'next_page_url' => $staff->nextPageUrl(),
                    'path'          => $staff->path(),
                ],
            ];

            return $this->responseWithSuccess('staff_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function submitStaff(Request $request, $id = null): \Illuminate\Http\JsonResponse
    {

        try {
          $validator = Validator::make($request->all(), [
              'first_name'   => 'required|max:50',
              'last_name'    => 'required|max:50',
              'email'        => 'required|email|unique:users,email,' . $id,
              'phone_number' => 'required',
          ]);

          if ($validator->fails()) {
              return $this->responseWithError(__('required_field_missing'), $validator->errors(), 422);
          }

          $data                     = $request->input('shops');
          $cleanedData              = str_replace(['[', ']', ' ', '"'], '', $data[0]);
          $numbers                  = explode(',', $cleanedData);
          $convertedData            = array_filter($numbers, function($number) {
              return (int)$number > 0;
          });

          $convertedData            = array_map('strval', array_values($convertedData));

          $request->merge(['shops' => $convertedData]);


        $permissions               = $request->input('permissions');


        if (is_array($permissions)) {
            $permissions  = implode('', $permissions);
        }


        $cleanedPermissions = str_replace(['[', ']'], '', $permissions);


        $permissionsArray   = explode(',', $cleanedPermissions);

        $permissionsArray   = array_map('trim', $permissionsArray);

        $request->merge(['permissions' => $permissionsArray]);

          $user                     = jwtUser();

          if ($user->user_type == 'merchant_staff') {
              $request['merchant']  = $user->merchant_id;
          } elseif ($user->user_type == 'merchant') {
              $request['merchant']  = $user->merchant->id;
          } else {
              return $this->responseWithError('Invalid user type');
          }

          if ($id) {
              $users                = User::findOrFail($id);
              $request['id']        = $users->id;
              $this->staffRepo->update($request);
              return $this->responseWithSuccess('Staff updated successfully');
          } else {
              $this->staffRepo->store($request);
              return $this->responseWithSuccess('Staff stored successfully');
          }

      } catch (\Exception $e) {
          return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
      }
    }

    public function editStaff($id)
    {
        try {
            $staff                  = User::where('id', $id)->get();

            $data = [
                'staff'              => StaffResource::collection($staff),
            ];
            return $this->responseWithSuccess('staff_edit_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

}
