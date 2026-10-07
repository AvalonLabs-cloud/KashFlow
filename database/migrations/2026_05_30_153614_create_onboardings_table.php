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
        Schema::create('onboardings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('phase', ['phase_one', 'phase_two', 'phase_three', 'phase_four', 'phase_five'])->default('phase_one');
            $table->boolean('is_authenticated')->default(false);
            $table->boolean('is_phone_verified')->default(false);
            $table->enum('bvn_verification_status', ['not_verified', 'pending', 'verified'])->default('not_verified');
            $table->enum('identity_verification_status', ['not_verified', 'pending', 'verified'])->default('not_verified');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboardings');
    }
};
