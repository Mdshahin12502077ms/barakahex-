<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Parcel;
use App\Models\Merchant;
use App\Models\ThirdParty;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LiveSearchController extends Controller
{
   
    public function getMerchant(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }

        $hasWarehousesTable = Schema::hasTable('warehouses');
        $with = ['user'];
        if ($hasWarehousesTable) {
            $with[] = 'warehouse';
        }

        $merchants = Merchant::with($with)->where('status', 'active')
            ->when(!hasPermission('use_all_merchant'), function ($query) use ($hasWarehousesTable) {
                $query->whereHas('shops', function ($q) {
                    $q->where('pickup_branch_id', Sentinel::getUser()->branch_id);
                });
                if ($hasWarehousesTable) {
                    $query->whereHas('warehouse', function ($q) {
                        $q->where('merchant_id', Sentinel::getUser()->merchant->id);
                    });
                }
            })
            ->where(function ($query) use ($term) {
                $query->where('company', 'LIKE', '%' . $term . '%')
                    ->orWhereHas('user', function ($q) use ($term) {
                        $q->where('first_name', 'LIKE', '%' . $term . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $term . '%');
                    });
            })
            ->limit(50)->get();

        $formatted_merchants = [];

        foreach ($merchants as $merchant) {
            $formatted_merchants[] = [
                'id' => $merchant->id,
                'text' => $merchant->user->first_name . ' ' . $merchant->user->last_name . ' (' . $merchant->company . ')',
                'balance' => $merchant->balance($merchant->id)
            ];
        }

        return \Response::json($formatted_merchants);
    }

    public function getDeliveryMan(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }

        $delivery_men = DeliveryMan::whereHas('user', function ($inner_query) use ($term) {
            // $inner_query->where('status', true);
            $inner_query->where(function ($query) use ($term) {
                $query->where('first_name', 'LIKE', '%' . $term . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $term . '%');
            });
        })
            ->when(!hasPermission('use_all_delivery_man'), function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('branch_id', Sentinel::getUser()->branch_id)
                        ->orWhere('branch_id', null);
                });
            })->limit(50)->get();

        $formatted_delivery_men = [];

        foreach ($delivery_men as $delivery_man) {
            $formatted_delivery_men[] = [
                'id' => $delivery_man->id,
                'text' => $delivery_man->user->first_name . ' ' . $delivery_man->user->last_name,
                'balance' => $delivery_man->balance($delivery_man->id)
            ];
        }

        // dd($formatted_delivery_men);

        return \Response::json($formatted_delivery_men);
    }

    public function getUser(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }

        $users = User::where('user_type', 'staff')->where('first_name', 'LIKE', '%' . $term . '%')->limit(50)->get();


        $formatted_users = [];

        foreach ($users as $user) {
            $formatted_users[] = ['id' => $user->id, 'text' => $user->first_name . ' ' . $user->last_name];
        }

        return \Response::json($formatted_users);
    }

    public function getParcel(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }

        $parcels = Parcel::where('parcel_no', 'LIKE', '%' . $term . '%')
            ->when(!hasPermission('read_all_parcel'), function ($query) {
                $query->where('branch_id', Sentinel::getUser()->branch_id)
                    ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                    ->orWhereNull('pickup_branch_id')
                    ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
            })
            ->limit(50)->get();


        $formatted_parcels = [];

        foreach ($parcels as $parcel) {
            $formatted_parcels[] = ['id' => $parcel->id, 'text' => $parcel->parcel_no . ' (' . $parcel->merchant->company . ')'];
        }

        return \Response::json($formatted_parcels);
    }

    public function getThirdParty(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }

        $third_parties = ThirdParty::where('status', true)->where('name', 'LIKE', '%' . $term . '%')
            ->limit(20)->get();

        $formatted_third_parties = [];

        foreach ($third_parties as $third_party) {
            $formatted_third_parties[] = ['id' => $third_party->id, 'text' => $third_party->name . ' (' . $third_party->address . ')'];
        }

        return \Response::json($formatted_third_parties);
    }
}
