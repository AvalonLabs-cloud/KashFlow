<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // bank_transfer, airtime, data, cable, bill, etc.
            $table->string('type');

            // User-defined name e.g. "Mum", "John", "Home DSTV"
            $table->string('recipient_name');

            // Bank transfer information
            $table->string('recipient_account')->nullable();
            $table->string('recipient_bank')->nullable();

            // Airtime / Data information
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_network')->nullable();


            // Extra provider/type-specific information
            $table->json('metadata')->nullable();

            $table->boolean('is_favorite')->default(false);

            $table->timestamp('last_used_at')->nullable();

            $table->timestamps();

            // Useful for quickly retrieving a user's beneficiaries
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'is_favorite']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
