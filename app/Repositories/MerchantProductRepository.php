<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Merchant;
use App\Enums\StatusEnum;
use App\Repositories\Interfaces\MerchantProductInterface;
use App\Traits\RepoResponseTrait;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\ImageTrait;
use Cartalyst\Sentinel\Laravel\Facades\Activation;

class MerchantProductRepository implements MerchantProductInterface{

    use RepoResponseTrait, ApiReturnFormatTrait, ImageTrait;

    private $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        $product = $this->model->where('status',\App\Enums\StatusEnum::ACTIVE)->get();
        return $product;
    }

    public function merchantProduct()
    {
        $product = $this->model->where('status',\App\Enums\StatusEnum::ACTIVE)->where('merchant_id',Sentinel::getUser()->merchant->id )->get();
        return $product;
    }

    public function get($id)
    {
        $product = Product::find($id);;
        return $product;

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
            $product = new Product();
            $product->name          = $request->name;
            $product->merchant_id   = $merchant->id;
            $product->description   = $request->description;
            $product->save();
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
            $product                = $this->get($request->id);
            $product->name          = $request->name;
            $product->merchant_id   = $request->merchant;
            $product->description   = $request->description;
            $product->save();

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
            $product = $this->get($id);
            $product->delete();
            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

}
