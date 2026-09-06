<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('source')->nullable()->comment('I will use it to make the description more meaningful');
            $table->text('details')->nullable();
            $table->date('date')->nullable();
            $table->string('type')->nullable()->comment('income/credit, expense/debit');
            $table->decimal('amount', 8, 2)->nullable()->comment('Positive and negative amount defined by type');
            $table->decimal('balance', 8, 2)->nullable()->comment('Grand balance');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Staff ID for easy retrieval');
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('company_account_id')->nullable();
            $table->unsignedBigInteger('fund_transfer_id')->nullable();
            $table->unsignedBigInteger('from_account_id')->nullable();
            $table->unsignedBigInteger('to_account_id')->nullable();
            $table->datetime('date_time')->nullable();
            // Define foreign key relationships
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('company_account_id')->references('id')->on('company_accounts')->onDelete('cascade');
            $table->foreign('fund_transfer_id')->references('id')->on('fund_transfers')->onDelete('cascade');
            $table->foreign('from_account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('to_account_id')->references('id')->on('accounts')->onDelete('cascade');
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
        Schema::dropIfExists('staff_accounts');
    }
}
