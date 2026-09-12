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
            if (!Schema::hasColumn('parcels', 'district_id')) {
                $table->unsignedBigInteger('district_id')->nullable()->after('customer_address');
                $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null')->onUpdate('cascade');
                $table->index('district_id');
            }

            if (!Schema::hasColumn('parcels', 'thana_id')) {
                $table->unsignedBigInteger('thana_id')->nullable()->after('district_id');
                $table->foreign('thana_id')->references('id')->on('thanas')->onDelete('set null')->onUpdate('cascade');
                $table->index('thana_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            if (Schema::hasColumn('parcels', 'thana_id')) {
                $table->dropForeign(['thana_id']);
                $table->dropColumn('thana_id');
            }

            if (Schema::hasColumn('parcels', 'district_id')) {
                $table->dropForeign(['district_id']);
                $table->dropColumn('district_id');
            }
        });
    }
};
