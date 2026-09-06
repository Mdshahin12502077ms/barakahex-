<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_stores', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->string('phone_number');
            $table->text('address');
            $table->string('password');
            $table->string('otp')->comment('for phone number verification');
            $table->string('ip')->nullable();
            $table->string('platform')->nullable();
            $table->timestamps();
            $table->string('browser')->nullable();
            $table->string('user_agent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_stores');
    }
}
