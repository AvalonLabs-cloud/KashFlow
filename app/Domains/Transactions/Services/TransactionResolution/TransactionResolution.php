<?php

namespace App\Domains\Transactions\Services\TransactionResolution;

use App\Models\WebHookEvent;
use App\ClientProvider\FlutterwaveClient;
use App\Domains\Transfer\TransactionResolution\TransactionSuccess;
use App\Domains\Transfer\TransactionResolution\TransactionFailure;
use App\Domains\Transactions\Admin\Services\MarkAsSuccessFull;
use App\Domains\Transactions\Admin\Services\MarkAsFailed;
use App\Models\TransactionReconsilation;
use App\Domains\Reconsilation\Services\markReconsilationFailure;
use App\Domains\Reconsilation\Services\markReconsilationSuccess;

class TransactionResolution
{
    public function __construct(
        public FlutterwaveClient $flutterwaveClient,
        public TransactionSuccess $transactionSuccess,
        public TransactionFailure $transactionFailure,
        public MarkAsSuccessFull $admimTransactionSuccess,
        public MarkAsFailed $admimTransactionFailure,
        public markReconsilationSuccess $markReconsilationSuccess,
        public markReconsilationFailure $markReconsilationFailure
    ) {}

    public function execute(?WebHookEvent $webhook = null, ?TransactionReconsilation $reconsilation = null)
    {
        if (isset($webhook)) {
            $providerTransaction = $this->verifyTransferWithFlutterwave(
                tx_ref: $webhook->transaction_reference
            );

            if ($providerTransaction['status'] === 'success') {
                $this->markSuccessfull(webhook: $webhook, providerTransaction: $providerTransaction);
            }
            if ($providerTransaction['status'] === 'Failed') {
                $this->markFailed(webhook: $webhook, providerTransaction: $providerTransaction);
            }
        }

        if ($reconsilation) {
            $providerTransaction = $this->verifyTransferWithFlutterwave(
                tx_ref: $reconsilation->transaction->reference,
            );

            if ($providerTransaction['status'] === 'success') {
                $this->markReconsilationSuccess->execute($reconsilation);
            }
            if ($providerTransaction['status'] === 'Failed') {
                $this->markReconsilationFailure->execute($reconsilation);
            }
        }
    }

    private function markSuccessfull(mixed $webhook, mixed $providerTransaction): void
    {
        if ($providerTransaction['data']['meta']['type'] === 'admin') {
            $this->admimTransactionSuccess->markAsSuccessfull($providerTransaction['reference']);
        } else {
            $this->transactionSuccess->execute(
                webhook: $webhook,
                providerTransfer: $providerTransaction
            );
        }
    }

    private function markFailed(mixed $webhook, mixed $providerTransaction): void
    {
        if ($providerTransaction['data']['meta']['type'] === 'admin') {
            $this->admimTransactionFailure->markAsFailed($providerTransaction['reference']);
        } else {
            $this->transactionFailure->execute(
                webhook: $webhook,
                providerTransfer: $providerTransaction
            );
        }
    }

    private function verifyTransferWithFlutterwave(string $tx_ref)
    {
        return $this->flutterwaveClient->verifyTransactionFromFlutterwave(tx_ref: $tx_ref);
    }
}
