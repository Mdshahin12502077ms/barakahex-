<?php

use App\Models\Language;
use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->index();
            $table->string('locale', 30)->unique()->index();
            $table->string('flag', 50)->nullable();
            $table->string('text_direction', 30)->default('ltr')->nullable();
            $table->enum('status', [
                StatusEnum::ACTIVE->value,
                StatusEnum::INACTIVE->value,
            ])->default(StatusEnum::ACTIVE->value);
            $table->timestamps();
        });

        Language::create([
            'name'      => 'English',
            'locale'    => 'en',
            'flag'      => 'images/flags/us.png'
        ]);
        Language::create([
            'name'      => 'Bengali',
            'locale'    => 'bn',
            'flag'      => 'images/flags/bd.png'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('languages');
    }
};
