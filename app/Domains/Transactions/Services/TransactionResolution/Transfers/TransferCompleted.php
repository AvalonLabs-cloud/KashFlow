<?php

namespace App\Domains\Transactions\Services\TransactionResolution\Transfers;

use App\Domains\Transactions\Services\TransactionResolution\TransactionResolution;
use App\Models\TransactionReconsilation;
use App\Models\WebHookEvent;

class TransferCompleted extends TransactionResolution
{
    public function execute(?WebHookEvent $webhook = null , ?TransactionReconsilation $reconsilation = null)
    {
        parent::execute(webhook: $webhook , reconsilation: $reconsilation);
    }
}
