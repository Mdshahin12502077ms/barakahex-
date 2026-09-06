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
        Schema::create('percel_movements', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('parcel_id');
        $table->string('tracking_number')->nullable();
        
        //where from where to
        $table->unsignedBigInteger('from_branch_id');
        $table->unsignedBigInteger('to_branch_id');
        
        // who sent and who receive
        $table->unsignedBigInteger('sent_by')->nullable()->comment('User ID who dispatched');
        $table->unsignedBigInteger('received_by')->nullable()->comment('User ID who received');
        
        // who take the parcel
        $table->unsignedBigInteger('delivery_man_id')->nullable()->comment('Driver/Rider ID');
        
        // time record (Date & Time)
        $table->timestamp('sent_at')->nullable();
        $table->timestamp('received_at')->nullable();
        
        // status and note
        $table->string('status')->default('in_transit'); // in_transit, received, cancelled
        $table->string('manifest_no')->nullable()->comment('Challan or Bag Number');
        $table->text('note')->nullable();
        
        $table->timestamps();
        // Foreign keys
        $table->foreign('parcel_id')->references('id')->on('parcels')->onDelete('cascade');
        $table->foreign('from_branch_id')->references('id')->on('branches')->onDelete('cascade');
        $table->foreign('to_branch_id')->references('id')->on('branches')->onDelete('cascade');
        $table->foreign('sent_by')->references('id')->on('users')->onDelete('set null');
        $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
    });
          
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('percel_movements');
    }
};
