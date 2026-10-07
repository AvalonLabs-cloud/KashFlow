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
        Schema::create('admin_held_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_transaction_id')->constrained('admin_transactions');
            $table->enum('status' , ['active' , 'released']);
            $table->decimal('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_held_balances');
    }
};
