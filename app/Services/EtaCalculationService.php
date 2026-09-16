<?php

namespace App\Services;

use App\Models\Parcel;
use App\Models\DeliveryTimeSet;
use Carbon\Carbon;

class ETACalculationService
{
    public function updateParcelETA(Parcel $parcel)
    {
        
        $deliverySettings = DeliveryTimeSet::find(1); 
        $offdays = $deliverySettings && $deliverySettings->offday ? explode(',', $deliverySettings->offday) : [];
        
        $deliveryDate = Carbon::now();
        $deliveryTime = 'Pending Assignment';

        switch ($parcel->status) {
            case 'pending':
                  
                    $baseDays = 2; 
                    if ($parcel->parcel_type == 'same_day' || $parcel->parcel_type == 'inside_city') {
                        $baseDays = $deliverySettings ? $deliverySettings->inside_city_days : 1;
                    } elseif ($parcel->parcel_type == 'sub_city') {
                        $baseDays = $deliverySettings ? $deliverySettings->sub_city_days : 2;
                    } elseif ($parcel->parcel_type == 'outside_city' || $parcel->parcel_type == 'sub_urban_area') {
                        $baseDays = $deliverySettings ? $deliverySettings->outside_city_days : 3;
                    }

                    $deliveryDate = Carbon::now()->addDays($baseDays);

                  
                    $dayName = $deliveryDate->format('l');
                    if (in_array($dayName, $offdays)) {
                        $deliveryDate->addDay();
                    }

                    if ($deliverySettings && $deliverySettings->working_hours_start) {
                        $deliveryTime = $deliverySettings->working_hours_start . ' - ' . $deliverySettings->working_hours_end;
                    }
                    break;

                case 'transfered-to-branch':
                case 'transferred-to-branch':
                 
                    if ($parcel->transfer_to_branch_id == $parcel->destination_branch_id) {
                        $deliveryDate = Carbon::now()->addDay();
                        $dayName = $deliveryDate->format('l');
                        
                        if (in_array($dayName, $offdays)) {
                            $deliveryDate->addDay();
                        }
                    } 
                   
                    else {
                        if ($parcel->delivery_date) {
                            $deliveryDate = Carbon::parse($parcel->delivery_date);
                        }
                    }
                    break;


                case 'delivery-assigned':
                   
                    $deliveryDate = Carbon::now(); 
             
                    $currentHour = Carbon::now()->hour;
                    if ($currentHour < 12) {
                        $deliveryTime = '10:00 AM - 2:00 PM'; 
                    } else {
                        $deliveryTime = '2:00 PM - 7:00 PM';  
                    }
                    break;

                case 'reschedule-delivery':
                   
                    if ($parcel->delivery_date) {
                        $deliveryDate = Carbon::parse($parcel->delivery_date);
                    }
                    break;

                case 'cancel':
                case 'delivered':
                    
                    $deliveryDate = null;
                    $deliveryTime = 'N/A';
                    break;
                    
                default:
                   
                    if ($parcel->delivery_date) {
                        $deliveryDate = Carbon::parse($parcel->delivery_date);
                    }
                    $deliveryTime = $parcel->delivery_time ?? 'Pending';
                    break;
            }

          
        $parcel->delivery_date = $deliveryDate ? $deliveryDate->format('Y-m-d') : null;
        $parcel->delivery_time = $deliveryTime;
        $parcel->save();
        
      

        return $parcel;
    }
}
