<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Merchant;
use App\Enums\StatusEnum;
use App\Models\Stock;
use App\Repositories\Interfaces\MerchantProductInterface;
use App\Traits\RepoResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\ImageTrait;
use Cartalyst\Sentinel\Laravel\Facades\Activation;

class StockRepository
{

    use RepoResponseTrait, ApiReturnFormatTrait, ImageTrait;

    private $model;

    public function __construct(Stock $model)
    {
        $this->model = $model;
    }

    public function get($id)
    {
        $product = Stock::find($id);;
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
            $image = $request->image;
            $images = '';

            if ($image) {
                $response = $this->saveImage($image, 'image');
                $images = $response['images'];
            }

            $stock = new Stock();
            $stock->product_id       = $request->product;
            $stock->warehouse_id     = $request->warehouse;
            $stock->quantity         = $request->quantity;
            $stock->type             = 1;
            $stock->images           = $images;
            $stock->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    public function update($request,$stock)
    {
        DB::beginTransaction();
        try{
            $stock->quantity          = $stock->quantity + $request->quantity;
            $stock->save();

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
