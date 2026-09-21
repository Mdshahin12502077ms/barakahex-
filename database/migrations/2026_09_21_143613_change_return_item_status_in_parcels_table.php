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
        // Change the column to allow NULL and set default to NULL
        DB::statement("ALTER TABLE parcels MODIFY COLUMN return_item_status ENUM('pending_at_rider','received_at_hub','returned_to_merchant','received_by_merchant') DEFAULT NULL");
        
        // Update existing parcels that are not partially delivered to have a NULL return_item_status
        DB::statement("UPDATE parcels SET return_item_status = NULL WHERE is_partially_delivered = 0");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the old default
        DB::statement("UPDATE parcels SET return_item_status = 'pending_at_rider' WHERE return_item_status IS NULL");
        DB::statement("ALTER TABLE parcels MODIFY COLUMN return_item_status ENUM('pending_at_rider','received_at_hub','returned_to_merchant','received_by_merchant') NOT NULL DEFAULT 'pending_at_rider'");
    }
};
