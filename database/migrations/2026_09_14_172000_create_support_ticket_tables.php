<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Support Tickets Table
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id')->unique(); // e.g. TCK-20260914-0001
            
            $table->unsignedBigInteger('user_id'); // Creator (User)
            $table->string('user_type')->default('merchant'); // merchant, customer, staff
            $table->unsignedBigInteger('merchant_id')->nullable();
            $table->unsignedBigInteger('parcel_id')->nullable();
            $table->string('tracking_number')->nullable()->index();
            
            $table->enum('ticket_type', [
                'merchant_ticket',
                'customer_ticket',
                'parcel_issue',
                'payment_issue',
                'delivery_issue',
                'return_issue',
                'technical_issue'
            ])->default('parcel_issue');
            
            $table->string('subject');
            $table->longText('description');
            
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['new', 'processing', 'resolved', 'closed'])->default('new');
            
            $table->unsignedBigInteger('assigned_to')->nullable(); // Staff user ID
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('set null');
            $table->foreign('parcel_id')->references('id')->on('parcels')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });

        // 2. Ticket Replies Table
        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('user_id');
            $table->text('reply_text');
            $table->boolean('is_internal_note')->default(false);
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('support_tickets')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 3. Ticket Attachments Table
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('reply_id')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable();
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('support_tickets')->onDelete('cascade');
            $table->foreign('reply_id')->references('id')->on('ticket_replies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('support_tickets');
    }
};
