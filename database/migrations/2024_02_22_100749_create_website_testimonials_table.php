<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('designation')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('media_id')->nullable();
            $table->text('image')->nullable();
            $table->string('rating')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0 inactive, 1 active');
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
        Schema::dropIfExists('website_testimonials');
    }
};
