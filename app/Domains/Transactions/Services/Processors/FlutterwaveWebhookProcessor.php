<?php

namespace App\Domains\Transactions\Services\Processors;

use App\Models\WebHookEvent;
use RuntimeException;
use App\Domains\Transactions\Services\TransactionResolution\Transfers\TransferCompleted;
use  App\Domains\Transactions\Services\TransactionResolution\Bills\BillCompleted;
use App\Domains\Transactions\Services\CreditClientsAccount;

class FlutterwaveWebhookProcessor
{

    public function __construct(
        public TransferCompleted $transferCompleted,
        public BillCompleted $billCompleted,
        public CreditClientsAccount $creditAccount,
    ) {
    }

    public function process(WebHookEvent $webhook): void
    {
        match ($webhook->event) {

            'charge.completed' =>
                $this->chargeCompleted($webhook),

            'transfer.completed' =>
                $this->transferCompleted($webhook),

            'billpayment.completed' =>
                $this->billPaymentCompleted($webhook),
        };
    }

    private function chargeCompleted(
        WebHookEvent $webhook
    ): void {
      $this->creditAccount->execute($webhook->transaction_reference , $webhook->amount , $webhook->metadata);
    }

    private function transferCompleted(
        WebHookEvent $webhook
    ): void {
     $this->transferCompleted->execute(webhook: $webhook);
    }



    private function billPaymentCompleted(
        WebHookEvent $webhook
    ) {
      $this->billCompleted->execute(webhook: $webhook);
    }

}
