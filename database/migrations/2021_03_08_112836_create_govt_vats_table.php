<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGovtVatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('govt_vats', function (Blueprint $table) {
            $table->id();
            $table->string('source', 191)->nullable()->comment('I will use it to make the description more meaningful');
            $table->date('date')->nullable();
            $table->text('details')->nullable();
            $table->string('type', 191)->nullable()->comment('income/credit, expense/debit');
            $table->decimal('amount', 8, 2)->nullable();
            $table->unsignedBigInteger('parcel_id')->nullable();
            $table->foreign('parcel_id')->references('id')->on('parcels')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('govt_vats');
    }
}
