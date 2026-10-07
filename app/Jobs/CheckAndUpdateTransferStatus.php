<?php

namespace App\Jobs;

use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CheckAndUpdateTransferStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transferId;

    public $transferIdFltServer;

    /**
     * Create a new job instance.
     */
    public function __construct($transferId, $transferIdFltServer)
    {
        $this->transferId = $transferId;
        $this->transferIdFltServer = $transferIdFltServer;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $transfer = Transfer::find($this->transferId);

        if (! $transfer) {
            return;
        }

        // Stop if already resolved (idempotency guard)
        if (in_array($transfer->status, ['successful', 'failed'])) {
            return;
        }

        // Call Flutterwave fetch transfer endpoint
        $response = Http::withToken(config('flutterwave.flutterwave_secret_key'))
            ->get("https://api.flutterwave.com/v3/transfers/{$this->transferIdFltServer}");

        if (! $response->successful()) {
            // Retry later if API fails
            $this->release(2);

            return;
        }

        $data = $response->json();

        $status = strtolower($data['data']['status'] ?? '');

        if ($status === 'successful') {
            $transfer->update([
                'status' => 'successful',
            ]);

            return;
        }

        if ($status === 'failed') {
            $transfer->update([
                'status' => 'failed',
            ]);

            return;
        }

        // Still pending → re-dispatch after 2 seconds
        self::dispatch($this->transferId)->delay(now()->addSeconds(2));
    }
}
