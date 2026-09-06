<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageAndChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_and_charges', function (Blueprint $table) {
            $table->id();
            $table->string('package_type')->nullable();
            $table->decimal('charge', 8, 2)->default(0.00);
            $table->timestamps();
        });

        \DB::statement("INSERT INTO `package_and_charges` (`id`, `package_type`, `charge`, `created_at`, `updated_at`) VALUES
        (1, 'Poly', '5.00', '2021-07-29 05:29:16', '2021-07-29 05:29:16'),
        (2, 'Bubble Poly', '10.00', '2021-07-29 05:29:16', '2021-07-29 05:29:16'),
        (3, 'Box', '15.00', '2021-07-29 05:29:16', '2021-07-29 05:29:16'),
        (4, 'Box Poly', '20.00', '2021-07-29 05:29:16', '2021-07-29 05:29:16')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_and_charges');
    }
}
