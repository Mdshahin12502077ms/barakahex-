<?php

namespace App\Repositories\Admin;

use App\Models\Parcel;
use App\Models\WebsiteStatistic;
use App\Models\WebsiteStatisticLanguage;
use App\Models\Account\CompanyAccount;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\DB;

class AdvancedAnalyticsRepository
{
   
public function AdvanceOpretion($request){
   $query = Parcel::query();
 
   if($request->filled('from_date') && $request->filled('to_date')){
     $query->whereBetween('created_at', [
         $request->from_date . ' 00:00:00', 
         $request->to_date . ' 23:59:59'
     ]);
   }
 
   $total_parcel = (clone $query)->count()??0;
   $delivered_parcel = (clone $query)->where('status', 'delivered')->count()??0;
   $failed_parcel = (clone $query)->where('status', 'cancel')->count()??0;
   $pending_parcel = (clone $query)->where('status', 'pending')->count()??0;
   $returned_parcel = (clone $query)->where('status', 'return-to-merchant')->count()??0;
  
   $delivery_success_rate = 0;
   if($total_parcel > 0) {
       $delivery_success_rate = round(($delivered_parcel / $total_parcel) * 100, 2);
   }
   
   $hub_performance = (clone $query)->select(
       'branch_id', 
       DB::raw('COUNT(*) as total'), 
       DB::raw('COUNT(CASE WHEN status = "delivered" THEN 1 END) as delivered'), 
       DB::raw('COUNT(CASE WHEN status = "cancel" THEN 1 END) as cancel')
   )->groupBy('branch_id')->get();
   
 
   return compact(
       'total_parcel', 
       'delivered_parcel', 
       'failed_parcel', 
       'pending_parcel', 
       'returned_parcel', 
       'delivery_success_rate', 
       'hub_performance'
   );
}
public function riderAnalytics($request){
    $query = Parcel::query()->whereNotNull('delivery_man_id');
    
    if($request->filled('from_date') && $request->filled('to_date')){
        $query->whereBetween('created_at', [
            $request->from_date . ' 00:00:00', 
            $request->to_date . ' 23:59:59'
        ]);
    }

    $assigned_parcels = (clone $query)->count() ?? 0;
    $delivered_parcels = (clone $query)->where('status', 'delivered')->count() ?? 0;
    $failed_parcels = (clone $query)->where('status', 'cancel')->count() ?? 0;
    $returned_parcels = (clone $query)->where('status', 'return-to-merchant')->count() ?? 0;
    
    $success_rate = 0;
    if($assigned_parcels > 0) {
        $success_rate = round(($delivered_parcels / $assigned_parcels) * 100, 2);
    }
    
    $avg_delivery_time = 0; 
    
    $cod_collection = (clone $query)->where('status', 'delivered')->sum('price') ?? 0;
    $rider_commission = (clone $query)->where('status', 'delivered')->sum('delivery_fee') ?? 0;

    $rider_performance_query = (clone $query)->select(
        'delivery_man_id', 
        DB::raw('COUNT(*) as assigned'), 
        DB::raw('COUNT(CASE WHEN status = "delivered" THEN 1 END) as delivered'), 
        DB::raw('COUNT(CASE WHEN status = "cancel" THEN 1 END) as failed'),
        DB::raw('COUNT(CASE WHEN status = "return-to-merchant" THEN 1 END) as returned'),
        DB::raw('SUM(CASE WHEN status = "delivered" THEN price ELSE 0 END) as cod_collected'),
        DB::raw('SUM(CASE WHEN status = "delivered" THEN delivery_fee ELSE 0 END) as commission')
    )->with('deliveryMan.user')->groupBy('delivery_man_id')->get();
    
    $rider_performance = [];
    foreach($rider_performance_query as $hub) {
        $name = __('unknown');
        $phone = '';
        if($hub->deliveryMan) {
            $phone = $hub->deliveryMan->phone_number ?? '';
            if($hub->deliveryMan->user) {
                $name = $hub->deliveryMan->user->first_name . ' ' . $hub->deliveryMan->user->last_name;
                if(empty($phone)) $phone = $hub->deliveryMan->user->phone ?? '';
            }
        }
        
        $riderSuccessRate = $hub->assigned > 0 ? round(($hub->delivered / $hub->assigned) * 100, 1) : 0;
        
        $rider_performance[] = (object)[
            'name' => $name,
            'phone' => $phone,
            'assigned' => $hub->assigned,
            'delivered' => $hub->delivered,
            'failed' => $hub->failed,
            'returned' => $hub->returned,
            'success_rate' => $riderSuccessRate,
            'cod_collected' => $hub->cod_collected,
            'commission' => $hub->commission,
        ];
    }
    
    return compact(
        'assigned_parcels', 
        'delivered_parcels', 
        'failed_parcels', 
        'returned_parcels', 
        'success_rate', 
        'avg_delivery_time', 
        'cod_collection', 
        'rider_commission', 
        'rider_performance'
    );
    }
    
