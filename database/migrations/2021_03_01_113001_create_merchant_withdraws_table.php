<?php

use App\Enums\PaymentMethodType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantWithdrawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_withdraws', function (Blueprint $table) {

            $table->id();
            $table->string('withdraw_id', 191)->nullable()->comment('auto generated 10-digit id');
            $table->unsignedBigInteger('merchant_id');
            $table->unsignedBigInteger('withdraw_batch_id')->nullable();
            $table->enum('payment_method_type', [
                PaymentMethodType::BANK->value,
                PaymentMethodType::MFS->value,
                PaymentMethodType::CASH->value,
            ])->default(PaymentMethodType::BANK->value);
            $table->unsignedBigInteger('withdraw_to')->nullable()->comment('in which account type gets withdraw-- merchant_payment_accounts');
            $table->text('note')->nullable();
            $table->decimal('amount', 8, 2)->nullable();
            $table->string('status', 191)->default('pending')->comment('pending, approved, processed, rejected');
            $table->text('account_details')->comment('account in which merchant withdraws his money');
            $table->date('date')->nullable();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade')->onUpdate('cascade');
            // $table->foreign('withdraw_batch_id')->references('id')->on('withdraw_batches')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('withdraw_to')->references('id')->on('merchant_payment_accounts')->onDelete('set null')->onUpdate('cascade');
            
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
        Schema::dropIfExists('merchant_withdraws');
    }
}
