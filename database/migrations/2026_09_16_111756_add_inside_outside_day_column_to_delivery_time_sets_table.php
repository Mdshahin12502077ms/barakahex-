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
    Schema::table('delivery_time_sets', function (Blueprint $table) {
        $table->integer('inside_city_days')->default(1)->after('offday'); // ইনসাইড সিটির জন্য কয়দিন
        $table->integer('sub_city_days')->default(2)->after('inside_city_days'); // সাব সিটির জন্য কয়দিন
        $table->integer('outside_city_days')->default(3)->after('sub_city_days'); // আউটসাইড সিটির জন্য কয়দিন
    });
}

public function down(): void
{
    Schema::table('delivery_time_sets', function (Blueprint $table) {
        $table->dropColumn(['inside_city_days', 'sub_city_days', 'outside_city_days']);
    });
}

};
