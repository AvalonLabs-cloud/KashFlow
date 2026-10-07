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
     Schema::create('transaction_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('transaction_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Unique reference shown to the user
            $table->string('reference')->unique();

            // transfer, airtime, data, cable, bill, deposit, withdrawal, etc.
            $table->string('type');

            // credit or debit
            $table->string('direction');

            // pending, processing, successful, failed, reversed
            $table->string('status');

            // Store monetary values in kobo
            $table->unsignedBigInteger('amount');
    

            $table->string('currency', 3)->default('NGN');

            // Snapshot of the recipient at the time of transaction
            $table->string('recipient_name')->nullable();
            $table->string('recipient_account')->nullable();
            $table->string('recipient_bank_code')->nullable();
            $table->string('recipient_bank_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_network')->nullable();

            // Human-readable description
            $table->string('description')->nullable();

            // Additional transaction-specific information
            $table->json('metadata')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'direction']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_histories');
    }
};

