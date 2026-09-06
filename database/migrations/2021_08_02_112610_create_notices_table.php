<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('details');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->tinyInteger('staff')->default(0)->comment('0 inactive, 1 active');
            $table->tinyInteger('merchant')->default(0)->comment('0 inactive, 1 active');
            $table->string('alert_class')->default('alert-warning');
            $table->enum('status', [
                StatusEnum::ACTIVE->value,
                StatusEnum::INACTIVE->value,
            ])->default(StatusEnum::ACTIVE->value);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
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
        Schema::dropIfExists('notices');
    }
}
