<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
             $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('income and expense');
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('method', 191)->nullable();
            $table->string('account_holder_name', 191)->nullable();
            $table->string('account_no', 191)->nullable();
            $table->string('bank_name', 191)->nullable();
            $table->string('bank_branch', 191)->nullable();
            $table->string('number', 191)->nullable();
            $table->string('type', 191)->nullable()->comment('merchant, personal');
            $table->decimal('balance', 8, 2)->default(0.00);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('accounts');
    }
}
