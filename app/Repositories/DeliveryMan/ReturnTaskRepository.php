<?php

namespace App\Repositories\DeliveryMan;

use App\Models\DeliveryMan;
use App\Models\Parcel;
use App\Models\ParcelEvent;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\ImageTrait;
use App\Traits\RepoResponseTrait;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\DB;

class ReturnTaskRepository
{
    use RepoResponseTrait, ApiReturnFormatTrait, ImageTrait;

    protected $model;

    public function __construct(Parcel $model)
    {
        $this->model = $model;
    }

    public function complete($id)
    {
        $user = Sentinel::getUser();
        $deliveryMan = $user ? ($user->deliveryMan ?? DeliveryMan::where('user_id', $user->id)->first()) : null;
        $deliveryManId = $deliveryMan ? $deliveryMan->id : 0;

        $parcel = $this->model->where('id', $id)
            ->where('return_delivery_man_id', $deliveryManId)
            ->first();

        if (!$parcel) {
            return [
                'success' => false,
                'message' => __('parcel_not_found'),
            ];
        }

        if ($parcel->status == 'returned-to-merchant') {
            return [
                'success' => false,
                'message' => __('this_parcel_has_already_been_returned'),
            ];
        }

        DB::beginTransaction();
        try {
            $parcel->status = 'returned-to-merchant';
            $parcel->returned_date = Carbon::now();
            $parcel->save();

            $event = new ParcelEvent();
            $event->parcel_id = $parcel->id;
            $event->title = 'parcel_return_to_merchant_event';
            $event->return_delivery_man_id = $deliveryManId;
            $event->cancel_note = null;
            $event->user_id = $user ? $user->id : null;
            $event->created_by = $user ? $user->id : null;
            $event->save();

            // Trigger Account/Financial Entries for Return (Merchant charge, Rider return fee, Company expense, VAT)
            try {
                $accountRepo = app(\App\Repositories\Interfaces\AccountInterface::class);
                $accountRepo->parcelStatusUpdate($parcel->id, 'returned-to-merchant');
            } catch (\Throwable $accEx) {
                // Keep event saved even if account rule fails
            }

            DB::commit();
            return [
                'success' => true,
                'message' => __('parcel_returned_to_merchant_successfully'),
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => __('something_went_wrong_please_try_again'),
            ];
        }
    }
}
