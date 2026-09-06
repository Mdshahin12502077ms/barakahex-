<?php

namespace App\Repositories;
use App\Models\CustomerParcelSmsTemplates;
use App\Models\DeliveryMan;
use App\Models\Parcel;
use App\Models\ParcelEvent;
use App\Models\PercelMovement;
use App\Repositories\Interfaces\BulkInterface;
use App\Traits\SmsSenderTrait;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\DB;

class BulkRepository implements BulkInterface {

    use SmsSenderTrait;

    public function get($id)
    {
        return Parcel::find($id);
    }

    public function bulkAssign($data)
    {
        DB::beginTransaction();
        try {
            foreach ($data['parcel_list'] as $parcel_id):

                $parcel                     = $this->get($parcel_id);
                $previous_status            = $parcel->status;
                if ($previous_status == 'received' || $previous_status == 'transferred-received-by-branch' ||
                    $previous_status == 'delivery-assigned' || $previous_status == 're-schedule-delivery'):
                    $parcel->status             = ($previous_status == 'received' || $previous_status == 'transferred-received-by-branch') ? 'delivery-assigned' : 're-schedule-delivery';
                    $parcel->delivery_man_id    = $data['delivery_man'];
                    $parcel->delivery_fee       = DeliveryMan::find($data['delivery_man'])->delivery_fee;
                    $parcel->save();

                    $parcel_event                   = new ParcelEvent();
                    $parcel_event->parcel_id        = $parcel_id;
                    $parcel_event->delivery_man_id  = $data['delivery_man'];
                    $parcel_event->pickup_man_id    = $parcel->pickup_man_id;
                    $parcel_event->user_id          = Sentinel::getUser()->id;
                    $parcel_event->cancel_note      = ($previous_status == 'received' || $previous_status == 'transferred-received-by-branch') ? '': __('delivery_man_changed');
                    $parcel_event->title            = ($previous_status == 'received' || $previous_status == 'transferred-received-by-branch') ? 'assign_delivery_man_event' : 'parcel_re_schedule_delivery_event';
                    $parcel_event->save();
                endif;

            endforeach;

            if ($data['notify_customer'] == 'notify'):
                $this->parcelEvent($parcel, 'assign_delivery_man_event', $data['delivery_man'], '', '');
            endif;

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function bulkTransferSave($data)
    {
        DB::beginTransaction();
        try {
            foreach ($data['parcel_list'] as $parcel_id):
                $parcel                             = $this->get($parcel_id);
                $parcel->status                     = 'transferred-to-branch';
                $parcel->transfer_to_branch_id      = $data['branch'];
                $parcel->transfer_delivery_man_id   = $data['delivery_man'];
                $parcel->save();
                
               if($parcel){
                 PercelMovement::create([
                    'parcel_id'        => $parcel_id,
                    'tracking_number'  => $parcel->parcel_no,
                    'from_branch_id'   => $parcel->branch_id,
                    'to_branch_id'     => $data['branch'],
                    'sent_by'          => Sentinel::getUser()->id,
                    'delivery_man_id'  => $data['delivery_man'],
                    'sent_at'          => now(),
                    'status'           => 'in_transit',
                    'note'             => 'Bulk transfer to branch/hub',
                ]);
               }





                $parcel_event                           = new ParcelEvent();
                $parcel_event->parcel_id                = $parcel_id;
                $parcel_event->pickup_man_id            = $parcel->pickup_man_id;
                $parcel_event->delivery_man_id          = $parcel->delivery_man_id;
                $parcel_event->transfer_delivery_man_id = $data['delivery_man'];
                $parcel_event->user_id                  = Sentinel::getUser()->id;
                $parcel_event->branch_id                = $data['branch'];
                $parcel_event->title                    = 'parcel_transferred_to_branch_assigned_event';
                $parcel_event->save();
            endforeach;

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function bulkTransferReceive($data)
    {
        DB::beginTransaction();
        try {
            foreach ($data['parcel_list'] as $parcel_id):

                $parcel                             = $this->get($parcel_id);
                $parcel->status                     = 'transferred-received-by-branch';
                $parcel->branch_id                  = $parcel->transfer_to_branch_id;
                $parcel->transfer_delivery_man_id   = $data['delivery_man'];
                $parcel->save();

                if($parcel->status == 'transferred-received-by-branch'){
                     
                $activeMovement = PercelMovement::where('parcel_id', $parcel_id)
                    ->where('status', 'in_transit')
                    ->latest()
                    ->first();
                if ($activeMovement) {
                    $activeMovement->update([
                        'received_by' => Sentinel::getUser()->id,
                        'received_at' => now(),
                        'status'      => 'received',
                    ]);
                }
                }

                $parcel_event                           = new ParcelEvent();
                $parcel_event->parcel_id                = $parcel_id;
                $parcel_event->pickup_man_id            = $parcel->pickup_man_id;
                $parcel_event->delivery_man_id          = $parcel->delivery_man_id;
                $parcel_event->transfer_delivery_man_id = $data['delivery_man'];
                $parcel_event->user_id                  = Sentinel::getUser()->id;
                $parcel_event->branch_id                = $parcel->transfer_to_branch_id;
                $parcel_event->title                    = 'parcel_transferred_to_branch_event';
                $parcel_event->save();

            endforeach;

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    public function parcelEvent($parcel, $title, $delivery_man = '', $pickup_man = '', $return_delivery_man = '', $cancel_note = '')
    {
        // if (($parcel->location != 'sub_urban_area' && $title != 'assign_delivery_man_event') || ($parcel->location != 'sub_city' && $title != 'assign_delivery_man_event')):
            $delivery_person = DeliveryMan::where('id',$parcel->delivery_man_id)->first();
            //customer sms start
            $customer_sms_template = CustomerParcelSmsTemplates::where('subject',$title)->first();
            $sms_body = str_replace('{merchant_name}', $parcel->merchant->company, $customer_sms_template->content);
            $sms_body = str_replace('{parcel_id}', $parcel->parcel_no, $sms_body);
            $sms_body = str_replace('{delivery_date_time}', date('M d, Y', strtotime($parcel->delivery_date)), $sms_body);
            $sms_body = str_replace('{current_date_time}', date('M d, Y h:i a'), $sms_body);
            $sms_body = str_replace('{our_company_name}', __('app_name'), $sms_body);
            $sms_body = str_replace('{delivery_man_name}', @$delivery_person->user->first_name, $sms_body);
            $sms_body = str_replace('{delivery_man_phone}', @$delivery_person->phone_number, $sms_body);
            $sms_body = str_replace('{price}', @$parcel->price, $sms_body);

            $this->test($sms_body, $parcel->customer_phone_number, $title, setting('active_sms_provider'),  $customer_sms_template->masking);
            //customer sms end
        // endif;
    }

    public function bulkPickupAssign($data)
    {
        DB::beginTransaction();
        try {
            foreach ($data['parcels'] as $parcel_id):

                $parcel                                = $this->get($parcel_id);
                if ($parcel->status == 'pending'):
                    $parcel->pickup_man_id             = $data['pickup_man'];
                    $parcel->status                    = 'pickup-assigned';
                    $parcel->save();

                    $parcel_event                      = new ParcelEvent();
                    $parcel_event->parcel_id           = $parcel_id;
                    $parcel_event->pickup_man_id       = $parcel->pickup_man_id;
                    $parcel_event->user_id             = Sentinel::getUser()->id;
                    $parcel_event->title               = 'assign_pickup_man_event';
                    $parcel_event->save();
                else:
                    continue;
                endif;
            endforeach;
            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
}
