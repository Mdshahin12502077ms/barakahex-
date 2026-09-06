<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() 
    {
        Schema::create('merchant_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('source', 191)->nullable();
            $table->unsignedBigInteger('merchant_withdraw_id')->nullable();
            $table->unsignedBigInteger('payment_withdraw_id')->nullable()->comment('withdraw_id on which this amount got calculated');
            $table->tinyInteger('is_paid')->default(0)->comment('true for payment completed');
            $table->unsignedBigInteger('parcel_withdraw_id')->nullable()->comment('withdraw_id upon which this parcel got reverse');
            $table->text('details')->nullable();
            $table->date('date')->nullable();
            $table->string('type', 191)->nullable()->comment('income/credit, expense/debit');
            $table->decimal('amount', 8, 2)->nullable()->comment('positive and negative amount define by type');
            $table->decimal('balance', 8, 2)->nullable()->comment('grand balance');
            $table->unsignedBigInteger('merchant_id')->nullable();
            $table->unsignedBigInteger('parcel_id')->nullable();
            $table->unsignedBigInteger('company_account_id')->nullable();
            $table->foreign('merchant_withdraw_id')->references('id')->on('merchant_withdraws')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('payment_withdraw_id')->references('id')->on('merchant_withdraws')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('parcel_withdraw_id')->references('id')->on('merchant_withdraws')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('parcel_id')->references('id')->on('parcels')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('company_account_id')->references('id')->on('company_accounts')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_accounts');
    }
}
