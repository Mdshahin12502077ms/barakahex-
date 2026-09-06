<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerParcelSmsTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_parcel_sms_templates', function (Blueprint $table) {
            $table->id();
            $table->string('subject')->nullable();
            $table->longText('content')->nullable();
            $table->tinyInteger('sms_to_customer')->default(0)->comment('0 inactive, 1 active');
            $table->tinyInteger('masking')->default(0)->comment('0 inactive, 1 active');
            $table->timestamps();
        });

        \DB::statement("INSERT INTO `customer_parcel_sms_templates` (`id`, `subject`, `content`, `sms_to_customer`, `created_at`, `updated_at`) VALUES
        (1, 'parcel_create_event', 'Your parcel {parcel_id} from {merchant_name} has been created successfully at {current_date_time}.\r\n{our_company_name}', 0, NULL, NULL),
        (2, 'assign_pickup_man_event', 'A pickup man has been assigned for pickup your parcel {parcel_id} from {merchant_name} at {pickup_date_time}. Pickup man: {pickup_man_name}, {pickup_man_phone}. \r\n{our_company_name}', 0, NULL, NULL),
        (3, 'parcel_re_schedule_pickup_event', 'Your parcel {parcel_id} has been re-schedule for pickup at {re_pickup_date_time} from {merchant_name}. Pickup man: {pickup_man_name}, {pickup_man_phone}.\r\n{our_company_name}', 0, NULL, NULL),
        (4, 'parcel_received_by_pickup_man_event', 'A pickup man have collected your parcel {parcel_id} from {merchant_name}. We will inform you while dispatching the parcel for delivery. \r\n{our_company_name}', 0, NULL, NULL),
        (5, 'parcel_received_event', 'We have collected your parcel {parcel_id} from {merchant_name}. We will inform you while dispatching the parcel for delivery. \r\n{our_company_name}', 0, NULL, NULL),
        (6, 'parcel_transferred_to_branch_assigned_event', 'Your parcel {parcel_id} has been assigned to transfer on another branch.\r\n{our_company_name}', 0, NULL, NULL),
        (7, 'parcel_transferred_to_branch_event', 'Your parcel {parcel_id} has been successfully transfer on another branch.\r\n{our_company_name}', 0, NULL, NULL),
        (8, 'assign_delivery_man_event', 'Pre-Delivery Info:\r\nParcel No: {parcel_id}\r\nCompany: {merchant_name}\r\nPrice: BDT {price} \r\nDelivery Person: {delivery_man_name}\r\nContact: {delivery_man_phone}. For Track: {short_url}\r\n{our_company_name}', 0, NULL, NULL),
        (9, 'parcel_re_schedule_delivery_event', 'Your parcel {parcel_id} has been re-scheduled for delivery at {re_delivery_date_time}.\r\nRe-schedule delivery Info:\r\nParcel No: {parcel_id}\r\nCompany: {merchant_name}\r\nPrice: BDT {price} \r\nDelivery Person: {delivery_man_name}\r\nContact: {delivery_man_phone}\r\n{our_company_name}', 0, NULL, NULL),
        (10, 'parcel_delivered_event', 'Your parcel {parcel_id} has been successfully delivered to you from {merchant_name} at {current_date_time}.\r\n{our_company_name}', 0, NULL, NULL),
        (11, 'parcel_return_to_wirehouse', 'Your parcel {parcel_id} from {merchant_name} has been return to {our_company_name} at {current_date_time}.\r\n{our_company_name}', 0, NULL, NULL),
        (12, 'parcel_return_assign_to_merchant_event', 'Your parcel {parcel_id} has been assigned for returning to {merchant_name} at {current_date_time}. Delivery Man: {delivery_man_name}, {delivery_man_phone}.\r\n{our_company_name}', 0, NULL, NULL),
        (13, 'parcel_returned_to_merchant_event', 'Your parcel {parcel_id} has been successfully returned to {merchant_name} at {current_date_time}.\r\n{our_company_name}', 0, NULL, NULL),
        (14, 'parcel_cancel_event', 'Your parcel {parcel_id} from {merchant_name} has been cancelled for delivery at {current_date_time}. Reason: {cancel_note}\r\n{our_company_name}', 0, NULL, NULL),
        (15, 'parcel_re_request_event', 'Your parcel {parcel_id} from {merchant_name} has been re-requested for delivery at {current_date_time}.\r\n{our_company_name}', 0, NULL, NULL),
        (16, 'parcel_update_event', 'Your parcel {parcel_id} from {merchant_name} has been updated at {current_date_time}.\r\n{our_company_name}', 0, NULL, NULL),
        (17, 'delivery_confirm_otp', 'Please share, this OTP: {otp} with the delivery hero to confirm your parcel successfully delivered to you. Do not share this OTP with anyone else.\r\n{our_company_name}', 0, NULL, NULL)");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_parcel_sms_templates');
    }
}
