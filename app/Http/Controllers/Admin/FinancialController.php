<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\FinancialDataTable;
use App\Http\Controllers\Controller;
use App\Models\Account\CompanyAccount;
use App\Models\Account\MerchantWithdraw;
use App\Models\Parcel;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function index(FinancialDataTable $dataTable, Request $request)
    {
        $startDate = $request->get('from_date');
        $endDate   = $request->get('to_date');

        // Base parcel query for financial calculations
        $parcelQuery = Parcel::query();
        if (!empty($startDate) && !empty($endDate)) {
            $parcelQuery->whereBetween('date', [$startDate, $endDate]);
        }

        // 1. Delivered Parcels Metrics
        $deliveredQuery   = (clone $parcelQuery)->whereIn('status', ['delivered', 'delivered-and-verified', 'partially-delivered']);
        $totalCod         = (float) (clone $deliveredQuery)->sum('price');
        $deliveryCharge   = (float) (clone $deliveredQuery)->sum('total_delivery_charge');
        $packagingCharge  = (float) (clone $deliveredQuery)->sum('packaging_charge');
        $fragileCharge    = (float) (clone $deliveredQuery)->sum('fragile_charge');

        // 2. Returned Parcels Metrics
        $returnQuery  = (clone $parcelQuery)->whereIn('status', ['returned-to-warehouse', 'return-assigned-to-merchant', 'returned-to-merchant', 'cancel', 'cancelled']);
        $returnCharge = (float) (clone $returnQuery)->sum('return_charge');

        // 3. Rider Commission (delivered delivery fee + return fee)
        $riderCommission = (float) (clone $deliveredQuery)->sum('delivery_fee') + (float) (clone $returnQuery)->sum('return_fee');

        // 4. Revenue (Delivery Charge + Return Charge + Packaging + Fragile)
        $revenue = $deliveryCharge + $returnCharge + $packagingCharge + $fragileCharge;

        // 5. Merchant Payable
        $withdrawQuery = MerchantWithdraw::whereIn('status', ['processed', 'pending', 'approved']);
        if (!empty($startDate) && !empty($endDate)) {
            $withdrawQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $totalPaidToMerchant = (float) $withdrawQuery->sum('amount');
        $merchantPayable = max(0, $totalCod - $deliveryCharge - $returnCharge - $totalPaidToMerchant);

        // 6. Expense (Office & General Expenses)
        $expenseQuery = CompanyAccount::where('type', 'expense')->where('create_type', 'user_defined');
        if (!empty($startDate) && !empty($endDate)) {
            $expenseQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $expense = (float) $expenseQuery->sum('amount');

        // 7. Net Profit
        $netProfit = $revenue - $riderCommission - $expense;

        $summary = [
            'revenue'          => $revenue,
            'total_cod'        => $totalCod,
            'delivery_charge'  => $deliveryCharge,
            'return_charge'    => $returnCharge,
            'rider_commission' => $riderCommission,
            'merchant_payable' => $merchantPayable,
            'expense'          => $expense,
            'net_profit'       => $netProfit,
        ];

        return $dataTable->render('admin.reports.financial_report', compact('summary'));
    }

    public function exportCsv(Request $request)
    {
        return (new FinancialDataTable())->csv();
    }
}
