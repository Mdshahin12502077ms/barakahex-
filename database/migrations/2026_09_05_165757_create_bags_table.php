<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bags', function (Blueprint $table) {
        $table->id();



    $table->string('bag_no')->unique();
    

    $table->unsignedBigInteger('from_branch_id');
    $table->unsignedBigInteger('to_branch_id');
    
   
    $table->unsignedBigInteger('created_by')->comment('User ID who created the bag');
    $table->unsignedBigInteger('received_by')->nullable()->comment('User ID who received at destination');
    
    
    $table->unsignedBigInteger('delivery_man_id')->nullable();
    
    
    $table->integer('total_parcels')->default(0);
    $table->integer('max_capacity')->default(50)->comment('Maximum parcels this bag can hold');
    
    $table->enum('status', ['open', 'closed', 'in_transit', 'received'])->default('open');
    
  
    $table->timestamp('dispatched_at')->nullable();
    $table->timestamp('received_at')->nullable();
    
    
    $table->text('note')->nullable();
    
    $table->timestamps();
   
    $table->foreign('from_branch_id')->references('id')->on('branches')->onDelete('cascade');
    $table->foreign('to_branch_id')->references('id')->on('branches')->onDelete('cascade');
    $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
    $table->foreign('delivery_man_id')->references('id')->on('delivery_men')->onDelete('set null');


           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bags');
    }
};
