<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name', 191);
            $table->string('shop_phone_number', 191);
            $table->string('contact_number', 191)->nullable();
            $table->unsignedBigInteger('pickup_branch_id')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('merchant_id')->nullable();
            $table->tinyInteger('default')->default(0)->comment('0 not default, 1 default shop used for creating parcel request/pickup');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('set null')->onUpdate('cascade');
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
        Schema::dropIfExists('shops');
    }
}
