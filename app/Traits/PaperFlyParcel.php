<?php

namespace App\Traits;

use App\Models\Account\CompanyAccount;
use App\Models\Account\DeliveryManAccount;
use App\Models\Account\GovtVat;
use App\Models\Account\MerchantAccount;
use App\Models\CustomerParcelSmsTemplates;
use App\Models\DeliveryMan;
use App\Models\Parcel;
use App\Models\ParcelEvent;
use App\Models\SmsTemplate;
use DB;

trait PaperFlyParcel {
    use SmsSenderTrait;
    public function createParcel($parcel, $selected_address) {
        try {
            $curl = curl_init();

            $data['merOrderRef']            = $parcel->parcel_no;
            $data['pickMerchantName']       = 'GreenX';
            $data['pickMerchantAddress']    = 'Lake City Concord Shopping Complex(6th Floor), Khilkhet, Dhaka-1229, Bangladesh';
            $data['pickMerchantThana']      = 'Khilkhet';
            $data['pickMerchantDistrict']   = 'Dhaka';
            $data['pickupMerchantPhone']    = '+8809678432444';
            $data['productSizeWeight']      = 'standard';
            if ($parcel->note != ''):
                $data['productBrief']       = $parcel->customer_invoice_no.' - '.$parcel->note;
            else:
                $data['productBrief']       = $parcel->customer_invoice_no;
            endif;
            $data['packagePrice']           = $parcel->price;
            $data['deliveryOption']         = 'regular';
            $data['custname']               = $parcel->customer_name;
            $data['custaddress']            = $parcel->customer_address;
            $data['customerThana']          = $selected_address->thana_name;
            $data['customerDistrict']       = $selected_address->district_name;
            $data['custPhone']              = $parcel->customer_phone_number;
            $data['max_weight']             = $parcel->weight;

            curl_setopt_array($curl, array(
                CURLOPT_URL             => 'https://api.paperflybd.com/OrderPlacement',
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_ENCODING        => '',
                CURLOPT_MAXREDIRS       => 10,
                CURLOPT_TIMEOUT         => 0,
                CURLOPT_FOLLOWLOCATION  => true,
                CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST   => 'POST',
                CURLOPT_POSTFIELDS      => json_encode($data),
                CURLOPT_HTTPAUTH        => CURLAUTH_BASIC,
                CURLOPT_USERPWD         => 'm116101' . ':' . 'Alam2030',
                CURLOPT_HTTPHEADER      => array(
                    'paperflykey:Paperfly_~La?Rj73FcLm'
                ),
            ));

            $response = curl_exec($curl);

            $response = json_decode($response);

            curl_close($curl);

            return $response;

        } catch (\Exception $e){
            return false;
        }
    }

    public function trackPaperflyParcel($parcel) {
        try {
            $curl = curl_init();

            $data['ReferenceNumber']            = $parcel->parcel_no;


            curl_setopt_array($curl, array(
                CURLOPT_URL             => 'https://api.paperflybd.com/API-Order-Tracking',
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_ENCODING        => '',
                CURLOPT_MAXREDIRS       => 10,
                CURLOPT_TIMEOUT         => 0,
                CURLOPT_FOLLOWLOCATION  => true,
                CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST   => 'POST',
                CURLOPT_POSTFIELDS      => json_encode($data),
                CURLOPT_HTTPAUTH        => CURLAUTH_BASIC,
                CURLOPT_USERPWD         => 'm116101' . ':' . 'Alam2030',
                CURLOPT_HTTPHEADER      => array(
                    'paperflykey:Paperfly_~La?Rj73FcLm'
                ),
            ));

            $response = curl_exec($curl);

            $response = json_decode($response);

            curl_close($curl);
            if ($response->response_code == 200):
                $track_status = $response->success->trackingStatus[0];
                if ($track_status->PickedForDeliveryTime != ''):
                    dd($track_status->PickedForDeliveryTime);
                endif;
            endif;

            return true;

        } catch (\Exception $e){
            return false;
        }
    }


