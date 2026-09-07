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
        Schema::table('parcel_events', function (Blueprint $table) {
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('action')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcel_events', function (Blueprint $table) {
            $table->dropColumn('old_status');
            $table->dropColumn('new_status');
            $table->dropColumn('ip_address');
            $table->dropColumn('action');
        });
    }
};
