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
    Schema::create('percel_otp_logs', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('parcel_id');
    $table->unsignedBigInteger('user_id')->nullable()->comment('Admin/Staff User ID');
    $table->unsignedBigInteger('delivery_man_id')->nullable()->comment('Rider ID');
    
   
    $table->string('action', 30);
    
    $table->string('otp_code', 10)->nullable();
    $table->string('submitted_otp', 10)->nullable();
    $table->integer('attempt_number')->default(1);
    
 
    $table->string('source', 30)->default('rider_web');
    $table->string('status_message', 255)->nullable();
    
    $table->string('ip_address', 45)->nullable();
    $table->text('user_agent')->nullable();
    $table->timestamps();
  
    $table->foreign('parcel_id')->references('id')->on('parcels')->onDelete('cascade');
    $table->index(['parcel_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_otp_logs');
    }
};
