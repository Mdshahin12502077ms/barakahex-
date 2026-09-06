<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // company account means income/expense
        Schema::create('company_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('source', 191)->nullable();
            $table->text('details')->nullable();
            $table->date('date')->nullable();
            $table->string('type', 191)->nullable()->comment('income/credit, expense/debit');
            $table->decimal('amount', 8, 2)->nullable()->comment('positive and negative amount define by type');
            $table->decimal('balance', 8, 2)->nullable()->comment('grand balance');
            $table->string('create_type', 191)->nullable()->comment('user_defined for admin create');
            $table->text('receipt')->nullable()->comment('any receipt uploaded');
            $table->text('reject_reason')->nullable()->comment('if request rejected');
            $table->text('transaction_id')->nullable()->comment('if request processed');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('merchant_id')->nullable();
            $table->unsignedBigInteger('parcel_id')->nullable();
            $table->unsignedBigInteger('merchant_withdraw_id')->nullable();
            $table->unsignedBigInteger('delivery_man_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable()->comment('bank method/account wise cash collection');
            $table->dateTime('date_time')->nullable();
            $table->enum('status', [
                StatusEnum::ACTIVE->value,
                StatusEnum::INACTIVE->value,
            ])->default(StatusEnum::ACTIVE->value);
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('parcel_id')->references('id')->on('parcels')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('merchant_withdraw_id')->references('id')->on('merchant_withdraws')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('delivery_man_id')->references('id')->on('delivery_men')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('company_accounts');
    }
}
