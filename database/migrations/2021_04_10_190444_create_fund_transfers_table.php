<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() 
    {
        Schema::create('fund_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_account_id')->nullable();
            $table->unsignedBigInteger('to_account_id')->nullable();
            $table->date('date')->nullable();
            $table->text('note')->nullable();
            $table->decimal('amount', 8, 2)->default(0.00);
            $table->datetime('date_time')->nullable();
            $table->integer('status')->default(1)->comment('0=pending, 1=accept, 2=decline');
            // Define foreign key relationships
            $table->foreign('from_account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('to_account_id')->references('id')->on('accounts')->onDelete('cascade');
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
        Schema::dropIfExists('fund_transfers');
    }
}
