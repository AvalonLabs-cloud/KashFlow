<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
Schema::create('transactions', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')
        ->constrained('users')
        ->restrictOnDelete();

    $table->foreignId('account_id')
        ->constrained('accounts')
        ->restrictOnDelete();

    $table->string('transaction_reference')->unique();

    $table->enum('type', [
        'airtime',
        'data',
        'transfer',
    ]);

    $table->enum('direction', [
        'credit',
        'debit',
    ]);

    $table->integer('amount' , false , true);

    // $table->string('currency', 3);

    $table->enum('status', [
        'pending',
        'processing',
        'successful',
        'failed',
        'cancelled',
    ])->default('pending');


    $table->string('base_currency')->default('naira')->nullable();

    $table->string('ledger_currency')->default('kobo')->nullable();

    $table->string('recipient')->nullable();

    $table->json('metadata')->nullable();

    $table->text('description')->nullable();

    $table->text('failure_reason')->nullable();

    $table->timestamp('initiated_at')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamp('failed_at')->nullable();

     $table->timestamp('next_reconsilation')->nullable();
    $table->integer('reconsilation_attempt_count')->nullable();

    $table->timestamps();

    $table->index([
        'account_id',
        'type',
        'status',
    ]);

    $table->index([
        'account_id',
        'direction',
    ]);

       $table->softDeletes();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};


