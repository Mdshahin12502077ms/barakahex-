<?php

use App\Enums\StatusEnum;
use App\Models\CodCharge;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCodChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cod_charges', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->decimal('charge', 8, 2)->default(0.00);
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

        $attributes = [
            '1'           => ['city', 0.0],
            '2'           => ['sub_city', 1.0],
            '3'           => ['sub_urban_area', 1.0],
            '4'           => ['third_party_booking', 1.0]
        ];

        foreach($attributes as $key => $attribute){
            $permission            = new CodCharge();
            $permission->location  = $attribute[0];
            $permission->charge    = $attribute[1];
            $permission->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cod_charges');
    }
}
