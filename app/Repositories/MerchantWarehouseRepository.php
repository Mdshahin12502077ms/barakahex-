<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Merchant;
use App\Enums\StatusEnum;
use App\Models\Warehouse;
use App\Repositories\Interfaces\MerchantWarehouseInterface;
use App\Traits\RepoResponseTrait;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\DB;
use App\Models\Image as ImageModel;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\ImageTrait;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use App\Repositories\Interfaces\MerchantStaffInterface;

class MerchantWarehouseRepository implements MerchantWarehouseInterface{

    use RepoResponseTrait, ApiReturnFormatTrait, ImageTrait;

    private $model;

    public function __construct(Warehouse $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        $warehouse = $this->model->where('status',\App\Enums\StatusEnum::ACTIVE)->get();
        return $warehouse;
    }

    public function merchantWarehouse()
    {
        $warehouse = $this->model->where('status',\App\Enums\StatusEnum::ACTIVE)->where('merchant_id',Sentinel::getUser()->merchant->id)->get();
        return $warehouse;
    }


    public function get($id)
    {
        $warehouse = Warehouse::find($id);;
        return $warehouse;

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
            $warehouse = new Warehouse();
            $warehouse->name    = $request->warehouse_name;
            $warehouse->email         = $request->email;
            $warehouse->phone_number  = $request->phone_number;
            $warehouse->merchant_id   = $merchant->id;
            $warehouse->address   = $request->address;
            $warehouse->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    public function update($request)
    {
        DB::beginTransaction();
        try{
            $merchant = $this->getMerchant($request->merchant);

            $warehouse                = $this->get($request->id);
            $warehouse->name          = $request->warehouse_name;
            $warehouse->email         = $request->email;
            $warehouse->phone_number  = $request->phone_number;
            $warehouse->merchant_id   = $merchant->id;
            $warehouse->address       = $request->address;
            $warehouse->save();

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

    public function delete($id)
    {
        DB::beginTransaction();
        try{
            $warehouse = $this->get($id);
            $warehouse->delete();
            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

}
