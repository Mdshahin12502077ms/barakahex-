<?php

namespace App\Repositories\DeliveryMan;

use App\Models\Account\DeliveryManAccount;
use App\Models\DeliveryMan;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\RepoResponseTrait;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\DB;

class MyAccountRepository
{
    use RepoResponseTrait, ApiReturnFormatTrait;

    protected $model;

    public function __construct(DeliveryManAccount $model)
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

    public function summary()
    {
        $deliveryMan = $this->getDeliveryMan();
        if (!$deliveryMan) {
            return [
                'cash_in_hand'     => 0,
                'total_collected'  => 0,
                'total_deposited'  => 0,
                'total_earnings'   => 0,
            ];
        }

        // Cash in hand calculated via DeliveryMan model formula
        $cashInHand = $deliveryMan->balance($deliveryMan->id);

        $totalCollected = $this->model->where('delivery_man_id', $deliveryMan->id)
            ->where('source', 'cash_collection')
            ->sum('amount');

        $totalDeposited = $this->model->where('delivery_man_id', $deliveryMan->id)
            ->where('source', 'cash_given_to_staff')
            ->sum('amount');

        $totalEarnings = $this->model->where('delivery_man_id', $deliveryMan->id)
            ->whereIn('source', ['pickup_commission', 'parcel_delivery', 'delivery_commission', 'commission'])
            ->sum('amount');

        return [
            'cash_in_hand'     => max(0, $cashInHand),
            'total_collected'  => $totalCollected,
            'total_deposited'  => $totalDeposited,
            'total_earnings'   => $totalEarnings,
        ];
    }

    public function statements($request = null, $paginate = 15)
    {
        $deliveryMan = $this->getDeliveryMan();
        if (!$deliveryMan) {
            return collect();
        }

        $query = $this->model->with(['parcel.merchant', 'companyAccount'])
            ->where('delivery_man_id', $deliveryMan->id);

        if ($request && $request->has('source') && !empty($request->source) && $request->source != 'all') {
            if ($request->source == 'collection') {
                $query->where('source', 'cash_collection');
            } elseif ($request->source == 'deposit') {
                $query->where('source', 'cash_given_to_staff');
            } elseif ($request->source == 'commission' || $request->source == 'earning') {
                $query->whereIn('source', ['pickup_commission', 'parcel_delivery', 'delivery_commission', 'commission']);
            } else {
                $query->where('source', $request->source);
            }
        }

        if ($request && $request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request && $request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        if ($request && $request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('details', 'LIKE', "%{$search}%")
                  ->orWhere('source', 'LIKE', "%{$search}%")
                  ->orWhereHas('parcel', function ($sub) use ($search) {
                      $sub->where('parcel_no', 'LIKE', "%{$search}%");
                  });
            });
        }

        return $query->latest('id')->paginate($paginate);
    }
}
