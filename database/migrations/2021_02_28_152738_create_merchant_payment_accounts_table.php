<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantPaymentAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id');
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
            $table->string('selected_bank', 191)->nullable()->comment('for bank account');
            $table->string('bank_branch', 50)->nullable()->comment('selected bank branch name');
            $table->string('bank_ac_name', 50)->nullable()->comment('bank account owner name');
            $table->string('bank_ac_number', 50)->nullable()->comment('bank account number');
            $table->string('routing_no', 50)->nullable()->comment('bank routing number');
            $table->string('mfs_number', 20)->nullable()->comment('mfs account number');
            $table->enum('mfs_ac_type',config('parcel.account_types'))->nullable()->comment('mfs account type');
            $table->enum('type',['mfs','bank', 'cash'])->default('cash');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', [
                StatusEnum::ACTIVE->value,
                StatusEnum::INACTIVE->value,
            ])->default(StatusEnum::ACTIVE->value);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();

        });

          // Index for foreign key
          Schema::table('merchant_payment_accounts', function (Blueprint $table) {
            $table->index('merchant_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_payment_accounts');
    }
}
