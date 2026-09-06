<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->string('parcel_no', 50)->comment('used for each merchant invoice tracking');
            $table->string('tracking_number', 50)->comment('used for paperfly parcel tracking');
            $table->string('short_url', 50)->comment('short-link for tracking');
            $table->string('packaging', 191)->default('no')->comment('1 yes, 0 no');
            $table->decimal('packaging_charge', 8, 2)->nullable()->comment('recorded for future reference');
            $table->tinyInteger('fragile')->default(0)->comment('recorded for previous reference');
            $table->decimal('fragile_charge', 8, 2)->nullable()->comment('recorded for future reference');
            $table->string('open_box', 191)->default('no')->comment('1 yes, 0 no');
            $table->string('home_delivery', 191)->default('no')->comment('1 yes, 0 no');
            $table->string('parcel_type', 191)->nullable()->comment('parcel type or parcel service');
            $table->string('weight', 191)->nullable();
            $table->decimal('charge', 8, 2)->nullable()->comment('weight delivery charge from merchant table');
            $table->decimal('cod_charge', 8, 2)->nullable()->comment('COD charge direct from merchant table');
            $table->decimal('vat', 8, 2)->nullable()->comment('customer wise vat from merchant table');
            $table->string('location', 191)->nullable()->comment('for future reference');
            $table->decimal('total_delivery_charge', 8, 2)->default(0.00)->comment('vat + cod + delivery charge');
            $table->decimal('payable', 8, 2)->default(0.00)->comment('total payable to Merchant');
            $table->decimal('return_charge', 8, 2)->default(0.00)->comment('parcel return charge if get returned');
            $table->decimal('price', 8, 2)->default(0.00);
            $table->unsignedBigInteger('merchant_id');
            $table->unsignedBigInteger('delivery_man_id')->nullable();
            $table->unsignedBigInteger('pickup_man_id')->nullable();
            $table->unsignedBigInteger('return_delivery_man_id')->nullable();
            $table->unsignedBigInteger('transfer_delivery_man_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('pickup_branch_id')->nullable();
            $table->unsignedBigInteger('transfer_to_branch_id')->nullable();
            $table->unsignedBigInteger('third_party_id')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->unsignedBigInteger('user_id')->comment('created by');
            $table->decimal('delivery_fee', 8, 2)->default(0.00);
            $table->decimal('pickup_fee', 8, 2)->default(0.00);
            $table->decimal('return_fee', 8, 2)->default(0.00);
            $table->string('customer_name', 100);
            $table->string('customer_invoice_no', 50);
            $table->string('customer_phone_number', 30);
            $table->text('customer_address');
            $table->date('pickup_date')->nullable();
            $table->time('pickup_time')->nullable();
            $table->date('delivery_date')->nullable();
            $table->time('delivery_time')->nullable();
            $table->date('date')->nullable()->comment('date added to get specific date data on where clause');
            $table->string('pickup_shop_phone_number', 191)->nullable();
            $table->text('pickup_address')->nullable();
            $table->string('note', 191)->nullable();

            // $table->enum('status', ['pending', 'deleted', 'pickup-assigned', 're-schedule-pickup', 'received-by-pickup-man', 'received', 'transferred-to-branch', 'transferred-received-by-branch', 'delivery-assigned', 're-schedule-delivery', 'returned-to-warehouse', 'return-assigned-to-merchant', 'partially-delivered', 'delivered', 'delivered-and-verified', 'returned-to-merchant', 'cancel', 're-request'])
            // ->default('pending')->comment('current status of parcel, re-request for after cancel by staff the merchant can re request parcel.');

            $table->enum('status', config('parcel.parcel_status'))
                ->default('pending')->comment('current status of parcel, re-request for after cancel by staff the merchant can re request parcel.');

            $table->string('status_before_cancel', 191)->nullable()->comment('status before cancel the parcel, it will use when re-request parcel');
            $table->tinyInteger('is_partially_delivered')->default(0)->comment('true for partially delivered');
            $table->decimal('price_before_delivery', 8, 2)->default(0.00)->comment('parcel price before partially delivered');
            $table->integer('otp')->nullable()->comment('to verify parcel successfully delivered to customer');
            $table->decimal('selling_price', 8, 2)->default(0.00)->comment('parcel actual price for damage of parcel money return purpose');
            $table->unsignedBigInteger('withdraw_id')->nullable();
            $table->tinyInteger('is_paid')->default(0)->comment('true for payment completed');
            $table->integer('merchant_otp')->nullable();
            $table->dateTime('delivered_date')->nullable();
            // $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('delivery_man_id')->references('id')->on('delivery_men')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pickup_man_id')->references('id')->on('delivery_men')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('return_delivery_man_id')->references('id')->on('delivery_men')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('transfer_delivery_man_id')->references('id')->on('delivery_men')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('pickup_branch_id')->references('id')->on('branches')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('transfer_to_branch_id')->references('id')->on('branches')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('third_party_id')->references('id')->on('third_parties')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('withdraw_id')->references('id')->on('merchant_withdraws')->onDelete('set null')->onUpdate('cascade');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
            // Indexes
            $table->index('merchant_id');
            $table->index('delivery_man_id');
            $table->index('pickup_man_id');
            $table->index('return_delivery_man_id');
            $table->index('user_id');
            $table->index('parcel_no');
            $table->index('branch_id');
            $table->index('transfer_to_branch_id');
            $table->index('transfer_delivery_man_id');
            $table->index('withdraw_id');
            $table->index('pickup_branch_id');
            $table->index('third_party_id');
            $table->index('shop_id');
            $table->index('tracking_number');
            $table->index('short_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parcels');
    }
}
