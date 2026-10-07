<?php
namespace App\Domains\Reconsilation\Services;
use App\Domains\Transactions\Services\TransactionResolution\TransactionResolution;
use App\Models\TransactionReconsilation;
use App\Models\WebHookEvent;

class TransactionResolutionService extends TransactionResolution
{
     public function execute(?WebHookEvent $webhook = null , ?TransactionReconsilation $reconsilation = null)
    {
        parent::execute(webhook: $webhook , reconsilation: $reconsilation);
    }
}