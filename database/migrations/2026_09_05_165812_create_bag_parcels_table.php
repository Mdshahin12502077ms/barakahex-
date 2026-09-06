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
        Schema::create('bag_parcels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBiginteger('bag_id')->nullable();
            $table->unsignedBiginteger('percel_id')->nullable();
            $table->foreign('bag_id')->references('id')->on('bags')->onDelete('cascade');
            $table->foreign('percel_id')->references('id')->on('parcels')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bag_parcels');
    }
};
