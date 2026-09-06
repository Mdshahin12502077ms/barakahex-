<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preferences', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->tinyInteger('staff')->default(0)->comment('0 inactive, 1 active');
            $table->tinyInteger('merchant')->default(0)->comment('0 inactive, 1 active');
            $table->timestamps();
        });

        \DB::statement("INSERT INTO `preferences` (`id`, `title`, `staff`, `merchant`, `created_at`, `updated_at`) VALUES
        (1, 'read_merchant_api', 1, 1, NULL, NULL),
        (2, 'merchant_api_update', 1, 1, NULL, NULL),
        (3, 'create_parcel', 1, 1, NULL, '2021-07-31 17:10:09'),
        (4, 'create_payment_request', 1, 1, NULL, '2021-07-31 17:10:10'),
        (5, 'same_day', 1, 1, NULL, '2021-08-13 16:15:13'),
        (6, 'sub_city', 1, 1, NULL, '2021-07-31 17:10:10'),
        (7, 'sub_urban_area', 1, 1, NULL, '2021-07-31 17:10:10')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preferences');
    }
}
