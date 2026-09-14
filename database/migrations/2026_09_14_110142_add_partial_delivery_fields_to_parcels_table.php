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
        Schema::table('parcels', function (Blueprint $table) {
            $table->decimal('total_quantity',10,2)->default(0)->comment('Total quantity of items in the parcel');
            $table->decimal('return_quantity',10,2)->default(0)->comment('Pending quantity of items in the parcel');
            $table->decimal('delivered_quantity',10,2)->default(0)->comment('Delivered quantity of items in the parcel');
            $table->enum('payment_status', ['paid','partial_paid','unpaid'])->default('unpaid')->comment('Payment status of the parcel');
            $table->string('payment_method')->nullable()->comment('Payment method of the parcel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
             $table->dropColumn(['total_quantity', 'delivered_quantity', 'return_quantity', 'payment_status', 'payment_method']);
        });
    }
};
