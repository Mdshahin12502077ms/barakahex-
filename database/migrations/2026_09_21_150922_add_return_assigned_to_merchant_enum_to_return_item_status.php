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
        DB::statement("ALTER TABLE parcels MODIFY COLUMN return_item_status ENUM('pending_at_rider','received_at_hub','return_assigned_to_merchant','returned_to_merchant','received_by_merchant') DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Change back to original enum values, removing return_assigned_to_merchant (this will cause errors if data exists with this status, but acceptable for down method)
        DB::statement("UPDATE parcels SET return_item_status = NULL WHERE return_item_status = 'return_assigned_to_merchant'");
        DB::statement("ALTER TABLE parcels MODIFY COLUMN return_item_status ENUM('pending_at_rider','received_at_hub','returned_to_merchant','received_by_merchant') DEFAULT NULL");
    }
};
