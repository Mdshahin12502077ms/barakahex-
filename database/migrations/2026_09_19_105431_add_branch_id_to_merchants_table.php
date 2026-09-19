<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchants', function (Blueprint $table) {
          
            $table->unsignedBigInteger('branch_id')->nullable()->after('id');
            
         
            $table->unsignedBigInteger('pickup_man_id')->nullable()->after('branch_id');
            $table->unsignedBigInteger('delivery_man_id')->nullable()->after('pickup_man_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchants', function (Blueprint $table) {
           
            $table->dropColumn(['branch_id', 'pickup_man_id', 'delivery_man_id']);
        });
    }
}
