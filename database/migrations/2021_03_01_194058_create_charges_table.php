<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->string('weight')->nullable();
            $table->decimal('same_day', 8, 2)->default(0.00);
            $table->decimal('sub_city', 8, 2)->default(0.00);
            $table->decimal('sub_urban_area', 8, 2)->default(0.00);
            $table->decimal('frozen')->default(0.00);
            $table->decimal('third_party_booking')->default(0.00);
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


        \DB::statement("INSERT INTO `charges` (`id`, `weight`, `same_day`,  `sub_city`, `sub_urban_area`, `created_at`, `updated_at`) VALUES
        (1, '1', 100.00, 100.00, 130.00, '2021-05-27 18:00:02', '2021-05-27 18:00:02'),
        (2, '2', 120.00, 115.00, 160.00, '2021-05-27 18:00:02', '2021-05-27 18:00:02'),
        (3, '3', 140.00, 130.00, 190.00, '2021-05-27 18:00:02', '2021-05-27 18:00:02'),
        (4, '4', 160.00, 145.00, 220.00, '2021-05-27 18:00:02', '2021-05-27 18:00:02'),
        (5, '5', 180.00, 160.00, 250.00, '2021-05-27 18:00:02', '2021-05-27 18:00:02'),
        (6, '6', 200.00, 175.00, 280.00, '2021-05-27 18:00:02', '2021-05-27 18:00:02'),
        (7, '7', 220.00, 190.00, 310.00, '2021-05-27 18:00:02', '2021-05-27 18:00:02'),
        (8, '8', 240.00, 205.00, 340.00, '2021-05-27 18:00:02', '2021-05-27 18:00:02'),
        (9, '9', 260.00, 220.00, 370.00, '2021-05-27 18:00:02', '2021-05-27 18:00:02'),
        (10, '10', 280.00, 235.00, 400.00, '2021-05-27 18:00:02', '2021-05-27 18:00:02'),
        (11, '11', 290.00, 240.00, 0.00, NULL, NULL),
        (12, '12', 305.00, 255.00, 0.00, NULL, NULL),
        (13, '13', 420.00, 270.00, 0.00, NULL, NULL),
        (14, '14', 435.00, 285.00, 0.00, NULL, NULL),
        (15, '15', 450.00, 300.00, 0.00, NULL, NULL),
        (16, '16', 465.00, 315.00, 0.00, NULL, NULL),
        (17, '17', 480.00, 330.00, 0.00, NULL, NULL),
        (18, '18', 595.00, 345.00, 0.00, NULL, NULL),
        (19, '19', 605.00, 360.00, 0.00, NULL, NULL),
        (20, '20', 290.00, 240.00, 0.00, NULL, NULL);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charges');
    }
}
