<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistrictZilasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('district_zilas', function (Blueprint $table) {
            $table->id();
            $table->string('point_code', 20)->nullable();
            $table->string('point_name', 100)->nullable();
            $table->string('union_para_name', 191)->nullable();
            $table->string('thana_name', 191)->nullable();
            $table->string('district_name', 191)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('district_zilas');
    }
}
