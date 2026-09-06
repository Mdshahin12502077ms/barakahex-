<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('company');
            $table->string('api_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('vat')->nullable();
            $table->string('phone_number');
            $table->string('city')->nullable();
            $table->string('zip')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('billing_street')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_zip')->nullable();
            $table->tinyInteger('registration_confirmed')->default(1)->comment('true confirmed, false not confirmed');
            $table->text('trade_license')->nullable();
            $table->text('nid')->nullable();
            $table->decimal('balance', 8, 2)->default(0.00);
            $table->integer('key_account_id')->nullable();
            $table->integer('sales_agent_id')->nullable();
            $table->integer('mig_done')->default(0);
            $table->enum('withdraw',['daily','weekly', 'monthly'])->default('monthly');
            $table->longText('charges')->nullable();
            $table->longText('cod_charges')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('default_account_id')->nullable();
            // $table->foreign('default_account_id')->references('id')->on('merchant_payment_accounts')->onUpdate('cascade')->onDelete('cascade');
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchants');
    }
}
