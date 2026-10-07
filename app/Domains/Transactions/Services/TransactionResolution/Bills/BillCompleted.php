<?php
namespace App\Domains\Transactions\Services\TransactionResolution\Bills;
use App\Domains\Transactions\Services\TransactionResolution\TransactionResolution;
use App\Models\TransactionReconsilation;
use App\Models\WebHookEvent;

class BillCompleted extends TransactionResolution {
    public function execute(?WebHookEvent $webhook = null , ?TransactionReconsilation $reconsilation = null)
    {
        parent::execute(webhook: $webhook , reconsilation: $reconsilation);
    }
}
