<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */ 
    public function up()
    {
        Schema::create('withdraw_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('created by');
            $table->unsignedBigInteger('account_id')->nullable()->comment('processed from account');
            $table->string('batch_type')->nullable();
            $table->string('batch_no', 191)->nullable()->comment('auto generated 10-digit id');
            $table->string('title', 191)->nullable();
            $table->mediumText('note')->nullable();
            $table->enum('status', ['pending', 'processed'])->default('pending');
            $table->text('receipt')->nullable()->comment('any receipt uploaded');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('set null')->onUpdate('cascade');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdraw_batches');
    }
}