    public function merchantAnalyticsData($request) {
        $query = Parcel::query();
        
        if($request->filled('start_date') && $request->filled('end_date')){
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00', 
                $request->end_date . ' 23:59:59'
            ]);
        }
        
        $total_orders = (clone $query)->count() ?? 0;
        $delivered_orders = (clone $query)->whereIn('status', ['delivered', 'delivered-and-verified'])->count() ?? 0;
        $failed_orders = (clone $query)->whereIn('status', ['cancel', 'cancelled'])->count() ?? 0;
        $returned_orders = (clone $query)->whereIn('status', ['returned', 'return-to-merchant', 'return-to-merchant-and-verified'])->count() ?? 0;
        
        $return_rate = 0;
        if($total_orders > 0) {
            $return_rate = round(($returned_orders / $total_orders) * 100, 2);
        }
        
        $cod_amount = (clone $query)->sum('price') ?? 0;
        $settlement_amount = (clone $query)->sum('payable') ?? 0;
        $merchant_revenue = (clone $query)->sum('payable') ?? 0; // Using payable as revenue
        
        return compact(
            'total_orders',
            'delivered_orders',
            'failed_orders',
            'returned_orders',
            'return_rate',
            'cod_amount',
            'settlement_amount',
            'merchant_revenue'
        );
    }

    public function financialAnalytics($request){
        $parcel=Parcel::query();
        if($request->filled('start_date') && $request->filled('end_date')){
            $parcel->whereBetween('created_at', [
                $request->start_date . ' 00:00:00', 
                $request->end_date . ' 23:59:59'
            ]);
        }

     
       $total_delivery_charge = (clone $parcel)->sum('total_delivery_charge') ?? 0;
       $cod_charge            = (clone $parcel)->sum('cod_charge') ?? 0;
       $return_charge         = (clone $parcel)->sum('return_charge') ?? 0;
       
       // অন্যান্য সম্ভাব্য আয় (যাতে কোনো কিছুই বাদ না যায়)
       $packaging_charge      = (clone $parcel)->sum('packaging_charge') ?? 0;
       $fragile_charge        = (clone $parcel)->sum('fragile_charge') ?? 0;
       
       $rider_commission      = ((clone $parcel)->sum('delivery_fee') ?? 0) 
                              + ((clone $parcel)->sum('pickup_fee') ?? 0) 
                              + ((clone $parcel)->sum('return_fee') ?? 0);
                              
       $merchant_payable      = (clone $parcel)->sum('payable') ?? 0;
       
       $company_expense = CompanyAccount::where('type', 'expense')->where('create_type', 'user_defined');
       if($request->filled('start_date') && $request->filled('end_date')){
           $company_expense->whereBetween('created_at', [
               $request->start_date . ' 00:00:00', 
               $request->end_date . ' 23:59:59'
           ]);
       }
       $total_company_expense = $company_expense->sum('amount') ?? 0;
       
       $operating_expense     = $rider_commission + $total_company_expense;
       $total_revenue         = $total_delivery_charge + $cod_charge + $return_charge + $packaging_charge + $fragile_charge;
       $net_profit            = $total_revenue - $operating_expense;
       
       return compact(
           'total_delivery_charge',
           'cod_charge',
           'return_charge',
           'rider_commission',
           'merchant_payable',
           'operating_expense',
           'total_revenue',
           'net_profit'
       );
    }
}
