<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryMenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_men', function (Blueprint $table) {
                 $table->id();
                $table->string('phone_number', 30);
                $table->string('city', 100)->nullable();
                $table->string('zip', 15)->nullable()->comment('postal code');
                $table->text('address')->nullable();
                $table->text('driving_license')->nullable();
                $table->decimal('pick_up_fee', 8, 2)->default(0.00)->comment('1');
                $table->decimal('delivery_fee', 8, 2)->default(0.00)->comment('1');
                $table->decimal('return_fee', 8, 2)->default(0.00)->comment('1');
                $table->unsignedBigInteger('user_id');
                $table->decimal('balance', 8, 2)->nullable();
                $table->integer('is_vertual')->default(0);
                $table->integer('is_shuttle')->default(0);
                $table->string('sip_extension', 151)->nullable();
                $table->string('sip_password', 151)->nullable();
                $table->integer('dial_enable')->default(0);
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('delivery_men');
    }
}