    public function updatePaperflyParcel() {
        DB::beginTransaction();
        try {

            $ids = [];

            $parcels = Parcel::whereIn('status', ['received','transferred-received-by-branch', 'delivery-assigned', 're-schedule-delivery'])->where('tracking_number','!=','')->get();

            foreach ($parcels as $parcel):
                $data['ReferenceNumber']            = $parcel->parcel_no;
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.paperflybd.com/API-Order-Tracking',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                    CURLOPT_USERPWD => 'm116101' . ':' . 'Alam2030',
                    CURLOPT_HTTPHEADER => array(
                        'paperflykey:Paperfly_~La?Rj73FcLm'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $response = json_decode($response);

                if ($response->response_code == 200):
                    $track_status = $response->success->trackingStatus[0];
                    if ($track_status->PickedForDeliveryTime != ''):
                        if ($parcel->status != 'delivery-assigned' && $parcel->status != 're-schedule-delivery'):

                            $parcel->delivery_man_id    = 7;
                            $parcel->status             = 'delivery-assigned';
                            $this->parcelEvent($parcel, 'assign_delivery_man_event', $parcel->delivery_man_id, $track_status->PickedForDeliveryTime);
                        endif;
                    endif;
                    $parcel->date               = date('Y-m-d');
                    $parcel->save();
                endif;
            endforeach;
            DB::commit();
            return true;
        } catch (\Exception $e){
            DB::rollback();
            return false;
        }
    }

    public function parcelEvent($parcel, $title, $delivery_man ,$created_at = '')
    {
        $parcel_event                      = new ParcelEvent();

        $parcel_event->parcel_id           = $parcel->id;
        $parcel_event->delivery_man_id     = $delivery_man;
        $parcel_event->pickup_man_id       = $parcel->pickup_man_id;
        $parcel_event->user_id             = 2260;
        $parcel_event->title               = $title;
        $parcel_event->branch_id              = $branch ?? $parcel->branch_id;
        $parcel_event->third_party_id      = $parcel->third_party_id;

        if ($created_at != ''):
            $parcel_event->created_at = $created_at;
        endif;

        $delivery_person = DeliveryMan::where('id',$delivery_man)->first();
        $pickup_person   = DeliveryMan::where('id',$parcel->pickup_man_id)->first();

        // merchant sms start
        $sms_template = SmsTemplate::where('subject',$title)->first();
        if (!blank($sms_template)):
            if($sms_template->sms_to_merchant):
                $sms_body = str_replace('{merchant_name}', $parcel->merchant->company, $sms_template->content);
                $sms_body = str_replace('{parcel_id}', $parcel->parcel_no, $sms_body);
                $sms_body = str_replace('{pickup_date_time}',  date('M d, Y', strtotime($parcel->pickup_date)), $sms_body);
                $sms_body = str_replace('{re_pickup_date_time}', date('M d, Y', strtotime($parcel->pickup_date)), $sms_body);
                $sms_body = str_replace('{delivery_date_time}', date('M d, Y', strtotime($parcel->delivery_date)), $sms_body);
                $sms_body = str_replace('{re_delivery_date_time}', date('M d, Y', strtotime($parcel->delivery_date)), $sms_body);
                if ($created_at != ''):
                    $sms_body = str_replace('{current_date_time}', date('M d, Y h:i a', strtotime($created_at)), $sms_body);
                else:
                    $sms_body = str_replace('{current_date_time}', date('M d, Y h:i a'), $sms_body);
                endif;
                $sms_body = str_replace('{return_date_time}', date('M d, Y h:i a'), $sms_body);
                $sms_body = str_replace('{our_company_name}', __('app_name'), $sms_body);
                $sms_body = str_replace('{pickup_man_name}', @$pickup_person->user->first_name, $sms_body);
                $sms_body = str_replace('{pickup_man_phone}', @$pickup_person->phone_number, $sms_body);
                $sms_body = str_replace('{delivery_man_name}', @$delivery_person->user->first_name, $sms_body);
                $sms_body = str_replace('{delivery_man_phone}', @$delivery_person->phone_number, $sms_body);
                $sms_body = str_replace('{cancel_note}', @$parcel->cancelnote->cancel_note, $sms_body);
                $sms_body = str_replace('{price}', @$parcel->price, $sms_body);
                $sms_body = str_replace('{short_url}', @$parcel->short_url, $sms_body);


                $this->test($sms_body, $parcel->merchant->phone_number, $title, setting('active_sms_provider'), $sms_template->masking);
            endif;
            //merchant sms end
        endif;

        //customer sms start
        if ($this->checkLocation($parcel, $title)):
            $customer_sms_template = CustomerParcelSmsTemplates::where('subject',$title)->first();
            if (!blank($customer_sms_template)):
                if($customer_sms_template->sms_to_customer):
                    $sms_body = str_replace('{merchant_name}', $parcel->merchant->company, $customer_sms_template->content);
                    $sms_body = str_replace('{parcel_id}', $parcel->parcel_no, $sms_body);
                    $sms_body = str_replace('{pickup_date_time}',  date('M d, Y', strtotime($parcel->pickup_date)), $sms_body);
                    $sms_body = str_replace('{re_pickup_date_time}', date('M d, Y', strtotime($parcel->pickup_date)), $sms_body);
                    $sms_body = str_replace('{delivery_date_time}', date('M d, Y', strtotime($parcel->delivery_date)), $sms_body);
                    $sms_body = str_replace('{re_delivery_date_time}', date('M d, Y', strtotime($parcel->delivery_date)), $sms_body);
                    $sms_body = str_replace('{current_date_time}', date('M d, Y h:i a'), $sms_body);
                    $sms_body = str_replace('{return_date_time}', date('M d, Y h:i a'), $sms_body);
                    $sms_body = str_replace('{our_company_name}', __('app_name'), $sms_body);
                    $sms_body = str_replace('{pickup_man_name}', @$pickup_person->user->first_name, $sms_body);
                    $sms_body = str_replace('{pickup_man_phone}', @$pickup_person->phone_number, $sms_body);
                    $sms_body = str_replace('{delivery_man_name}', @$delivery_person->user->first_name, $sms_body);
                    $sms_body = str_replace('{delivery_man_phone}', @$delivery_person->phone_number, $sms_body);
                    $sms_body = str_replace('{cancel_note}', @$parcel->cancelnote->cancel_note, $sms_body);
                    $sms_body = str_replace('{price}', @$parcel->price, $sms_body);
                    $sms_body = str_replace('{short_url}', @$parcel->short_url, $sms_body);
                    //send sms
                    $this->test($sms_body, $parcel->customer_phone_number, $title, setting('active_sms_provider'), $customer_sms_template->masking);
                endif;
                //customer sms end
            endif;
        endif;
        $parcel_event->save();
        return true;
    }

    public function checkLocation($parcel, $title){
        if($parcel->location == 'sub_urban_area' || $parcel->location == 'sub_city'):
            if ($title != 'assign_delivery_man_event'):
                return false;
            endif;
        endif;

        return true;
    }

    public function incomeExpenseManage($parcel)
    {
        $delivery_man = DeliveryMan::find($parcel->delivery_man_id);

        // merchant account entry
        $merchant_account               = new MerchantAccount();
        $merchant_account->parcel_id    = $parcel->id;
        $merchant_account->merchant_id  = $parcel->merchant_id;
        $merchant_account->date         = date('Y-m-d');
        $merchant_account->source       = 'parcel_delivery';
        $merchant_account->amount       = $parcel->price;
        $merchant_account->type         = 'income';
        $merchant_account->details      = 'parcel_cod_collection_from_customer';
        $merchant_account->save();

        $vat                   = $parcel->vat ?? 0.00;
        $total_delivery_charge = $parcel->total_delivery_charge;
        $total_vat             = floor($total_delivery_charge / 100 * $vat);
        $total_delivery_charge = $total_delivery_charge - $total_vat;


        $merchant_account               = new MerchantAccount();
        $merchant_account->parcel_id    = $parcel->id;
        $merchant_account->merchant_id  = $parcel->merchant_id;
        $merchant_account->date         = date('Y-m-d');
        $merchant_account->source       = 'delivery_charge';
        $merchant_account->amount       = floor($total_delivery_charge);
        $merchant_account->type         = 'expense';
        $merchant_account->details      = 'parcel_total_delivery_charge';
        $merchant_account->save();

        $merchant_account               = new MerchantAccount();
        $merchant_account->parcel_id    = $parcel->id;
        $merchant_account->merchant_id  = $parcel->merchant_id;
        $merchant_account->date         = date('Y-m-d');
        $merchant_account->source       = 'vat_adjustment';
        $merchant_account->amount       = floor($total_vat);
        $merchant_account->type         = 'expense';
        $merchant_account->details      = 'govt_vat_for_parcel_delivery';
        $merchant_account->save();

        // Vat
        $govt_vat                   = new GovtVat();
        $govt_vat->amount           = floor($total_vat);
        $govt_vat->source           = 'parcel_delivery';
        $govt_vat->parcel_id        = $parcel->id;
        $govt_vat->date             = date('Y-m-d');
        $govt_vat->details          = 'parcel_successfully_delivered_vat';
        $govt_vat->type             = 'income';
        $govt_vat->save();

        // delivery man account entry for cod
        $delivery_account                  = new DeliveryManAccount();
        $delivery_account->delivery_man_id = $delivery_man->id;
        $delivery_account->parcel_id       = $parcel->id;
        $delivery_account->date            = date('Y-m-d');
        $delivery_account->source          = 'cash_collection';
        $delivery_account->amount          = $parcel->price;
        $delivery_account->type            = 'income';
        $delivery_account->details         = 'parcel_cod_collection_from_customer';
        $delivery_account->save();

        $company_account                    = new CompanyAccount();
        $company_account->parcel_id         = $parcel->id;
        $company_account->delivery_man_id   = $delivery_man->id;
        $company_account->date              = date('Y-m-d');
        $company_account->source            = 'parcel_delivery';
        $company_account->type              = 'expense';
        $company_account->details           = 'parcel_delivery_commission_to_delivery_man';
        $company_account->amount            = $parcel->delivery_fee;
        $company_account->save();

        // Delivery fee entry
        $delivery_account                   = new DeliveryManAccount();
        $delivery_account->delivery_man_id  = $delivery_man->id;
        $delivery_account->parcel_id        = $parcel->id;
        $delivery_account->company_account_id = $company_account->id;
        $delivery_account->date            = date('Y-m-d');
        $delivery_account->source          = 'parcel_delivery';
        $delivery_account->amount          = $parcel->delivery_fee;
        $delivery_account->type            = 'expense';
        $delivery_account->details         = 'parcel_delivery_commission_received';
        $delivery_account->save();
        return true;
    }


}
