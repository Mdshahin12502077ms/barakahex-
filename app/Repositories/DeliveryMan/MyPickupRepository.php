<?php

namespace App\Repositories\DeliveryMan;

use App\Models\Parcel;
use App\Models\ParcelEvent;
use App\Models\DeliveryMan;
use App\Traits\RepoResponseTrait;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\SmsSenderTrait;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\DB;

class MyPickupRepository
{
    use RepoResponseTrait, ApiReturnFormatTrait, SmsSenderTrait;

    protected $model;

    public function __construct(Parcel $model)
    {
        $this->model = $model;
    }

    private function getDeliveryMan()
    {
        $user = Sentinel::getUser();
        if (!$user) {
            return null;
        }
        return $user->deliveryMan ?? DeliveryMan::where('user_id', $user->id)->first();
    }

    public function all($request = null, $paginate = 15)
    {
        $deliveryMan = $this->getDeliveryMan();
        if (!$deliveryMan) {
            return collect();
        }

        $query = $this->model->with(['merchant.user', 'shop'])
            ->where('pickup_man_id', $deliveryMan->id);

        if ($request && $request->has('status') && !empty($request->status) && $request->status != 'all') {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', [
                'pickup_assigned',
                'pickup-assigned',
                'pickup_re_schedule',
                're-schedule-pickup',
                'pickup_received',
                'received-by-pickup-man'
            ]);
        }

        if ($request && $request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('parcel_no', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone_number', 'LIKE', "%{$search}%");
            });
        }

        return $query->latest('id')->paginate($paginate);
    }

    public function find($id)
    {
        $deliveryMan = $this->getDeliveryMan();
        $deliveryManId = $deliveryMan->id ?? 0;
        return $this->model->with(['merchant.user', 'shop', 'events'])
            ->where(function ($q) use ($deliveryManId) {
                $q->where('pickup_man_id', $deliveryManId)
                  ->orWhere('delivery_man_id', $deliveryManId);
            })
            ->findOrFail($id);
    }

    public function pickupReceived($id, $request = null)
    {
        DB::beginTransaction();
        try {
            $deliveryMan = $this->getDeliveryMan();
            $deliveryManId = $deliveryMan->id ?? 0;

            $parcel = $this->model->where('id', $id)
                ->where(function ($q) use ($deliveryManId) {
                    $q->where('pickup_man_id', $deliveryManId)
                      ->orWhere('delivery_man_id', $deliveryManId);
                })
                ->firstOrFail();

            if (empty($parcel->pickup_man_id)) {
                $parcel->pickup_man_id = $deliveryManId;
            }

            $parcel->status = 'received-by-pickup-man';
            $parcel->date = date('Y-m-d');
            $parcel->save();

            // Log event
            $note = ($request && $request->note) ? $request->note : 'Pickup received by delivery man';
            $this->parcelEvent($parcel->id, 'parcel_received_by_pickup_man_event', '', $deliveryManId, '', $note);

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function reschedulePickup($id, $request)
    {
        DB::beginTransaction();
        try {
            $deliveryMan = $this->getDeliveryMan();
            $deliveryManId = $deliveryMan->id ?? 0;

            $parcel = $this->model->where('id', $id)
                ->where(function ($q) use ($deliveryManId) {
                    $q->where('pickup_man_id', $deliveryManId)
                      ->orWhere('delivery_man_id', $deliveryManId);
                })
                ->firstOrFail();

            $parcel->status = 're-schedule-pickup';
            if ($request && $request->pickup_date) {
                $parcel->pickup_date = $request->pickup_date;
            }
            $parcel->save();

            // Log event
            $note = ($request && $request->note) ? $request->note : 'Pickup rescheduled by delivery man';
            $this->parcelEvent($parcel->id, 'parcel_re_schedule_pickup_event', '', $deliveryManId, '', $note);

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function cancelPickup($id, $request)
    {
        DB::beginTransaction();
        try {
            $deliveryMan = $this->getDeliveryMan();
            $deliveryManId = $deliveryMan->id ?? 0;

            $parcel = $this->model->where('id', $id)
                ->where(function ($q) use ($deliveryManId) {
                    $q->where('pickup_man_id', $deliveryManId)
                      ->orWhere('delivery_man_id', $deliveryManId);
                })
                ->firstOrFail();

            $parcel->status = 'cancel';
            $parcel->save();

            // Log event
            $note = ($request && $request->note) ? $request->note : 'Pickup cancelled by delivery man';
            $this->parcelEvent($parcel->id, 'parcel_cancel_event', '', $deliveryManId, '', $note);

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function statistics()
    {
        $deliveryMan = $this->getDeliveryMan();
        if (!$deliveryMan) {
            return [
                'total'      => 0,
                'pending'    => 0,
                'completed'  => 0,
                'reschedule' => 0,
            ];
        }

        $base = $this->model->where('pickup_man_id', $deliveryMan->id);

        return [
            'total'      => (clone $base)->count(),
            'pending'    => (clone $base)->whereIn('status', ['pickup_assigned', 'pickup-assigned'])->count(),
            'completed'  => (clone $base)->whereIn('status', ['pickup_received', 'received-by-pickup-man'])->count(),
            'reschedule' => (clone $base)->whereIn('status', ['pickup_re_schedule', 're-schedule-pickup'])->count(),
        ];
    }

    private function parcelEvent($parcel_id, $title, $delivery_man = '', $pickup_man = '', $return_delivery_man = '', $note = '')
    {
        $event = new ParcelEvent();
        $event->parcel_id = $parcel_id;
        $event->title = $title;
        $event->delivery_man_id = $delivery_man ?: null;
        $event->pickup_man_id = $pickup_man ?: null;
        $event->return_delivery_man_id = $return_delivery_man ?: null;
        $event->cancel_note = $note;
        $event->user_id = Sentinel::check() ? Sentinel::getUser()->id : null;
        $event->created_by = Sentinel::check() ? Sentinel::getUser()->id : null;
        $event->save();
        return $event;
    }
}
