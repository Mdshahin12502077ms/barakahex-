<?php

namespace App\Http\Controllers\MerchantStaff;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Parcel;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Merchant\NewParcelService;
use App\Services\Merchant\ProcessingParcelService;
use App\Services\Merchant\DeliveredParcelService;
use App\Repositories\Interfaces\Merchant\MerchantInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class DashboardController extends Controller
{

    protected $merchants;

    public function __construct(MerchantInterface $merchants)
    {
        $this->merchants = $merchants;
    }

    public function index(Request $request)
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $last7days = date('Y-m-d', strtotime('-7 days'));
        $last14days = date('Y-m-d', strtotime('-14 days'));
        $lastMonthFirstDay = date('Y-m-01', strtotime('last month'));
        $lastMonthLastDay = date('Y-m-t', strtotime('last month'));
        $last6MonthsFirstDay = date('Y-m-01', strtotime('-6 months'));
        $last6MonthsLastDay = date('Y-m-t', strtotime('-6 months'));
        $last12MonthsFirstDay = date('Y-m-01', strtotime('-12 months'));
        $last12MonthsLastDay = date('Y-m-t', strtotime('-12 months'));
        $start_date_one_month_ago = date('Y-m-d', strtotime('-1 month'));
        $start_date_one_year_ago = date('Y-m-d', strtotime('-1 year'));
        $now = Carbon::now();
        $start_date = '2000-04-01';
        $end_date = date('Y-m-d');
        $filter = $request->input('filter');
        $custom_start_date = $request['startDate'];
        $custom_end_date = $request->input('endDate');
        switch ($filter) {
            case 'yesterday':
                $filter_start_date = $yesterday;
                $filter_end_date = $today;
                break;
            case 'last_7_day':
                $filter_start_date = $last7days;
                $filter_end_date = $today;
                break;
            case 'last_14_day':
                $filter_start_date = $last14days;
                $filter_end_date = $today;
                break;
            case 'last_month':
                $filter_start_date = $lastMonthFirstDay;
                $filter_end_date = $lastMonthLastDay;
                break;
            case 'last_6_month':
                $filter_start_date = $last6MonthsFirstDay;
                $filter_end_date = $last6MonthsLastDay;
                break;
            case 'this_year':
                $filter_start_date = $start_date_one_year_ago;
                $filter_end_date = $today;
                break;
            case 'last_12_month':
                $filter_start_date = $last12MonthsFirstDay;
                $filter_end_date = $last12MonthsLastDay;
                break;
            case 'custom':
                $filter_start_date = $custom_start_date;
                $filter_end_date = $custom_end_date;
                break;
            default:
                $filter_start_date = $today;
                $filter_end_date = $today;
                break;
        }


        //parcel report
        $new_parcel = $this->newParcel($filter_start_date, $filter_end_date);
        $new_parcel_total = $new_parcel->get();
        $new_parcel_cod = $new_parcel_total->sum('price');
        $processing_parcel = $this->processedParcel($filter_start_date, $filter_end_date);
        $processing_parcel_total = $processing_parcel->get();
        $processing_parcel_cod = $processing_parcel_total->sum('price');
        $delivered_parcel = $this->deliveredParcel($filter_start_date, $filter_end_date);
        $delivered_parcel_total = $delivered_parcel->get();
        $delivered_parcel_cod = $delivered_parcel_total->sum('price');

        // $life_time_total_parcel                             = $this->newParcel($start_date, $end_date)


        //parcel
        $total_parcel = $this->totalParcel($start_date, $end_date);
        $total_parcel_count = $total_parcel->count();
        //dd(Sentinel::getUser()->shops);
        $total_shop = Shop::whereIn('id', \Sentinel::getUser()->shops ?? [])->count();
        $total_staff = User::where('merchant_id', \Sentinel::getUser()->merchant_id)->where('user_type', 'merchant_staff')->count();

        $total_delivered_parcel = $this->deliveredParcel($start_date, $end_date);
        $total_delivered_parcel_count = $total_delivered_parcel->count();
        $total_cod = $total_delivered_parcel->sum('price');
        $total_return_parcel = $this->returnParcel($start_date, $end_date)->count();

        $delivery_ratio = 0;
        $return_ratio = 0;

        if ($total_parcel_count > 0) {
            $delivery_ratio = round(($total_delivered_parcel->count() * 100 / $total_parcel_count), 2);
            $return_ratio = round(($total_return_parcel * 100 / $total_parcel_count), 2);
        } else {
            $delivery_ratio = 0;
            $return_ratio = 0;
        }

        $delivery_ratio_readable = number_format($delivery_ratio, 2) . '%';
        $return_ratio_readable = number_format($return_ratio, 2) . '%';


        $current_time = Carbon::now()->format('Y-m-d H:i:s');
        $notices = Notice::where('status', true)->where('merchant', true)->where('start_time', '<=', $current_time)->where('end_time', '>=', $current_time)->get();


        $data = [
            'charts' => [
                //parcel report
                'new_parcel' => app(NewParcelService::class)->totalParcel($new_parcel),
                'processing_parcel' => app(ProcessingParcelService::class)->totalParcel($processing_parcel),
                'delivered_parcel' => app(DeliveredParcelService::class)->totalParcel($delivered_parcel),

                //parcel statistic
                'total_parcel' => app(NewParcelService::class)->totalParcel($total_parcel),
                'total_delivered_parcel' => app(DeliveredParcelService::class)->totalParcel($delivered_parcel),

            ],

            'notices' => $notices,

            //parcel report
            'new_parcel' => $new_parcel_total->count(),
            'new_parcel_cod' => $new_parcel_cod,
            'processing_parcel' => $processing_parcel_total->count(),
            'processing_parcel_cod' => $processing_parcel_cod,
            'delivered_parcel_cod' => $delivered_parcel_cod,
            'delivered_parcel' => $delivered_parcel_total->count(),


            //parcel overview
            'total_parcel_count' => $total_delivered_parcel_count,
            'total_cod' => $total_cod,
            'total_shop' => $total_shop,
            'total_staff' => $total_staff,
            'delivery_ratio' => $delivery_ratio_readable,
            'return_ratio' => $return_ratio_readable,
            'latest_parcels' => $this->latestParcel(),

        ];

        if ($request->ajax()) {
            return response()->json(['data' => $data]);
        } else {
            return view('merchant-staff.dashboard', $data);

        }

    }

    public function totalParcel($start_date, $end_date)
    {
        $data = Parcel::where('merchant_id', \Sentinel::getUser()->merchant_id)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->when(!hasPermission('all_parcel'), function ($query) {
                return $query->whereHas('shop', function ($q) {
                    $q->whereIn('id', \Sentinel::getUser()->shops);
                });
            });

        return $data;
    }

    public function processedParcel($start_date, $end_date)
    {
        $data = Parcel::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->where('merchant_id', \Sentinel::getUser()->merchant_id)
            ->whereNotIn('status', ['pending', 'delivered', 'returned-to-merchant', 'partially-delivered', 'deleted'])
            ->when(!hasPermission('all_parcel'), function ($query) {
                return $query->whereHas('shop', function ($q) {
                    $q->whereIn('id', \Sentinel::getUser()->shops);
                });
            });

        return $data;
    }

    public function newParcel($start_date, $end_date)
    {
        $data = Parcel::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->where('status', 'pending')
            ->where('merchant_id', \Sentinel::getUser()->merchant_id)
            ->when(!hasPermission('all_parcel'), function ($query) {
                return $query->whereHas('shop', function ($q) {
                    $q->whereIn('id', \Sentinel::getUser()->shops);
                });
            });

        return $data;
    }

    public function deliveredParcel($start_date, $end_date)
    {
        $data = Parcel::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->where('status', 'delivered')
            ->where('merchant_id', \Sentinel::getUser()->merchant_id)
            ->when(!hasPermission('all_parcel'), function ($query) {
                return $query->whereHas('shop', function ($q) {
                    $q->whereIn('id', \Sentinel::getUser()->shops);
                });
            });


        return $data;
    }

    public function returnParcel($start_date, $end_date)
    {
        $data = Parcel::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->where('status', 'returned-to-merchant')
            ->where('merchant_id', \Sentinel::getUser()->merchant_id)
            ->when(!hasPermission('all_parcel'), function ($query) {
                return $query->whereHas('shop', function ($q) {
                    $q->whereIn('id', \Sentinel::getUser()->shops);
                });
            });

        return $data;
    }

    public function latestParcel()
    {
        $data = Parcel::where('merchant_id', \Sentinel::getUser()->merchant_id)
            ->when(!hasPermission('all_parcel'), function ($query) {
                return $query->whereHas('shop', function ($q) {
                    $q->whereIn('id', \Sentinel::getUser()->shops);
                });
            })->take(5)->get();

        return $data;
    }

}
