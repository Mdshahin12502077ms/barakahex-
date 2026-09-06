<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema; 
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryManAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_man_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('source', 191)->nullable();
            $table->text('details')->nullable();
            $table->date('date')->nullable();
            $table->string('type', 191)->nullable()->comment('income/credit, expense/debit');
            $table->decimal('amount', 8, 2)->nullable()->comment('positive and negative amount define by type');
            $table->decimal('balance', 8, 2)->nullable()->comment('grand balance');
            $table->unsignedBigInteger('parcel_id')->nullable();
            $table->unsignedBigInteger('delivery_man_id')->nullable();
            $table->unsignedBigInteger('company_account_id')->nullable();
            $table->foreign('parcel_id')->references('id')->on('parcels')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('delivery_man_id')->references('id')->on('delivery_men')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('company_account_id')->references('id')->on('company_accounts')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('delivery_man_accounts');
    }
}
