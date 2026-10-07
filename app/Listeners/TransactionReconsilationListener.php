<?php

namespace App\Listeners;

use App\ClientProvider\FlutterwaveClient;
use App\Events\Reconsilation;
use App\Events\TransactionReconsilationEvent;
use App\Models\Transaction;
use App\TransactionReconsilationStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class TransactionReconsilationListener implements ShouldQueue
{
    use InteractsWithQueue;
    public function __construct(
        protected FlutterwaveClient $flutterwaveClient,
    ) {
        //
    }

    public function handle(TransactionReconsilationEvent $event): void
    {
        $transaction = Transaction::find($event->transactionId);
        $result = $this->retrieveTransactionStatus(tx_ref: $transaction->transaction_reference);
        if ($result['status'] !== 'success') {
            Log::error("Failed to retrieve transaction status for {$transaction->transaction_reference}.", [
                'response' => $result,
            ]);
            return;
        }
        switch ($result['data']['status']) {
            case 'pending':
                return;
                break;
            case 'success':
                $this->success(transaction: $transaction);
                break;
            case 'failed':
                $this->failed(transaction: $transaction);
                break;

            default:
                break;
        }
    }

    protected function retrieveTransactionStatus(mixed $tx_ref)
    {
        return $this->flutterwaveClient->verifyTransactionFromFlutterwave(tx_ref: $tx_ref);
    }

    protected function success(mixed $transaction)
    {
        $reconsilation = $transaction->transactionReconsilation()->create([
            'reconsilation_time' => now(),
            'status' => TransactionReconsilationStatus::Success,
        ]);

        Reconsilation::dispatch($reconsilation->id);
    }
    protected function failed(mixed $transaction)
    {
        $reconsilation = $transaction->transactionReconsilation()->create([
            'reconsilation_time' => now(),
            'status' => TransactionReconsilationStatus::Failed,
        ]);
        Reconsilation::dispatch($reconsilation->id);
    }
}
