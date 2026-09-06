<?php
namespace App\Http\Controllers\Admin;
use Carbon\Carbon;
use App\Models\Notice;
use App\Models\Parcel;
use App\Models\User;
use App\Models\Merchant;
use App\Models\Account\FundTransfer;
use App\Models\ParcelEvent;
use Illuminate\Http\Request;
use App\Models\Account\Account;
use App\Models\Account\GovtVat;
use App\Traits\ShortenLinkTrait;
use App\Traits\RandomStringTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Account\CompanyAccount;
use App\Models\StaffAccount;
use App\Models\Account\MerchantAccount;
use Illuminate\Support\Facades\Artisan;
use App\Models\Account\MerchantWithdraw;
use App\Models\Account\DeliveryManAccount;
use App\Models\DeliveryMan;
use App\Services\CodService;
use App\Models\Branch;
use App\Services\MerchantService;
use App\Services\FinanceReportService;
use App\Services\CashFromMerchantService;
use App\Services\ChargesService;
use App\Services\ParcelService;
use App\Services\FilteredParcelService;
use App\Services\DeliverymanService;
use App\Services\BranchService;
use App\Repositories\Interfaces\Merchant\MerchantInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class DashboardController extends Controller
{
    use RandomStringTrait, ShortenLinkTrait;

    protected $merchants;

    public function __construct(MerchantInterface $merchants)
    {
        $this->merchants = $merchants;
    }

    public function index(Request $request)
    {
        $today = date('Y-m-d');
        $start_date_one_month_ago = date('Y-m-d', strtotime('-1 month'));
        $start_date = '2000-04-01' . ' 00:00:00';
        $end_date = date('Y-m-d') . ' 23:59:59';

        $filter_start_date = $today . ' 00:00:00';
        $filter_end_date = $today . ' 23:59:59';

        $last12MonthsFirstDay = date('Y-m-01', strtotime('-11 months'));
        $last12MonthsLastDay = date('Y-m-t');

        //for all dashboards
        $current_time = Carbon::now()->format('Y-m-d H:i:s');

        $notices = Notice::where('status', true)->where('staff', true)
            ->where('start_time', '<=', $current_time)->where('end_time', '>=', $current_time)->get();


        if (@Sentinel::getUser()->dashboard == 'admin') {
            //merchant
            $merchantsCounts = $this->totalMerchant($start_date, $end_date) // or earliest start date
                ->selectRaw("
                        COUNT(*) as total_merchants,
                        SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as last_30_days
                    ", [$start_date_one_month_ago . ' 00:00:00', $end_date . ' 23:59:59'])
                ->first();

            //parcel
            $parcelstats = Parcel::withPermission()
                ->selectRaw("
                        COUNT(*) as total_parcel_count,
                        SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as last_month_total_parcel_count,
                        SUM(CASE WHEN status IN ('partially-delivered','delivered') THEN price ELSE 0 END) as total_cod,
                        SUM(CASE WHEN created_at BETWEEN ? AND ? AND status IN ('partially-delivered','delivered') THEN price ELSE 0 END) as last_month_total_cod
                    ", [
                    $start_date_one_month_ago . ' 00:00:00',
                    $end_date . ' 23:59:59',
                    $start_date_one_month_ago . ' 00:00:00',
                    $end_date . ' 23:59:59',
                ])
                ->first();

            //parcel overview table
            $stattusWiseParcelsCounts = $this->totalParcel($start_date . ' 00:00:00', $end_date . ' 23:59:59')
                ->select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $known_statuses = ['delivered', 'partially-delivered', 'pending', 'returned-to-merchant', 'cancle', 'deleted'];

            // calculate 'other' counts
            $other_total = array_sum(
                array_diff_key($stattusWiseParcelsCounts, array_flip($known_statuses))
            );

            // remove other statuses from main array (optional)
            $stattusWiseParcelsCounts = array_intersect_key($stattusWiseParcelsCounts, array_flip($known_statuses));

            // add 'other'
            $stattusWiseParcelsCounts['other'] = $other_total;
            //parcel overview end

            $statusWiseParcelStatsForParcelReport = app(FilteredParcelService::class)->totalParcelStats($last12MonthsFirstDay, $last12MonthsLastDay);
            $timeWiseFinancetatsForEarningReport = $this->financeReportStats(new Request([
                'filter' => ''
            ]));

            $lifeTimeFinancetatsForEarningReport = app(FinanceReportService::class)->getMonthlyReport($last12MonthsFirstDay, $last12MonthsLastDay);
            $lifeTimeFinancetatsForEarningtotals = app(FinanceReportService::class)->getTotals($start_date, $end_date);
            $data = [
                'notices' => $notices,
                'no_default_branch' => !Branch::where('default', 1)->exists(),
                'charts' => [
                    'labels' => $statusWiseParcelStatsForParcelReport['labels'],

                    //1st card
                    'total_cod' => app(CodService::class)->totalCod()['data'],
                    //2nd card
                    'life_time_profit' => $lifeTimeFinancetatsForEarningReport['profit'],
                    //3rd card
                    'merchant' => app(MerchantService::class)->totalMerchant()['data'],
                    //4th card
                    'parcel' => app(ParcelService::class)->totalParcel()['data'],
                    //5th card
                    'life_time_income' => $lifeTimeFinancetatsForEarningReport['income'],
                    //6th card
                    'life_time_expense' => $lifeTimeFinancetatsForEarningReport['expense'],
                    //7th card
                    'delivery_man_list' => app(DeliverymanService::class)->totalDeliveryman()['data'],
                    //8th card
                    'branch_list' => app(BranchService::class)->totalBranch()['data'],

                    //parcel report
                    'new_parcel' => $statusWiseParcelStatsForParcelReport['new_parcel'],
                    'processing_parcel' => $statusWiseParcelStatsForParcelReport['processing_parcel'],
                    'delivered_parcel' => $statusWiseParcelStatsForParcelReport['delivered_parcel'],

                    //earning report
                    'profit' => $timeWiseFinancetatsForEarningReport['monthly_data']['profit'], // app(FinanceReportService::class)->getTotals($filter_start_date, $filter_end_date),
                    'income' => $timeWiseFinancetatsForEarningReport['monthly_data']['income'], //app(IncomeService::class)->totalIncome($income),
                    'expense' => $timeWiseFinancetatsForEarningReport['monthly_data']['expense'], // app(ExpenseService::class)->totalExpense($expense),
                ],

                //1st card
                'life_time_total_cod' => $parcelstats->total_cod,                                // all-time COD
                'total_cod_in_last_30_days' => $parcelstats->last_month_total_cod,               // last month COD

                //2nd card
                'life_time_total_profit' => $lifeTimeFinancetatsForEarningtotals['total_profit'],
                'monthly_profit' => end($lifeTimeFinancetatsForEarningReport['profit']),
                //3rd card
                'life_time_total_merchant' => $merchantsCounts->total_merchants,
                'total_merchant_in_last_30_days' => $merchantsCounts->last_30_days ?? 0,
                //4th card
                'life_time_total_parcel_count' => $parcelstats->total_parcel_count,                  // all-time parcel count
                'total_parcel_count_in_last_30_days' => $parcelstats->last_month_total_parcel_count ?? 0, // last month parcel count
                //5th card
                'life_time_income' => $lifeTimeFinancetatsForEarningtotals['total_income'],
                //6th card
                'life_time_expense' => $lifeTimeFinancetatsForEarningtotals['total_expense'],
                //7th car
                'life_time_total_delivery_man' => DeliveryMan::where('status', 'active')->count(),
                //8th card
                'life_time_total_branch' => $this->totalBranch()->count(),

                //parcel report [COD FOR finance and admin not branch manager]
                'new_parcel' => $statusWiseParcelStatsForParcelReport['total_new_parcel'], //as pending is the initial status so its addressed as new parcel
                'new_parcel_cod' => $statusWiseParcelStatsForParcelReport['total_new_parcel_cod'],

                'processing_parcel' => $statusWiseParcelStatsForParcelReport['total_processing_parcel'],
                'processing_parcel_cod' => $statusWiseParcelStatsForParcelReport['total_processing_parcel_cod'],

                'delivered_parcel' => $statusWiseParcelStatsForParcelReport['total_delivered_parcel'],
                'delivered_parcel_cod' => $statusWiseParcelStatsForParcelReport['total_delivered_parcel_cod'],

                //parcel overview
                'life_time_delivered_parcel' => $stattusWiseParcelsCounts['delivered'] ?? 0,                      // $this->deliveredParcel($start_date, $end_date)->count();
                'life_time_partially_delivered_parcel' => $stattusWiseParcelsCounts['partially-delivered'] ?? 0,  // $this->partiallyDeliveredParcel($start_date, $end_date)->count();
                'life_time_new_parcel' => $stattusWiseParcelsCounts['pending'] ?? 0,                              // $this->pendingParcel($start_date, $end_date)->count();
                'life_time_processing_parcel' => $stattusWiseParcelsCounts['other'] ?? 0,                         // $this->processedParcel($start_date, $end_date)->count();
                'life_time_return_parcel' => $stattusWiseParcelsCounts['returned-to-merchant'] ?? 0,              // $this->returnParcel($start_date, $end_date)->count();
                'life_time_cancel_parcel' => $stattusWiseParcelsCounts['cancle'] ?? 0,                            //$this->cancelParcel($start_date, $end_date)->count();
                'life_time_deleted_parcel' => $stattusWiseParcelsCounts['deleted'] ?? 0,                          // $this->deletedParcel($start_date, $end_date)->count();

                //earning report
                'total_income' => $timeWiseFinancetatsForEarningReport['totals']['total_income'],
                'total_expense' => $timeWiseFinancetatsForEarningReport['totals']['total_expense'],
                'total_profit' => $timeWiseFinancetatsForEarningReport['totals']['total_profit'],
                //income and payout

                'lifetime_profit' => $this->profits($start_date, $today),
            ];

            $merchantBaseQuery = Merchant::with([
                'user:id,first_name,last_name,email,image_id'
            ])
                ->select('id', 'user_id', 'company', 'phone_number', 'default_account_id', 'created_at');

            // Latest merchants (fast, uses index on created_at)
            $data['latest_merchants'] = (clone $merchantBaseQuery)
                ->latest('created_at')
                ->take(3)
                ->get();

            // Top merchants (with count on parcels)
            $data['top_rank_merchants'] = (clone $merchantBaseQuery)
                ->withCount([
                    'parcels as successful_parcels_count' => function ($query) {
                        $query->whereIn('status', ['delivered', 'partially-delivered']);
                    }
                ])
                ->orderByDesc('successful_parcels_count')
                ->orderByDesc('id')
                ->take(3)
                ->get();
            // dd($data);

            if ($request->ajax()) {
                return response()->json(['data' => $data]);
            } else {
                return view('admin.dashboard', $data);

            }
        } elseif (@Sentinel::getUser()->dashboard == 'finance') {
            $no_default_branch = !Branch::where('default', 1)->exists();
            $parcelstats = Parcel::withPermission()
                ->selectRaw("SUM(CASE WHEN status IN ('partially-delivered','delivered') THEN price ELSE 0 END) as total_cod")
                ->first();

            $statusWiseParcelStatsForParcelReport = app(FilteredParcelService::class)->totalParcelStats($last12MonthsFirstDay, $last12MonthsLastDay);

            $incomeStatisticsReport = $this->incomeReportStats($filter_start_date, $filter_end_date);
            $lifeTimeFinancetatsForEarningReport = app(FinanceReportService::class)->getMonthlyReport($last12MonthsFirstDay, $last12MonthsLastDay);

            $data = [
                'notices' => $notices,
                'no_default_branch' => $no_default_branch,
                'deliveryman_balance' => $this->deliverymanBalance(),
                'staff_balance' => $this->staffBalance(),
                'total_charge' => $this->deliveredParcel($start_date, $end_date)->get()->sum('total_delivery_charge'),
                'pending_payout' => $this->totalPendingPayout(),
                'merchant_balance' => $this->merchantBalance(),
                'finance_self_wallet' => Account::where('user_id', \Sentinel::getUser()->id)->get()->sum('balance'),
                'fund_transfers' => FundTransfer::with('fromAccount')->get()->take(5),
                'cash_collections' => CompanyAccount::where('type', 'income')->where('source', 'cash_receive_from_delivery_man')
                    ->where('create_type', 'user_defined')->get()->take(5),
                'life_time_total_cod' => $parcelstats->total_cod,

                //parcel report [COD FOR finance and admin not branch manager]
                'new_parcel' => $statusWiseParcelStatsForParcelReport['total_new_parcel'], //as pending is the initial status so its addressed as new parcel
                'new_parcel_cod' => $statusWiseParcelStatsForParcelReport['total_new_parcel_cod'],
                'processing_parcel' => $statusWiseParcelStatsForParcelReport['total_processing_parcel'],
                'processing_parcel_cod' => $statusWiseParcelStatsForParcelReport['total_processing_parcel_cod'],

                'delivered_parcel' => $statusWiseParcelStatsForParcelReport['total_delivered_parcel'],
                'delivered_parcel_cod' => $statusWiseParcelStatsForParcelReport['total_delivered_parcel_cod'],
                'charts' => [
                    'labels' => $statusWiseParcelStatsForParcelReport['labels'],
                    //1st card
                    'total_cod' => app(CodService::class)->totalCod()['data'],
                    //2nd card
                    'life_time_profit' => $lifeTimeFinancetatsForEarningReport['profit'],
                    //3rd card
                    'merchant' => app(MerchantService::class)->totalMerchant()['data'],
                    //4th card
                    'parcel' => app(ParcelService::class)->totalParcel()['data'],
                    //5th card
                    'life_time_income' => $lifeTimeFinancetatsForEarningReport['income'],
                    //6th card
                    'life_time_expense' => $lifeTimeFinancetatsForEarningReport['expense'],
                    //7th card
                    'delivery_man_list' => app(DeliverymanService::class)->totalDeliveryman()['data'],
                    //8th card
                    'branch_list' => app(BranchService::class)->totalBranch()['data'],

                    //parcel report
                    'new_parcel' => $statusWiseParcelStatsForParcelReport['new_parcel'],
                    'processing_parcel' => $statusWiseParcelStatsForParcelReport['processing_parcel'],
                    'delivered_parcel' => $statusWiseParcelStatsForParcelReport['delivered_parcel'],

                    //income statistics
                    'cashFromMerchant' => $incomeStatisticsReport['cashFromMerchant'],
                    'vas' => $incomeStatisticsReport['charges']['vas'],
                    'charge' => $incomeStatisticsReport['charges']['charge'],
                ]
            ];


            if ($request->ajax()) {
                return response()->json(['data' => $data]);
            } else {
                return view('admin.finance-dashboard', $data);

            }
        } else {
            //parcel
            $parcelstats = Parcel::withPermission()
                ->selectRaw("
                        COUNT(*) as total_parcel_count,
                        SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as last_month_total_parcel_count,
                        SUM(CASE WHEN status IN ('partially-delivered','delivered') THEN price ELSE 0 END) as total_cod,
                        SUM(CASE WHEN created_at BETWEEN ? AND ? AND status IN ('partially-delivered','delivered') THEN price ELSE 0 END) as last_month_total_cod
                    ", [
                    $start_date_one_month_ago . ' 00:00:00',
                    $end_date . ' 23:59:59',
                    $start_date_one_month_ago . ' 00:00:00',
                    $end_date . ' 23:59:59',
                ])
                ->first();

            $statusWiseParcelStatsForParcelReport = app(FilteredParcelService::class)->totalParcelStats($last12MonthsFirstDay, $last12MonthsLastDay);
            $lifeTimeFinancetatsForEarningReport = app(FinanceReportService::class)->getMonthlyReport($last12MonthsFirstDay, $last12MonthsLastDay);

            $lifeTimeFinancetatsForEarningtotals = app(FinanceReportService::class)->getTotals($start_date, $end_date);



            $branchId = \Sentinel::getUser()->branch_id;

            // Get all delivery men IDs for the branch
            $deliveryMenIds = DeliveryMan::whereHas('user', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->pluck('id');

            $totals = DeliveryManAccount::whereIn('delivery_man_id', $deliveryMenIds)
                ->selectRaw("
                    SUM(CASE WHEN type='income' AND source NOT IN ('pickup_commission','parcel_delivery','opening_balance') THEN amount ELSE 0 END) as total_income,
                    SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as total_expense
                ")
                ->first();

            $total_cash_income = $totals->total_income;
            $total_cash_expense = $totals->total_expense;

            //branch balance
            $users = User::where('branch_id', $branchId)
                ->where('user_type', 'staff')
                ->pluck('id');
            $branch_account = Account::whereIn('user_id', $users)->get();
            $branch_balance = $branch_account->sum(function ($account) {
                return $account->incomes()->sum('amount')
                    + $account->fundReceives()->sum('amount')
                    - $account->expenses()->sum('amount')
                    - $account->fundTransfers()->sum('amount');
            });
            $data = [
                'notices' => $notices,
                'life_time_total_cod' => $parcelstats->total_cod,                                // all-time COD
                'total_cod_in_last_30_days' => $parcelstats->last_month_total_cod,               // last month COD

                'life_time_total_parcel_count' => $parcelstats->total_parcel_count,                  // all-time parcel count
                'total_parcel_count_in_last_30_days' => $parcelstats->last_month_total_parcel_count ?? 0, // last month parcel count

                'total_delivery_man' => $this->totalDeliveryMan()
                    ->whereHas('user', function ($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->count(),
                'branch_wise_total_merchant' => Merchant::whereHas('shops', function ($query) use ($branchId) {
                    $query->where('pickup_branch_id', $branchId);
                })
                    ->count(),
                'pending_pickup' => $this->pendingParcel($start_date, $end_date)->withPermission()->count(),
                'cash_in' => $total_cash_income - $total_cash_expense,

                //branch manager
                'branch_wise_total_new_parcel' => $statusWiseParcelStatsForParcelReport['total_new_parcel'],
                'branch_wise_total_processing_parcel' => $statusWiseParcelStatsForParcelReport['total_processing_parcel'],
                'branch_wise_total_delivered_parcel' => $statusWiseParcelStatsForParcelReport['total_delivered_parcel'],
                'branch_wise_total_new_parcel_cod' => $statusWiseParcelStatsForParcelReport['total_new_parcel_cod'],
                'branch_wise_total_processing_parcel_cod' => $statusWiseParcelStatsForParcelReport['total_processing_parcel_cod'],
                'branch_wise_total_delivered_parcel_cod' => $statusWiseParcelStatsForParcelReport['total_delivered_parcel_cod'],
                'branch_balance' => $branch_balance,
                'latest_parcels' => Parcel::withPermission()->latest()->take(5)->get(),

                'latest_delivery_parcels' => Parcel::where('status', 'delivered')->withPermission()->latest()->take(5)->get(),
                'charts' => [  //branch manager dashboard
                    'labels' => $statusWiseParcelStatsForParcelReport['labels'],

                    //1st card
                    'total_cod' => app(CodService::class)->totalCod()['data'],
                    //2nd card
                    'life_time_profit' => $lifeTimeFinancetatsForEarningReport['profit'],
                    //3rd card
                    'merchant' => app(MerchantService::class)->totalMerchant()['data'],
                    //4th card
                    'parcel' => app(ParcelService::class)->totalParcel()['data'],
                    //5th card
                    'life_time_income' => $lifeTimeFinancetatsForEarningReport['income'],
                    //6th card
                    'life_time_expense' => $lifeTimeFinancetatsForEarningReport['expense'],
                    //7th card
                    'delivery_man_list' => app(DeliverymanService::class)->totalDeliveryman()['data'],
                    //8th card
                    'branch_list' => app(BranchService::class)->totalBranch()['data'],

                    'new_parcel' => $statusWiseParcelStatsForParcelReport['new_parcel'],
                    'processing_parcel' => $statusWiseParcelStatsForParcelReport['processing_parcel'],
                    'delivered_parcel' => $statusWiseParcelStatsForParcelReport['delivered_parcel'],

                    'added_parcel' => $statusWiseParcelStatsForParcelReport['added_parcel'],
                ]
            ];
            if ($request->ajax()) {
                return response()->json(['data' => $data]);
            } else {
                return view('admin.branch-manager-dashboard', $data);

            }
        }
    }

    public function getFormatedDate($request)
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $last7days = date('Y-m-d', strtotime('-7 days'));
        $last14days = date('Y-m-d', strtotime('-14 days'));
        $last12MonthsFirstDay = date('Y-m-01', strtotime('-12 months'));
        $last12MonthsLastDay = date('Y-m-t', strtotime('-12 months'));
        $start_date_six_month_ago = date('Y-m-d', strtotime('-6 month'));

        $start_date_one_month_ago = date('Y-m-d', strtotime('-1 month'));
        $start_date_one_year_ago = date('Y-m-d', strtotime('-1 year'));
        $start_date = '2000-04-01' . ' 00:00:00';
        $end_date = date('Y-m-d') . ' 23:59:59';
        $filter = $request['filter'] ?? '';
        $custom_start_date = $request['startDate'] ?? '';
        $custom_end_date = $request['endDate'] ?? '';

        switch ($filter) {
            case 'yesterday':
                $filter_start_date = $yesterday . ' 00:00:00';
                $filter_end_date = $today . ' 23:59:59';
                break;
            case 'last_7_day':
                $filter_start_date = $last7days . ' 00:00:00';
                $filter_end_date = $today . ' 23:59:59';
                break;
            case 'last_14_day':
                $filter_start_date = $last14days . ' 00:00:00';
                $filter_end_date = $today . ' 23:59:59';
                break;
            case 'since_last_month':
                $filter_start_date = $start_date_one_month_ago . ' 00:00:00';
                $filter_end_date = $today . ' 23:59:59';
                break;
            case 'since_last_6_month':
                $filter_start_date = $start_date_six_month_ago . ' 00:00:00';
                $filter_end_date = $today . ' 23:59:59';
                break;
            case 'since_this_year':
                $filter_start_date = $start_date_one_year_ago . ' 00:00:00';
                $filter_end_date = $today . ' 23:59:59';
                break;
            case 'last_12_month':
                $filter_start_date = $last12MonthsFirstDay . ' 00:00:00';
                $filter_end_date = $last12MonthsLastDay . ' 23:59:59';
                break;
            case 'custom':
                $filter_start_date = $custom_start_date . ' 00:00:00';
                $filter_end_date = $custom_end_date . ' 23:59:59';
                break;
            default:
                $filter_start_date = $today . ' 00:00:00';
                $filter_end_date = $today . ' 23:59:59';
                break;
        }
        return [$filter_start_date, $filter_end_date];
    }
    public function incomeReportStats($filter_start_date, $filter_end_date)
    {
        $data['cashFromMerchant'] = app(CashFromMerchantService::class)->totalCash($this->cashFromMerchant($filter_start_date, $filter_end_date));
        $data['charges'] = app(ChargesService::class)->getMonthlyReport($filter_start_date, $filter_end_date);

        return $data;
    }
    public function parcelReportStats(Request $request)
    {
        $formatedDate = $this->getFormatedDate($request);
        //dd($formatedDate);
        return app(FilteredParcelService::class)->totalParcelStats($formatedDate[0], $formatedDate[1]);
    }

    public function financeReportStats(Request $request)
    {
        $formatedDate = $this->getFormatedDate($request);

        $data['totals'] = app(FinanceReportService::class)->getTotals($formatedDate[0], $formatedDate[1]);
        $data['monthly_data'] = app(FinanceReportService::class)->getMonthlyReport($formatedDate[0], $formatedDate[1]);
        return $data;
    }



    public function totalMerchant($start_date, $end_date)
    {
        $data = Merchant::where('status', 'active')->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date);
        return $data;
    }

    public function totalDeliveryMan()
    {
        $data = DeliveryMan::where('status', 'active');
        return $data;
    }
    public function totalBranch()
    {
        $data = Branch::where('status', 'active')->get();
        return $data;
    }
    public function totalParcel($start_date, $end_date)
    {
        $data = Parcel::whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)
            ->withPermission();
        return $data;
    }
    public function totalDeliveredParcel($start_date, $end_date)
    {
        $data = Parcel::whereIn('status', ['partially-delivered', 'delivered'])
            ->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)
            ->withPermission()->get();

        return $data;
    }

    public function processedParcel($start_date, $end_date)
    {
        $data = Parcel::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->whereNotIn('status', ['pending', 'delivered', 'returned-to-merchant', 'partially-delivered', 'deleted', 'cancle']);
        return $data;
    }

    public function pendingParcel($start_date, $end_date)
    {
        $data = Parcel::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->where('status', 'pending');

        return $data;
    }

    public function deliveredParcel($start_date, $end_date)
    {
        $data = Parcel::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->where('status', 'delivered');

        return $data;
    }

    public function partiallyDeliveredParcel($start_date, $end_date)
    {
        $data = Parcel::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->where('status', 'partially-delivered');

        return $data;
    }

    public function returnParcel($start_date, $end_date)
    {
        $data = Parcel::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->where('status', 'returned-to-merchant');

        return $data;
    }

    public function cancelParcel($start_date, $end_date)
    {
        $data = Parcel::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->where('status', 'cancel');

        return $data;
    }

    public function deletedParcel($start_date, $end_date)
    {
        $data = Parcel::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->where('status', 'deleted');

        return $data;
    }

    public function income($start_date, $end_date)
    {
        $data = CompanyAccount::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)->whereIn('source', ['delivery_charge_receive_from_merchant', 'cash_receive_from_delivery_man'])
            ->where('type', 'income')
            ->where('create_type', 'user_defined');

        return $data;

    }

    public function expense($start_date, $end_date)
    {
        $data = CompanyAccount::whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->where('type', 'expense')
            ->where('create_type', 'user_defined');
        return $data;

    }


    public function cashFromMerchant($start_date, $end_date)
    {
        $data = StaffAccount::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)->where('details', 'delivery_charge_receive_from_merchant');
        return $data;
    }

    public function get_counts($parcels)
    {
        $delivered_cod = $parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->sum('price');
        $data['total_cod'] = format_price($parcels->where('is_partially_delivered', true)->sum('price') + $delivered_cod);
        $data['parcels_count'] = $parcels->count();
        $data['processing_count'] = $parcels->whereNotIn('status', ['delivered', 'delivered-and-verified', 'cancel', 'returned-to-merchant', 'deleted'])->where('is_partially_delivered', false)->count();
        $data['cancelled_count'] = $parcels->where('status', 'cancel')->count();
        $data['deleted_count'] = $parcels->where('status', 'deleted')->count();
        $data['partial_delivered_count'] = $parcels->where('is_partially_delivered', true)->count();
        $data['returned_count'] = $parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();

        return $data;
    }

    public function report(Request $request)
    {
        $report_type = $request->report_type;

        if ($report_type == 'today'):
            $today = date('Y-m-d');
            $parcels = Parcel::where('created_at', '>=', $today . ' 00:00:00')
                ->where('created_at', '<=', $today . ' 23:59:59')
                ->when(!hasPermission('read_all_parcel'), function ($query) {
                    $query->where(function ($q) {
                        $q->where('branch_id', Sentinel::getUser()->branch_id)
                            ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                            ->orWhereNull('pickup_branch_id')
                            ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
                    });
                })
                ->latest()->get();

            $parcel_delivered = ParcelEvent::whereDate('created_at', $today . ' 00:00:00')
                ->whereDate('created_at', '<=', $today . ' 23:59:59')
                ->whereIn('title', ['parcel_delivered_event', 'parcel_partial_delivered_event', 'parcel_partial_delivered_event'])
                ->where('reverse_status', null)
                ->get();

            $data['totalParcelDelivered'] = $parcel_delivered->count();


            $data['dates'] = ["12AM - 02AM", "02AM - 04AM", "04AM - 06AM", "06AM - 08AM", "08AM - 10AM", "10AM - 12PM", "12PM - 02PM", "02PM - 04PM", "04PM - 06PM", "06PM - 08PM", "08PM - 10PM", "10PM - 12PM"];

            for ($i = 0; $i <= 11; $i++) {

                $j = $i * 2;

                $j = str_pad($j, 2, "0", STR_PAD_LEFT);
                $in = $j + 1;
                if ($in < 10) {
                    $in = str_pad($in, 2, "0", STR_PAD_LEFT);
                }

                //date range parcels
                $start = date('Y-m-d ') . $j . ':00:00';
                $end = date('Y-m-d ') . $in . ':59:59';

                $merchant_parcels = $parcels->where('created_at', '>=', $start);
                $merchant_parcels = $merchant_parcels->where('created_at', '<=', $end);

                // count
                $data['totalParcel'][] = $totalParcel = $merchant_parcels->count();
                $data['cancelled'][] = $cancelled = $merchant_parcels->where('status', 'cancel')->count();
                $data['deleted'][] = $deleted = $merchant_parcels->where('status', 'deleted')->count();
                $data['partially_delivered'][] = $partially_delivered = $merchant_parcels->where('is_partially_delivered', true)->count();
                $data['delivered'][] = $delivered = $merchant_parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
                $data['returned'][] = $returned = $merchant_parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
                $data['processing'][] = $totalParcel - ($cancelled + $deleted + $partially_delivered + $delivered + $returned);
            }

            $data['totalParcels'] = $parcels->count();
            $data['totalCancelled'] = $parcels->where('status', 'cancel')->count();
            $data['totalDeleted'] = $parcels->where('status', 'deleted')->count();
            $data['totalDelivered'] = $parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
            $data['totalPartialDelivered'] = $parcels->where('is_partially_delivered', true)->count();
            $data['totalReturned'] = $parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
            $data['totalProcessing'] = $data['totalParcels'] - ($data['totalCancelled'] + $data['totalDeleted'] + $data['totalDelivered'] + $data['totalPartialDelivered'] + $data['totalReturned']);

            $data['merchant_total_withdraw'] = MerchantWithdraw::whereIn('status', ['processed', 'pending'])->where('date', $today)->sum('amount');

            $profits = $this->profits($today, $today);

        elseif ($report_type == 'yesterday'):
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $parcels = Parcel::where('created_at', '>=', $yesterday . ' 00:00:00')
                ->when(!hasPermission('read_all_parcel'), function ($query) {
                    $query->where(function ($q) {
                        $q->where('branch_id', Sentinel::getUser()->branch_id)
                            ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                            ->orWhereNull('pickup_branch_id')
                            ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
                    });
                })
                ->where('created_at', '<=', $yesterday . ' 23:59:59')
                ->latest()->get();

            $parcel_delivered = ParcelEvent::whereDate('created_at', $yesterday . ' 00:00:00')
                ->whereDate('created_at', '<=', $yesterday . ' 23:59:59')
                ->whereIn('title', ['parcel_delivered_event', 'parcel_partial_delivered_event', 'parcel_partial_delivered_event'])
                ->where('reverse_status', null)
                ->get();

            $data['totalParcelDelivered'] = $parcel_delivered->count();

            $data['dates'] = ["12AM - 02AM", "02AM - 04AM", "04AM - 06AM", "06AM - 08AM", "08AM - 10AM", "10AM - 12PM", "12PM - 02PM", "02PM - 04PM", "04PM - 06PM", "06PM - 08PM", "08PM - 10PM", "10PM - 12PM"];

            for ($i = 0; $i <= 11; $i++) {

                $j = $i * 2;

                $j = str_pad($j, 2, "0", STR_PAD_LEFT);
                $in = $j + 1;
                if ($in < 10) {
                    $in = str_pad($in, 2, "0", STR_PAD_LEFT);
                }

                //date range parcels
                $start = $yesterday . ' ' . $j . ':00:00';
                $end = $yesterday . ' ' . $in . ':59:59';

                $merchant_parcels = $parcels->where('created_at', '>=', $start);
                $merchant_parcels = $merchant_parcels->where('created_at', '<=', $end);

                // count
                $data['totalParcel'][] = $totalParcel = $merchant_parcels->count();
                $data['cancelled'][] = $cancelled = $merchant_parcels->where('status', 'cancel')->count();
                $data['deleted'][] = $deleted = $merchant_parcels->where('status', 'deleted')->count();
                $data['partially_delivered'][] = $partially_delivered = $merchant_parcels->where('is_partially_delivered', true)->count();
                $data['delivered'][] = $delivered = $merchant_parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
                $data['returned'][] = $returned = $merchant_parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
                $data['processing'][] = $totalParcel - ($cancelled + $deleted + $partially_delivered + $delivered + $returned);
            }

            $data['totalParcels'] = $parcels->count();
            $data['totalCancelled'] = $parcels->where('status', 'cancel')->count();
            $data['totalDeleted'] = $parcels->where('status', 'deleted')->count();
            $data['totalDelivered'] = $parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
            $data['totalPartialDelivered'] = $parcels->where('is_partially_delivered', true)->count();
            $data['totalReturned'] = $parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
            $data['totalProcessing'] = $data['totalParcels'] - ($data['totalCancelled'] + $data['totalDeleted'] + $data['totalDelivered'] + $data['totalPartialDelivered'] + $data['totalReturned']);

            $data['merchant_total_withdraw'] = MerchantWithdraw::whereIn('status', ['processed', 'pending'])->where('date', $yesterday)->sum('amount');

            $profits = $this->profits($yesterday, $yesterday);

        elseif ($report_type == 'this_week'):
            $now = Carbon::now();

            $start_day = date('Y-m-d', strtotime($now->startOfWeek(Carbon::SATURDAY)));
            $end_day = date('Y-m-d', strtotime($now->endOfWeek(Carbon::FRIDAY)));

            $parcels = Parcel::where('created_at', '>=', $start_day . ' 00:00:00')
                ->where('created_at', '<=', $end_day . ' 23:59:59')
                ->when(!hasPermission('read_all_parcel'), function ($query) {
                    $query->where(function ($q) {
                        $q->where('branch_id', Sentinel::getUser()->branch_id)
                            ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                            ->orWhereNull('pickup_branch_id')
                            ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
                    });
                })
                ->latest()->get();

            $parcel_delivered = ParcelEvent::where('created_at', '>=', $start_day . ' 00:00:00')
                ->where('created_at', '<=', $end_day . ' 23:59:59')
                ->whereIn('title', ['parcel_delivered_event', 'parcel_partial_delivered_event', 'parcel_partial_delivered_event'])
                ->where('reverse_status', null)
                ->get();

            $data['totalParcelDelivered'] = $parcel_delivered->count();

            for ($i = 0; $i <= 6; $i++) {

                $created_at = date('Y-m-d', strtotime($start_day . "+" . $i . ' days'));

                $merchant_parcels = $parcels->where('created_at', '>=', $created_at . ' 00:00:00')->where('created_at', '<=', $created_at . ' 23:59:59');

                // dates
                $data['dates'][] = date('d M, Y', strtotime($start_day . "+" . $i . ' days'));

                // count
                $data['totalParcel'][] = $totalParcel = $merchant_parcels->count();
                $data['cancelled'][] = $cancelled = $merchant_parcels->where('status', 'cancel')->count();
                $data['deleted'][] = $deleted = $merchant_parcels->where('status', 'deleted')->count();
                $data['partially_delivered'][] = $partially_delivered = $merchant_parcels->where('is_partially_delivered', true)->count();
                $data['delivered'][] = $delivered = $merchant_parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
                $data['returned'][] = $returned = $merchant_parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
                $data['processing'][] = $totalParcel - ($cancelled + $deleted + $partially_delivered + $delivered + $returned);
            }

            $data['totalParcels'] = $parcels->count();
            $data['totalCancelled'] = $parcels->where('status', 'cancel')->count();
            $data['totalDeleted'] = $parcels->where('status', 'deleted')->count();
            $data['totalDelivered'] = $parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
            $data['totalPartialDelivered'] = $parcels->where('is_partially_delivered', true)->count();
            $data['totalReturned'] = $parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
            $data['totalProcessing'] = $data['totalParcels'] - ($data['totalCancelled'] + $data['totalDeleted'] + $data['totalDelivered'] + $data['totalPartialDelivered'] + $data['totalReturned']);

            $start = date('Y-m-d', strtotime($start_day));
            $end = date('Y-m-d', strtotime($end_day));

            $data['merchant_total_withdraw'] = MerchantWithdraw::whereIn('status', ['processed', 'pending'])
                ->where('date', '>=', $start)
                ->where('date', '<=', $end)
                ->sum('amount');

            $profits = $this->profits($start, $end);

        elseif ($report_type == 'last_week'):
            $now = Carbon::now();

            $start_day = date('Y-m-d', strtotime($now->startOfWeek(Carbon::SATURDAY) . ('-1 week')));
            $end_day = date('Y-m-d', strtotime($now->endOfWeek(Carbon::FRIDAY) . ('-1 week')));

            $parcels = Parcel::where('created_at', '>=', $start_day . ' 00:00:00')
                ->where('created_at', '<=', $end_day . ' 23:59:59')
                ->when(!hasPermission('read_all_parcel'), function ($query) {
                    $query->where(function ($q) {
                        $q->where('branch_id', Sentinel::getUser()->branch_id)
                            ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                            ->orWhereNull('pickup_branch_id')
                            ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
                    });
                })
                ->latest()->get();

            $parcel_delivered = ParcelEvent::where('created_at', '>=', $start_day . ' 00:00:00')
                ->where('created_at', '<=', $end_day . ' 23:59:59')
                ->whereIn('title', ['parcel_delivered_event', 'parcel_partial_delivered_event', 'parcel_partial_delivered_event'])
                ->where('reverse_status', null)
                ->get();

            $data['totalParcelDelivered'] = $parcel_delivered->count();

            for ($i = 0; $i <= 6; $i++) {

                $created_at = date('Y-m-d', strtotime($start_day . "+" . $i . ' days'));

                $merchant_parcels = $parcels->where('created_at', '>=', $created_at . ' 00:00:00')->where('created_at', '<=', $created_at . ' 23:59:59');

                // dates
                $data['dates'][] = date('d M, Y', strtotime($start_day . "+" . $i . ' days'));

                // count
                $data['totalParcel'][] = $totalParcel = $merchant_parcels->count();
                $data['cancelled'][] = $cancelled = $merchant_parcels->where('status', 'cancel')->count();
                $data['deleted'][] = $deleted = $merchant_parcels->where('status', 'deleted')->count();
                $data['partially_delivered'][] = $partially_delivered = $merchant_parcels->where('is_partially_delivered', true)->count();
                $data['delivered'][] = $delivered = $merchant_parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
                $data['returned'][] = $returned = $merchant_parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
                $data['processing'][] = $totalParcel - ($cancelled + $deleted + $partially_delivered + $delivered + $returned);
            }

            $data['totalParcels'] = $parcels->count();
            $data['totalCancelled'] = $parcels->where('status', 'cancel')->count();
            $data['totalDeleted'] = $parcels->where('status', 'deleted')->count();
            $data['totalDelivered'] = $parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
            $data['totalPartialDelivered'] = $parcels->where('is_partially_delivered', true)->count();
            $data['totalReturned'] = $parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
            $data['totalProcessing'] = $data['totalParcels'] - ($data['totalCancelled'] + $data['totalDeleted'] + $data['totalDelivered'] + $data['totalPartialDelivered'] + $data['totalReturned']);

            $start = date('Y-m-d', strtotime($start_day));
            $end = date('Y-m-d', strtotime($end_day));

            $data['merchant_total_withdraw'] = MerchantWithdraw::whereIn('status', ['processed', 'pending'])
                ->where('date', '>=', $start)
                ->where('date', '<=', $end)
                ->sum('amount');

            $profits = $this->profits($start, $end);
        elseif ($report_type == 'this_month'):

            $start = date('Y-m-' . '01');
            $end = date('Y-m-t');

            $parcels = Parcel::where('created_at', '>=', $start . ' 00:00:00')
                ->where('created_at', '<=', $end . ' 23:59:59')
                ->when(!hasPermission('read_all_parcel'), function ($query) {
                    $query->where(function ($q) {
                        $q->where('branch_id', Sentinel::getUser()->branch_id)
                            ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                            ->orWhereNull('pickup_branch_id')
                            ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
                    });
                })
                ->latest()->get();
            $parcel_delivered = ParcelEvent::where('created_at', '>=', $start . ' 00:00:00')
                ->where('created_at', '<=', $end . ' 23:59:59')
                ->whereIn('title', ['parcel_delivered_event', 'parcel_partial_delivered_event', 'parcel_partial_delivered_event'])
                ->where('reverse_status', null)
                ->get();

            $data['totalParcelDelivered'] = $parcel_delivered->count();

            for ($i = 1; $i <= date('t'); $i++) {
                if ($i < 10) {
                    $i = str_pad($i, 2, "0", STR_PAD_LEFT);
                }
                //date range parcels

                $created_at = date('Y-m-' . $i);

                $merchant_parcels = $parcels->where('created_at', '>=', $created_at . ' 00:00:00')->where('created_at', '<=', $created_at . ' 23:59:59');

                // dates
                $data['dates'][] = $i . ' ' . date('M');

                // count
                $data['totalParcel'][] = $totalParcel = $merchant_parcels->count();
                $data['cancelled'][] = $cancelled = $merchant_parcels->where('status', 'cancel')->count();
                $data['deleted'][] = $deleted = $merchant_parcels->where('status', 'deleted')->count();
                $data['partially_delivered'][] = $partially_delivered = $merchant_parcels->where('is_partially_delivered', true)->count();
                $data['delivered'][] = $delivered = $merchant_parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
                $data['returned'][] = $returned = $merchant_parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
                $data['processing'][] = $totalParcel - ($cancelled + $deleted + $partially_delivered + $delivered + $returned);
            }

            $data['totalParcels'] = $parcels->count();
            $data['totalCancelled'] = $parcels->where('status', 'cancel')->count();
            $data['totalDeleted'] = $parcels->where('status', 'deleted')->count();
            $data['totalDelivered'] = $parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
            $data['totalPartialDelivered'] = $parcels->where('is_partially_delivered', true)->count();
            $data['totalReturned'] = $parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
            $data['totalProcessing'] = $data['totalParcels'] - ($data['totalCancelled'] + $data['totalDeleted'] + $data['totalDelivered'] + $data['totalPartialDelivered'] + $data['totalReturned']);

            $data['merchant_total_withdraw'] = MerchantWithdraw::whereIn('status', ['processed', 'pending'])
                ->where('date', '>=', $start)
                ->where('date', '<=', $end)
                ->sum('amount');

            $profits = $this->profits($start, $end);
        elseif ($report_type == 'last_month'):

            $start = date('Y-m-d', strtotime("first day of -1 month"));
            $end = date('Y-m-d', strtotime("last day of -1 month"));

            $parcels = Parcel::where('created_at', '>=', $start . ' 00:00:00')
                ->where('created_at', '<=', $end . ' 23:59:59')
                ->when(!hasPermission('read_all_parcel'), function ($query) {
                    $query->where(function ($q) {
                        $q->where('branch_id', Sentinel::getUser()->branch_id)
                            ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                            ->orWhereNull('pickup_branch_id')
                            ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
                    });
                })
                ->latest()->get();

            $parcel_delivered = ParcelEvent::where('created_at', '>=', $start . ' 00:00:00')
                ->where('created_at', '<=', $end . ' 23:59:59')
                ->whereIn('title', ['parcel_delivered_event', 'parcel_partial_delivered_event', 'parcel_partial_delivered_event'])
                ->where('reverse_status', null)
                ->get();

            $data['totalParcelDelivered'] = $parcel_delivered->count();

            for ($i = 1; $i <= date('t', strtotime('last day of -1 month')); $i++) {
                if ($i < 10) {
                    $i = str_pad($i, 2, "0", STR_PAD_LEFT);
                }
                //date range parcels

                $created_at = date('Y-m', strtotime('first day of -1 month')) . '-' . $i;

                $merchant_parcels = $parcels->where('created_at', '>=', $created_at . ' 00:00:00')->where('created_at', '<=', $created_at . ' 23:59:59');

                // dates
                $data['dates'][] = $i . ' ' . date('M', strtotime('first day of -1 month'));

                // count
                $data['totalParcel'][] = $totalParcel = $merchant_parcels->count();
                $data['cancelled'][] = $cancelled = $merchant_parcels->where('status', 'cancel')->count();
                $data['deleted'][] = $deleted = $merchant_parcels->where('status', 'deleted')->count();
                $data['partially_delivered'][] = $partially_delivered = $merchant_parcels->where('is_partially_delivered', true)->count();
                $data['delivered'][] = $delivered = $merchant_parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
                $data['returned'][] = $returned = $merchant_parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
                $data['processing'][] = $totalParcel - ($cancelled + $deleted + $partially_delivered + $delivered + $returned);
            }

            $data['totalParcels'] = $parcels->count();
            $data['totalCancelled'] = $parcels->where('status', 'cancel')->count();
            $data['totalDeleted'] = $parcels->where('status', 'deleted')->count();
            $data['totalDelivered'] = $parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
            $data['totalPartialDelivered'] = $parcels->where('is_partially_delivered', true)->count();
            $data['totalReturned'] = $parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
            $data['totalProcessing'] = $data['totalParcels'] - ($data['totalCancelled'] + $data['totalDeleted'] + $data['totalDelivered'] + $data['totalPartialDelivered'] + $data['totalReturned']);

            $data['merchant_total_withdraw'] = MerchantWithdraw::whereIn('status', ['processed', 'pending'])
                ->where('date', '>=', $start)
                ->where('date', '<=', $end)
                ->sum('amount');

            $profits = $this->profits($start, $end);

        elseif ($report_type == 'last_3_month'):
            $start_month = date('Y-m', strtotime('-3 month'));
            $end_month = date('Y-m', strtotime('first day of -1 month'));

            $parcels = Parcel::where('created_at', '>=', $start_month . '-01' . ' 00:00:00')
                ->where('created_at', '<=', date('Y-m-d', strtotime('last day of -1 month')) . ' 23:59:59')
                ->when(!hasPermission('read_all_parcel'), function ($query) {
                    $query->where(function ($q) {
                        $q->where('branch_id', Sentinel::getUser()->branch_id)
                            ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                            ->orWhereNull('pickup_branch_id')
                            ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
                    });
                })
                ->latest()->get();

            $parcel_delivered = ParcelEvent::where('created_at', '>=', $start_month . '-01' . ' 00:00:00')
                ->where('created_at', '<=', date('Y-m-d', strtotime('last day of -1 month')) . ' 23:59:59')
                ->whereIn('title', ['parcel_delivered_event', 'parcel_partial_delivered_event', 'parcel_partial_delivered_event'])
                ->where('reverse_status', null)
                ->get();

            $data['totalParcelDelivered'] = $parcel_delivered->count();

            for ($i = 3; $i >= 1; $i--) {

                $start = date('Y-m-d', strtotime('first day of -' . $i . ' month'));
                $end = date('Y-m-d', strtotime('last day of -' . $i . ' month'));

                $merchant_parcels = $parcels->where('created_at', '>=', $start . ' 00:00:00' . '%');
                $merchant_parcels = $merchant_parcels->where('created_at', '<=', $end . ' 23:59:59' . '%');

                // dates
                $data['dates'][] = $start = date('Y-m', strtotime('first day of -' . $i . ' month'));

                // count
                $data['totalParcel'][] = $totalParcel = $merchant_parcels->count();
                $data['cancelled'][] = $cancelled = $merchant_parcels->where('status', 'cancel')->count();
                $data['deleted'][] = $deleted = $merchant_parcels->where('status', 'deleted')->count();
                $data['partially_delivered'][] = $partially_delivered = $merchant_parcels->where('is_partially_delivered', true)->count();
                $data['delivered'][] = $delivered = $merchant_parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
                $data['returned'][] = $returned = $merchant_parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
                $data['processing'][] = $totalParcel - ($cancelled + $deleted + $partially_delivered + $delivered + $returned);
            }

            $data['totalParcels'] = $parcels->count();
            $data['totalCancelled'] = $parcels->where('status', 'cancel')->count();
            $data['totalDeleted'] = $parcels->where('status', 'deleted')->count();
            $data['totalDelivered'] = $parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
            $data['totalPartialDelivered'] = $parcels->where('is_partially_delivered', true)->count();
            $data['totalReturned'] = $parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
            $data['totalProcessing'] = $data['totalParcels'] - ($data['totalCancelled'] + $data['totalDeleted'] + $data['totalDelivered'] + $data['totalPartialDelivered'] + $data['totalReturned']);

            $data['merchant_total_withdraw'] = MerchantWithdraw::whereIn('status', ['processed', 'pending'])->where('date', '>=', $start_month)->where('date', '<=', $end_month)->sum('amount');

            $start = date('Y-m-d', strtotime('first day of -3 month'));
            $end = date('Y-m-d', strtotime('last day of -1 month'));

            $profits = $this->profits($start, $end);

        elseif ($report_type == 'last_6_month'):
            $start_month = date('Y-m', strtotime('-6 month'));
            $end_month = date('Y-m', strtotime('first day of -1 month'));

            $parcels = Parcel::where('created_at', '>=', $start_month . '-01' . ' 00:00:00')
                ->where('created_at', '<=', date('Y-m-d', strtotime('last day of -1 month')) . ' 23:59:59')
                ->when(!hasPermission('read_all_parcel'), function ($query) {
                    $query->where(function ($q) {
                        $q->where('branch_id', Sentinel::getUser()->branch_id)
                            ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                            ->orWhereNull('pickup_branch_id')
                            ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
                    });
                })
                ->latest()->get();

            $parcel_delivered = ParcelEvent::where('created_at', '>=', $start_month . '-01' . ' 00:00:00')
                ->where('created_at', '<=', date('Y-m-d', strtotime('last day of -1 month')) . ' 23:59:59')
                ->whereIn('title', ['parcel_delivered_event', 'parcel_partial_delivered_event', 'parcel_partial_delivered_event'])
                ->where('reverse_status', null)
                ->get();

            $data['totalParcelDelivered'] = $parcel_delivered->count();

            for ($i = 6; $i >= 1; $i--) {

                $start = date('Y-m-d', strtotime('first day of -' . $i . ' month'));
                $end = date('Y-m-d', strtotime('last day of -' . $i . ' month'));

                $merchant_parcels = $parcels->where('created_at', '>=', $start . ' 00:00:00' . '%');
                $merchant_parcels = $merchant_parcels->where('created_at', '<=', $end . ' 23:59:59' . '%');

                // dates
                $data['dates'][] = $start = date('Y-m', strtotime('first day of -' . $i . ' month'));

                // count
                $data['totalParcel'][] = $totalParcel = $merchant_parcels->count();
                $data['cancelled'][] = $cancelled = $merchant_parcels->where('status', 'cancel')->count();
                $data['deleted'][] = $deleted = $merchant_parcels->where('status', 'deleted')->count();
                $data['partially_delivered'][] = $partially_delivered = $merchant_parcels->where('is_partially_delivered', true)->count();
                $data['delivered'][] = $delivered = $merchant_parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
                $data['returned'][] = $returned = $merchant_parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
                $data['processing'][] = $totalParcel - ($cancelled + $deleted + $partially_delivered + $delivered + $returned);
            }

            $data['totalParcels'] = $parcels->count();
            $data['totalCancelled'] = $parcels->where('status', 'cancel')->count();
            $data['totalDeleted'] = $parcels->where('status', 'deleted')->count();
            $data['totalDelivered'] = $parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
            $data['totalPartialDelivered'] = $parcels->where('is_partially_delivered', true)->count();
            $data['totalReturned'] = $parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
            $data['totalProcessing'] = $data['totalParcels'] - ($data['totalCancelled'] + $data['totalDeleted'] + $data['totalDelivered'] + $data['totalPartialDelivered'] + $data['totalReturned']);

            $data['merchant_total_withdraw'] = MerchantWithdraw::whereIn('status', ['processed', 'pending'])->where('date', '>=', $start_month)->where('date', '<=', $end_month)->sum('amount');
            $start = date('Y-m-d', strtotime('first day of -6 month'));
            $end = date('Y-m-d', strtotime('last day of -1 month'));

            $profits = $this->profits($start, $end);

        elseif ($report_type == 'this_year'):

            $start_month = date('Y-' . '01');
            $end_month = date('Y-' . '12');

            $parcels = Parcel::where('created_at', '>=', $start_month . '-01 00:00:00')
                ->where('created_at', '<=', $end_month . '-31 23:59:59')
                ->when(!hasPermission('read_all_parcel'), function ($query) {
                    $query->where(function ($q) {
                        $q->where('branch_id', Sentinel::getUser()->branch_id)
                            ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                            ->orWhereNull('pickup_branch_id')
                            ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
                    });
                })
                ->latest()->get();

            $parcel_delivered = ParcelEvent::where('created_at', '>=', $start_month . '-01 00:00:00')
                ->where('created_at', '<=', $end_month . '-31 23:59:59')
                ->whereIn('title', ['parcel_delivered_event', 'parcel_partial_delivered_event', 'parcel_partial_delivered_event'])
                ->where('reverse_status', null)
                ->get();

            $data['totalParcelDelivered'] = $parcel_delivered->count();

            for ($i = 1; $i <= 12; $i++) {

                if ($i < 10) {
                    $i = str_pad($i, 2, "0", STR_PAD_LEFT);
                }

                $created_at = date('Y-' . $i);

                $start = $created_at . '-01';
                $end = $created_at . '-' . $this->getLastDateOfMonth(01);

                $merchant_parcels = $parcels->where('created_at', '>=', $start . ' 00:00:00' . '%');
                $merchant_parcels = $merchant_parcels->where('created_at', '<=', $end . ' 23:59:59' . '%');
                // dates
                $data['dates'][] = $created_at;

                // count
                $data['totalParcel'][] = $totalParcel = $merchant_parcels->count();
                $data['cancelled'][] = $cancelled = $merchant_parcels->where('status', 'cancel')->count();
                $data['deleted'][] = $deleted = $merchant_parcels->where('status', 'deleted')->count();
                $data['partially_delivered'][] = $partially_delivered = $merchant_parcels->where('is_partially_delivered', true)->count();
                $data['delivered'][] = $delivered = $merchant_parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
                $data['returned'][] = $returned = $merchant_parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
                $data['processing'][] = $totalParcel - ($cancelled + $deleted + $partially_delivered + $delivered + $returned);
            }

            $data['totalParcels'] = $parcels->count();
            $data['totalCancelled'] = $parcels->where('status', 'cancel')->count();
            $data['totalDeleted'] = $parcels->where('status', 'deleted')->count();
            $data['totalDelivered'] = $parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
            $data['totalPartialDelivered'] = $parcels->where('is_partially_delivered', true)->count();
            $data['totalReturned'] = $parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
            $data['totalProcessing'] = $data['totalParcels'] - ($data['totalCancelled'] + $data['totalDeleted'] + $data['totalDelivered'] + $data['totalPartialDelivered'] + $data['totalReturned']);

            $data['merchant_total_withdraw'] = MerchantWithdraw::whereIn('status', ['processed', 'pending'])->where('date', '>=', $start_month)->where('date', '<=', $end_month)->sum('amount');

            $profits = $this->profits($start_month . '-01', $end_month . '-31');
        elseif ($report_type == 'last_year'):
            $start_month = date('Y-' . '01', strtotime('-1 year'));
            $end_month = date('Y-' . '12', strtotime('-1 year'));

            $parcels = Parcel::where('created_at', '>=', $start_month . '-01 00:00:00')
                ->where('created_at', '<=', $end_month . '-31 23:59:59')
                ->when(!hasPermission('read_all_parcel'), function ($query) {
                    $query->where(function ($q) {
                        $q->where('branch_id', Sentinel::getUser()->branch_id)
                            ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                            ->orWhereNull('pickup_branch_id')
                            ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
                    });
                })
                ->latest()->get();

            $parcel_delivered = ParcelEvent::where('created_at', '>=', $start_month . '-01 00:00:00')
                ->where('created_at', '<=', $end_month . '-31 23:59:59')
                ->whereIn('title', ['parcel_delivered_event', 'parcel_partial_delivered_event', 'parcel_partial_delivered_event'])
                ->where('reverse_status', null)
                ->get();

            $data['totalParcelDelivered'] = $parcel_delivered->count();

            for ($i = 1; $i <= 12; $i++) {

                if ($i < 10) {
                    $i = str_pad($i, 2, "0", STR_PAD_LEFT);
                }

                $created_at = date('Y-' . $i, strtotime('-1 year'));

                $start = $created_at . '-01';
                $end = $created_at . '-' . $this->getLastDateOfMonth(01);

                $merchant_parcels = $parcels->where('created_at', '>=', $start . ' 00:00:00' . '%');
                $merchant_parcels = $merchant_parcels->where('created_at', '<=', $end . ' 23:59:59' . '%');

                // dates
                $data['dates'][] = $created_at;

                // count
                $data['totalParcel'][] = $totalParcel = $merchant_parcels->count();
                $data['cancelled'][] = $cancelled = $merchant_parcels->where('status', 'cancel')->count();
                $data['deleted'][] = $deleted = $merchant_parcels->where('status', 'deleted')->count();
                $data['partially_delivered'][] = $partially_delivered = $merchant_parcels->where('is_partially_delivered', true)->count();
                $data['delivered'][] = $delivered = $merchant_parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
                $data['returned'][] = $returned = $merchant_parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
                $data['processing'][] = $totalParcel - ($cancelled + $deleted + $partially_delivered + $delivered + $returned);
            }

            $data['totalParcels'] = $parcels->count();
            $data['totalCancelled'] = $parcels->where('status', 'cancel')->count();
            $data['totalDeleted'] = $parcels->where('status', 'deleted')->count();
            $data['totalDelivered'] = $parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
            $data['totalPartialDelivered'] = $parcels->where('is_partially_delivered', true)->count();
            $data['totalReturned'] = $parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
            $data['totalProcessing'] = $data['totalParcels'] - ($data['totalCancelled'] + $data['totalDeleted'] + $data['totalDelivered'] + $data['totalPartialDelivered'] + $data['totalReturned']);

            $data['merchant_total_withdraw'] = MerchantWithdraw::whereIn('status', ['processed', 'pending'])->where('date', '>=', $start_month)->where('date', '<=', $end_month)->sum('amount');

            $profits = $this->profits($start_month . '-01', $end_month . '-31');
        elseif ($report_type == 'lifetime'):

            $parcels = Parcel::when(!hasPermission('read_all_parcel'), function ($query) {
                $query->where(function ($q) {
                    $q->where('branch_id', Sentinel::getUser()->branch_id)
                        ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                        ->orWhereNull('pickup_branch_id')
                        ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
                });
            })->latest()->get();

            $parcel_delivered = ParcelEvent::whereIn('title', ['parcel_delivered_event', 'parcel_partial_delivered_event', 'parcel_partial_delivered_event'])
                ->where('reverse_status', null)
                ->get();

            $data['totalParcelDelivered'] = $parcel_delivered->count();

            $start_year = date('Y', strtotime($parcels->min('date')));
            $last_year = date('Y');

            if ($start_year - $last_year == 0):
                $start_year = $last_year;
                for ($i = 1; $i <= 12; $i++) {

                    if ($i < 10) {
                        $i = str_pad($i, 2, "0", STR_PAD_LEFT);
                    }

                    $created_at = date('Y-' . $i);

                    $start = $created_at . '-01';
                    $end = $created_at . '-' . $this->getLastDateOfMonth(01);

                    $merchant_parcels = $parcels->where('created_at', '>=', $start . ' 00:00:00' . '%');
                    $merchant_parcels = $merchant_parcels->where('created_at', '<=', $end . ' 23:59:59' . '%');
                    // dates
                    $data['dates'][] = $created_at;

                    // count
                    $data['totalParcel'][] = $totalParcel = $merchant_parcels->count();
                    $data['cancelled'][] = $cancelled = $merchant_parcels->where('status', 'cancel')->count();
                    $data['deleted'][] = $deleted = $merchant_parcels->where('status', 'deleted')->count();
                    $data['partially_delivered'][] = $partially_delivered = $merchant_parcels->where('is_partially_delivered', true)->count();
                    $data['delivered'][] = $delivered = $merchant_parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
                    $data['returned'][] = $returned = $merchant_parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
                    $data['processing'][] = $totalParcel - ($cancelled + $deleted + $partially_delivered + $delivered + $returned);
                }
            else:
                for ($i = $start_year; $i <= $last_year; $i++) {
                    $start = $i . '-01-01';
                    $end = $i . '-12-31';

                    $merchant_parcels = $parcels->where('created_at', '>=', $start . ' 00:00:00' . '%');
                    $merchant_parcels = $merchant_parcels->where('created_at', '<=', $end . ' 23:59:59' . '%');

                    $data['dates'][] = $i;

                    // count
                    $data['totalParcel'][] = $totalParcel = $merchant_parcels->count();
                    $data['cancelled'][] = $cancelled = $merchant_parcels->where('status', 'cancel')->count();
                    $data['deleted'][] = $deleted = $merchant_parcels->where('status', 'deleted')->count();
                    $data['partially_delivered'][] = $partially_delivered = $merchant_parcels->where('is_partially_delivered', true)->count();
                    $data['delivered'][] = $delivered = $merchant_parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
                    $data['returned'][] = $returned = $merchant_parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
                    $data['processing'][] = $totalParcel - ($cancelled + $deleted + $partially_delivered + $delivered + $returned);
                }
            endif;

            $data['totalParcels'] = $parcels->count();
            $data['totalCancelled'] = $parcels->where('status', 'cancel')->count();
            $data['totalDeleted'] = $parcels->where('status', 'deleted')->count();
            $data['totalDelivered'] = $parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
            $data['totalPartialDelivered'] = $parcels->where('is_partially_delivered', true)->count();
            $data['totalReturned'] = $parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
            $data['totalProcessing'] = $data['totalParcels'] - ($data['totalCancelled'] + $data['totalDeleted'] + $data['totalDelivered'] + $data['totalPartialDelivered'] + $data['totalReturned']);

            $data['merchant_total_withdraw'] = MerchantWithdraw::whereIn('status', ['processed', 'pending'])->where('date', '>=', $start_year . '-01-01')->where('date', '<=', $last_year . '-12-31')->sum('amount');

            $profits = $this->profits($start_year . '-01-01', $last_year . '-12-31');
        endif;

        $counts = $this->get_counts($parcels);

        return view('admin.dashboard.report', compact('data', 'counts', 'profits'))->render();
    }

    public function staffBalance()
    {
        $accounts = Account::get();

        $total_staff_account_balance = 0;

        foreach ($accounts as $account) {
            $total = $account->incomes()->sum('amount')
                + $account->fundReceives()->sum('amount')
                - $account->expenses()->sum('amount')
                - $account->fundTransfers()->sum('amount');

            $total_staff_account_balance += $total;
        }
        $data = $total_staff_account_balance;


        return $data;

    }

    public function deliverymanBalance()
    {
        // $deliverymen = DeliveryMan::active()->get();
        // $total_deliveryman_balance = 0;

        // foreach ($deliverymen as $deliveryman) {
        //     $balance = $deliveryman->balance($deliveryman->id);
        //     $total_deliveryman_balance += $balance;
        // }
        // $data = $total_deliveryman_balance;

        // return $data;
        $stats = DeliveryManAccount::selectRaw("
            SUM(CASE WHEN type = 'income' AND source NOT IN ('pickup_commission', 'parcel_delivery', 'opening_balance') 
                     THEN amount ELSE 0 END) as total_income,
            SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
        ")
            ->first();

        $total_balance = $stats->total_income - $stats->total_expense;

        return $total_balance;
    }

    public function merchantBalance()
    {
        // aggregate payable from parcels
        $parcelPayables = DB::table('parcels')
            ->join('merchants', 'merchants.id', '=', 'parcels.merchant_id')
            ->where('merchants.status', 'active') // use your active() scope condition here
            ->where(function ($query) {
                $query->where('parcels.is_partially_delivered', 1)
                    ->orWhereIn('parcels.status', ['delivered', 'delivered-and-verified']);
            })
            ->whereNull('parcels.withdraw_id')
            ->where('parcels.is_paid', false)
            ->sum('parcels.payable');

        // aggregate merchant_accounts
        $merchantAccounts = DB::table('merchant_accounts')
            ->join('merchants', 'merchants.id', '=', 'merchant_accounts.merchant_id')
            ->where('merchants.status', 'active')
            ->where(function ($query) {
                $query->whereIn('merchant_accounts.source', [
                    'previous_balance',
                    'cash_given_for_delivery_charge',
                    'parcel_return',
                    'paid_parcels_delivery_reverse',
                    'opening_balance'
                ])->orWhere(function ($query) {
                    $query->where('merchant_accounts.source', 'vat_adjustment')
                        ->whereIn('merchant_accounts.details', [
                            'govt_vat_for_parcel_return',
                            'govt_vat_for_parcel_return_reversed'
                        ]);
                });
            })
            ->whereNull('merchant_accounts.payment_withdraw_id')
            ->where('merchant_accounts.is_paid', false)
            ->selectRaw("
            SUM(CASE WHEN merchant_accounts.type = 'income' THEN merchant_accounts.amount ELSE 0 END) as total_income,
            SUM(CASE WHEN merchant_accounts.type = 'expense' THEN merchant_accounts.amount ELSE 0 END) as total_expense
        ")
            ->first();

        $total_merchant_balance = $parcelPayables
            + ($merchantAccounts->total_income ?? 0)
            - ($merchantAccounts->total_expense ?? 0);

        return format_price($total_merchant_balance);

    }


    public function totalPendingPayout()
    {
        $data = MerchantWithdraw::where('status', 'pending')->get()->sum('amount');

        return $data;

    }

    public function customDateRange(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $parcels = Parcel::where('created_at', '>=', $start_date . ' 00:00:00')
            ->where('created_at', '<=', $end_date . ' 23:59:59')
            ->when(!hasPermission('read_all_parcel'), function ($query) {
                $query->where(function ($q) {
                    $q->where('branch_id', Sentinel::getUser()->branch_id)
                        ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                        ->orWhereNull('pickup_branch_id')
                        ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
                });
            })
            ->latest()->get();

        $parcel_delivered = ParcelEvent::whereDate('created_at', '>=', $start_date . ' 00:00:00')
            ->whereDate('created_at', '<=', $end_date . ' 23:59:59')
            ->whereIn('title', ['parcel_delivered_event', 'parcel_partial_delivered_event', 'parcel_partial_delivered_event'])
            ->where('reverse_status', null)
            ->get();

        $data['totalParcelDelivered'] = $parcel_delivered->count();

        $start_date = date_create($start_date);
        $end_date = date_create($end_date);

        $different_days = date_diff($start_date, $end_date);

        $days = $different_days->format("%a");

        if ($days == 0):

            $data['dates'] = ["12AM - 02AM", "02AM - 04AM", "04AM - 06AM", "06AM - 08AM", "08AM - 10AM", "10AM - 12PM", "12PM - 02PM", "02PM - 04PM", "04PM - 06PM", "06PM - 08PM", "08PM - 10PM", "10PM - 12PM"];

            for ($i = 0; $i <= 11; $i++) {

                $j = $i * 2;

                $j = str_pad($j, 2, "0", STR_PAD_LEFT);
                $in = $j + 1;
                if ($in < 10) {
                    $in = str_pad($in, 2, "0", STR_PAD_LEFT);
                }

                //date range parcels
                $start = $request->start_date . ' ' . $j . ':00:00';
                $end = $request->start_date . ' ' . $in . ':59:59';

                $merchant_parcels = $parcels->where('created_at', '>=', $start);
                $merchant_parcels = $merchant_parcels->where('created_at', '<=', $end);

                // count
                $data['totalParcel'][] = $totalParcel = $merchant_parcels->count();
                $data['cancelled'][] = $cancelled = $merchant_parcels->where('status', 'cancel')->count();
                $data['deleted'][] = $deleted = $merchant_parcels->where('status', 'deleted')->count();
                $data['partially_delivered'][] = $partially_delivered = $merchant_parcels->where('is_partially_delivered', true)->count();
                $data['delivered'][] = $delivered = $merchant_parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
                $data['returned'][] = $returned = $merchant_parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
                $data['processing'][] = $totalParcel - ($cancelled + $deleted + $partially_delivered + $delivered + $returned);
            }
        else:
            for ($i = $days; $i >= 0; $i--) {
                if ($i < 10) {
                    $i = str_pad($i, 2, "0", STR_PAD_LEFT);
                }
                //date range parcels

                $created_at = date('Y-m-d', strtotime('-' . $i . ' days', strtotime($request->end_date)));

                $merchant_parcels = $parcels->where('created_at', '>=', $created_at . ' 00:00:00' . '%');
                $merchant_parcels = $merchant_parcels->where('created_at', '<=', $created_at . ' 23:59:59' . '%');

                // dates
                $data['dates'][] = $created_at;

                // count
                $data['totalParcel'][] = $totalParcel = $merchant_parcels->count();
                $data['cancelled'][] = $cancelled = $merchant_parcels->where('status', 'cancel')->count();
                $data['deleted'][] = $deleted = $merchant_parcels->where('status', 'deleted')->count();
                $data['partially_delivered'][] = $partially_delivered = $merchant_parcels->where('is_partially_delivered', true)->count();
                $data['delivered'][] = $delivered = $merchant_parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
                $data['returned'][] = $returned = $merchant_parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
                $data['processing'][] = $totalParcel - ($cancelled + $deleted + $partially_delivered + $delivered + $returned);
            }
        endif;
        $data['totalParcels'] = $parcels->count();
        $data['totalCancelled'] = $parcels->where('status', 'cancel')->count();
        $data['totalDeleted'] = $parcels->where('status', 'deleted')->count();
        $data['totalDelivered'] = $parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();
        $data['totalPartialDelivered'] = $parcels->where('is_partially_delivered', true)->count();
        $data['totalReturned'] = $parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
        $data['totalProcessing'] = $data['totalParcels'] - ($data['totalCancelled'] + $data['totalDeleted'] + $data['totalDelivered'] + $data['totalPartialDelivered'] + $data['totalReturned']);

        $data['merchant_total_withdraw'] = MerchantWithdraw::whereIn('status', ['processed', 'pending'])->where('date', '>=', $start_date)->where('date', '<=', $end_date)->sum('amount');

        $profits = $this->profits($request->start_date, $request->end_date);

        $counts = $this->get_counts($parcels);

        return view('admin.dashboard.report', compact('data', 'counts', 'profits'))->render();
    }

    public function profits($start, $end)
    {

        $startTime = $start . ' 00:00:00';
        $endTime = $end . ' 23:59:59';

        $parcelSources = ['parcel_delivery', 'parcel_return'];
        $returnVatDetails = ['govt_vat_for_parcel_return', 'govt_vat_for_parcel_return_reversed'];
        $deliverySources = ['pickup_commission', 'parcel_delivery', 'parcel_return'];

        // 1️⃣ GovtVat: income & expense
        $vat = GovtVat::whereBetween('date', [$start, $end])
            ->where('parcel_id', '!=', '')
            ->whereIn('source', $parcelSources)
            ->selectRaw("
            SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
            SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
        ")
            ->first();

        $data['total_vat'] = ($vat->total_income ?? 0) - ($vat->total_expense ?? 0);

        // 2️⃣ MerchantAccount: return income & expense
        $returnAccounts = MerchantAccount::whereBetween('date', [$start, $end])
            ->where(function ($q) use ($returnVatDetails) {
                $q->where('source', 'parcel_return')
                    ->orWhere(function ($q) use ($returnVatDetails) {
                        $q->where('source', 'vat_adjustment')
                            ->whereIn('details', $returnVatDetails);
                    });
            })
            ->selectRaw("
            SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as return_income,
            SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as return_expense
        ")
            ->first();

        $return_income = $returnAccounts->return_income ?? 0;
        $return_expense = $returnAccounts->return_expense ?? 0;

        // 3️⃣ Parcel: total_delivery_charge, fragile_charge, packaging_charge, price (all in one query)
        $parcelSums = Parcel::whereBetween('date', [$start, $end])
            ->withPermission()
            ->where(function ($q) {
                $q->where('is_partially_delivered', true)
                    ->orWhereIn('status', ['delivered', 'delivered-and-verified']);
            })
            ->selectRaw("
            SUM(total_delivery_charge) as total_delivery_charge,
            SUM(fragile_charge) as total_fragile_charge,
            SUM(packaging_charge) as total_packaging_charge,
            SUM(price) as total_price,
            SUM(payable) as total_payable_to_merchant

        ")
            ->first();

        $total_delivery_charge = $parcelSums->total_delivery_charge ?? 0;
        $data['total_charge_vat'] = $total_delivery_charge + $return_expense - $return_income;//total delivery charge of delivered parcel and total return charges with vat of returned parcels
        $data['total_fragile_charge'] = $parcelSums->total_fragile_charge ?? 0;
        $data['total_packaging_charge'] = $parcelSums->total_packaging_charge ?? 0;
        $data['total_payable_to_merchant'] = $parcelSums->total_price ?? 0;//total payable to merchant
        $data['total_cash_on_delivery'] = $parcelSums->total_price ?? 0; //total cash collection

        // 4️⃣ DeliveryManAccount: income & expense
        $deliveryAccount = DeliveryManAccount::whereBetween('date', [$start, $end])
            ->whereIn('source', $deliverySources)
            ->selectRaw("
            SUM(CASE WHEN type='income' THEN amount ELSE 0 END) as total_income,
            SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as total_expense,
            SUM(CASE WHEN source='cash_given_to_staff' AND type='expense' THEN amount ELSE 0 END) as total_cash_given_to_staff
        ")
            ->first();

        // $total_delivery_charge_income = $deliveryAccount->total_income ?? 0;
        // $total_delivery_charge_expense = $deliveryAccount->total_expense ?? 0;
        // $data['total_delivery_charge'] = $total_delivery_charge_expense - $total_delivery_charge_income;
        $data['total_paid_by_delivery_man'] = $deliveryAccount->total_cash_given_to_staff ?? 0;

        // 5️⃣ MerchantWithdraw: processed, pending, approved sums
        $merchantWithdraw = MerchantWithdraw::whereBetween('date', [$start, $end])
            ->selectRaw("
            SUM(CASE WHEN status IN ('processed','pending','approved') THEN amount ELSE 0 END) as total_paid,
            SUM(CASE WHEN status IN ('pending','approved') THEN amount ELSE 0 END) as pending_payouts,
            SUM(CASE WHEN status='processed' THEN amount ELSE 0 END) as processed_payouts
        ")
            ->first();

        // $data['total_paid_to_merchant'] = $merchantWithdraw->total_paid ?? 0;
        $data['pending_payouts'] = $merchantWithdraw->pending_payouts ?? 0;
        $data['processed_payouts'] = $merchantWithdraw->processed_payouts ?? 0;

        // 6️⃣ CompanyAccount: income & expense
        $companyAccount = CompanyAccount::whereBetween('date', [$start, $end])
            ->selectRaw("
            SUM(CASE WHEN source='delivery_charge_receive_from_merchant' AND type='income' AND merchant_id!='' THEN amount ELSE 0 END) as total_paid_by_merchant,
            SUM(CASE WHEN type='expense' AND create_type='user_defined' THEN amount ELSE 0 END) as total_expense_from_account
        ")
            ->first();

        $data['total_paid_by_merchant'] = $companyAccount->total_paid_by_merchant ?? 0;
        $data['total_expense_from_account'] = $companyAccount->total_expense_from_account ?? 0;

        // 7️⃣ Account: total_bank_opening_balance
        $data['total_bank_opening_balance'] = Account::whereBetween('created_at', [$startTime, $endTime])
            ->sum('balance');

        //merchant opening balances
        $merchantOpeningBalances = MerchantAccount::where('source', 'opening_balance')->sum('amount');

        // 8️⃣ Final calculations
        //$data['total_profit'] = (abs($data['total_charge_vat']) + $data['total_delivery_charge']) - $data['total_vat'];
        $data['total_profit'] = abs($data['total_charge_vat']) - $data['total_vat'];
        $data['current_payable'] = abs($data['total_payable_to_merchant'])
            + $data['total_paid_by_merchant'] + $merchantOpeningBalances
            - $data['processed_payouts']
            - $data['total_charge_vat'];
        // dd($data);
        return $data;
    }

    public function getLastDateOfMonth($month)
    {
        $date = date('Y') . '-' . $month . '-01';  //make date of month
        return date('t', strtotime($date));
    }


    public function oldBalance()
    {
        DB::beginTransaction();
        try {
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    public function mergeUpdate()
    {
        Artisan::call('database:backup');
    }


}
