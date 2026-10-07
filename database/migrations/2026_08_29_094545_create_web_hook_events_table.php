<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_hook_events', function (Blueprint $table) {
            $table->id();

            /*
             * ============================================================
             * WEBHOOK EVENT
             * ============================================================
             */

            // e.g. charge.completed
            // e.g. transfer.completed
            // e.g. billpayment.completed
            $table->string('event', 100);

            // e.g. BANK_TRANSFER_TRANSACTION
            $table->string('event_type', 150)->nullable();

            /*
             * ============================================================
             * PROVIDER IDENTIFIERS
             * ============================================================
             */

            $table->integer('event_id')->unique();

            // Flutterwave transaction ID where available.
            $table->string('transaction_reference', 100)->nullable();

            // tx_ref / transfer reference / bill reference etc.
            $table->string('provider_reference', 255)->nullable();

            /*
             * ============================================================
             * GENERIC TRANSACTION INFORMATION
             * ============================================================
             */

            $table->decimal('amount', 20, 2)->nullable();

            $table->char('currency', 3)->nullable();

            $table->string('status', 100)->nullable();

            /*
             * ============================================================
             * CUSTOMER
             * ============================================================
             *
             * Not every webhook will contain these.
             */

            $table->unsignedBigInteger('customer_id')->nullable();

            $table->string('customer_name')->nullable();

            $table->string('customer_email')->nullable();

            $table->string('customer_phone', 50)->nullable();

            /*
             * ============================================================
             * PAYMENT / SERVICE INFORMATION
             * ============================================================
             */

            // bank_transfer, card, mobile_money, airtime, data, etc.
            $table->string('payment_type', 100)->nullable();

            // Useful for bill payments.
            // e.g. airtime, data, cable, electricity.
            $table->string('service_type', 100)->nullable();

            /*
             * ============================================================
             * PROCESSING
             * ============================================================
             */

            // $table->string('processing_status', 50)
            //     ->default('pending');

            // $table->unsignedInteger('processing_attempts')
            //     ->default(0);

            $table->timestamp('processed_at')->nullable();

            $table->text('processing_error')->nullable();

            /*
             * ============================================================
             * RAW PROVIDER PAYLOAD
             * ============================================================
             *
             * This is the most important flexibility mechanism.
             *
             * Whatever Flutterwave sends gets preserved here.
             */

            $table->json('payload');

            /*
             * ============================================================
             * NORMALIZED DATA
             * ============================================================
             *
             * Optional data extracted from the payload that isn't
             * worth creating a dedicated column for.
             */

            $table->json('metadata')->nullable();

            /*
             * ============================================================
             * TIMESTAMPS
             * ============================================================
             */

            $table->timestamp('provider_created_at')->nullable();

            $table->timestamps();

            /*
             * ============================================================
             * INDEXES
             * ============================================================
             */

            $table->index('event');
            $table->index('event_type');

            $table->index('transaction_reference');
            $table->index('provider_reference');
            // $table->index('provider_reference_2');

            $table->index('status');
            // $table->index('processing_status');

            $table->index('service_type');

            $table->index('customer_id');

            /*
             * IMPORTANT:
             *
             * Do NOT make provider_reference universally unique.
             *
             * Different Flutterwave event types may have different
             * identifiers or potentially reuse references.
             */
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_hook_events');
    }
};