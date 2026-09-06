<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_events', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parcel_id')->unsigned()->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->comment('created by');
            $table->bigInteger('delivery_man_id')->unsigned()->nullable();
            $table->bigInteger('pickup_man_id')->unsigned()->nullable();
            $table->bigInteger('return_delivery_man_id')->unsigned()->nullable();
            $table->bigInteger('transfer_delivery_man_id')->unsigned()->nullable();
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->bigInteger('third_party_id')->unsigned()->nullable();
            $table->string('title')->nullable();
            $table->string('reverse_status')->nullable()->comment('if reversed than it will be set as reversed');
            $table->foreign('parcel_id')->references('id')->on('parcels')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('delivery_man_id')->references('id')->on('delivery_men')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('pickup_man_id')->references('id')->on('delivery_men')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('return_delivery_man_id')->references('id')->on('delivery_men')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('transfer_delivery_man_id')->references('id')->on('delivery_men')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('third_party_id')->references('id')->on('third_parties')->onUpdate('cascade')->onDelete('set null');
            $table->text('cancel_note')->nullable()->comment('events note');
            $table->longText('additional_info')->nullable(); 
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
        Schema::dropIfExists('parcel_events');
    }
}
