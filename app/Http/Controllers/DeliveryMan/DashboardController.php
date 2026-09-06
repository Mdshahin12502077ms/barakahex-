<?php

namespace App\Http\Controllers\DeliveryMan;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use App\Models\Notice;
use App\Models\DeliveryMan;
use App\Models\Account\DeliveryManAccount;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Sentinel::getUser();
        $deliveryMan = DeliveryMan::where('user_id', $user->id)->first();

        if (!$deliveryMan) {
            return redirect()->route('login')->with('danger', __('delivery_man_not_found'));
        }

        $deliveryManId = $deliveryMan->id;

        // Statistics
        $today = date('Y-m-d');

        // Pickups
        $total_pickups       = Parcel::where('pickup_man_id', $deliveryManId)->count();
        $pending_pickups     = Parcel::where('pickup_man_id', $deliveryManId)->whereIn('status', ['pickup_assigned', 'pickup-assigned', 'pickup_re_schedule', 're-schedule-pickup'])->count();
        $completed_pickups   = Parcel::where('pickup_man_id', $deliveryManId)->whereIn('status', ['pickup_received', 'received-by-pickup-man'])->count();

        // Deliveries
        $total_deliveries     = Parcel::where('delivery_man_id', $deliveryManId)->count();
        $pending_deliveries   = Parcel::where('delivery_man_id', $deliveryManId)->whereIn('status', ['delivery_assigned', 'delivery-assigned', 're_schedule_delivery', 're-schedule-delivery'])->count();
        $processing_deliveries = Parcel::where('delivery_man_id', $deliveryManId)->whereIn('status', ['processing', 'out-for-delivery'])->count();
        $completed_deliveries = Parcel::where('delivery_man_id', $deliveryManId)->whereIn('status', ['delivered', 'delivered-and-verified', 'partially-delivered'])->count();
        $cancelled_deliveries = Parcel::where('delivery_man_id', $deliveryManId)->whereIn('status', ['cancel', 'cancelled', 'delivery_cancelled'])->count();

        // Today's Stats
        $today_deliveries     = Parcel::where('delivery_man_id', $deliveryManId)->whereDate('updated_at', $today)->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
        $today_pickups        = Parcel::where('pickup_man_id', $deliveryManId)->whereDate('updated_at', $today)->whereIn('status', ['pickup_received', 'received-by-pickup-man'])->count();

        // Financials (COD in hand & Earnings)
        $cash_in_hand = DeliveryManAccount::where('delivery_man_id', $deliveryManId)
            ->where('source', 'parcel_delivery')
            ->sum('amount');

        $total_earnings = DeliveryManAccount::where('delivery_man_id', $deliveryManId)
            ->whereIn('source', ['delivery_commission', 'pickup_commission'])
            ->sum('amount');

        $today_earnings = DeliveryManAccount::where('delivery_man_id', $deliveryManId)
            ->whereIn('source', ['delivery_commission', 'pickup_commission'])
            ->whereDate('created_at', $today)
            ->sum('amount');

        // Recent Today's Assigned Deliveries
        $recent_deliveries = Parcel::with(['merchant.user', 'shop'])
            ->where('delivery_man_id', $deliveryManId)
            ->latest()
            ->take(10)
            ->get();

        // Active Notices
        $notices = Notice::active()->latest()->take(3)->get();

        $data = [
            'user'                  => $user,
            'deliveryMan'           => $deliveryMan,
            'total_pickups'         => $total_pickups,
            'pending_pickups'       => $pending_pickups,
            'completed_pickups'     => $completed_pickups,
            'total_deliveries'      => $total_deliveries,
            'pending_deliveries'    => $pending_deliveries,
            'processing_deliveries' => $processing_deliveries,
            'completed_deliveries'  => $completed_deliveries,
            'cancelled_deliveries'  => $cancelled_deliveries,
            'today_deliveries'      => $today_deliveries,
            'today_pickups'         => $today_pickups,
            'cash_in_hand'          => $cash_in_hand,
            'total_earnings'        => $total_earnings,
            'today_earnings'        => $today_earnings,
            'recent_deliveries'     => $recent_deliveries,
            'notices'               => $notices,
        ];

        return view('deliveryman.dashboard', $data);
    }
}
