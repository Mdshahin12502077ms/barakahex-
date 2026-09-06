<?php

namespace App\Repositories;

use Image;
use App\Models\User;
use App\Models\Merchant;
use App\Enums\StatusEnum;
use App\Traits\RepoResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Image as ImageModel;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\ImageTrait;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use App\Repositories\Interfaces\MerchantStaffInterface;

class MerchantStaffRepository implements MerchantStaffInterface{

    use RepoResponseTrait, ApiReturnFormatTrait, ImageTrait;

    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function get($id)
    {
        $user = User::find($id);;
        if ($user->user_type == 'merchant_staff'):
            return $user;
        else:
            return abort(403, 'Access Denied');
        endif;
    }
    public function getMerchant($id)
    {
        return Merchant::find($id);
    }

    public function paginate($merchant)
    {
        return $merchant->staffs()->paginate(\Config::get('parcel.paginate'));
    }


    public function store($request)
    {
        DB::beginTransaction();
        try{
            $merchant = $this->getMerchant($request->merchant);

            $image               = [];
            $image               = $request->image_id;
            if (isset($image)) {
                $response        = $this->saveImage($image ,'image');
                $images          = $response['images'];
            }

            $user = new User();
            $user->first_name    = $request->first_name;
            $user->last_name     = $request->last_name;
            $user->email         = $request->email;
            $user->phone_number  = $request->phone_number;
            $user->password      = bcrypt($request->password);
            $user->permissions   = isset($request->permissions) ? $request->permissions : [];
            $user->image_id      = $image->id ?? null;
            $user->merchant_id   = $merchant->id;
            $user->shops         = $request->shops;
            $user->image_id      = $images ?? '';
            $user->user_type     = 'merchant_staff';
            $user->save();

            $activation = Activation::create($user);
            Activation::complete($user, $activation->code);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            return false;
        }
    }
    public function update($request)
    {
        DB::beginTransaction();
        try{

            $merchant = $this->getMerchant($request->merchant);
            $user     = User::find($request->id);

            $image               = [];
            $image               = $request->image_id;

            if (isset($image)) {
                $response        = $this->saveImage($image ,'image');
                $images          = $response['images'];
            }

            $user->first_name    = $request->first_name;
            $user->last_name     = $request->last_name;
            $user->email         = $request->email;
            $user->phone_number  = $request->phone_number;
            $user->shops         = $request->shops ?? [];
            $user->merchant_id   = $merchant->id;
            $user->image_id      = $images ?? $user->image_id;
            if($request->password != ""):
                $user->password  = bcrypt($request->password);
            endif;
            $user->permissions   = isset($request->permissions) ? $request->permissions : [];

            $user->save();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function statusChange($request)
    {
        try {
            $row = $this->model->find($request->id);
            if ($row->status == StatusEnum::ACTIVE) {
                $row->status = StatusEnum::INACTIVE;
            } elseif ($row->status == StatusEnum::INACTIVE) {
                $row->status = StatusEnum::ACTIVE;
            }
            $row->save();

            return $this->responseWithSuccess(__('updated_successfully'), []);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), []);
        }
    }



}
