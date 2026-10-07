<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Domains\Transactions\Services\Processors\FlutterwaveWebhookProcessor;
use App\Models\WebHookEvent;

class ProcessFlutterwaveWebhook implements ShouldQueue
{
    use Queueable;

    public function __construct(
         public int $webhookEventId
    )
    {
        //
    }


    public function handle(
        FlutterwaveWebhookProcessor $processor
    ): void {
        $webhook = WebHookEvent::findOrFail(
            $this->webhookEventId
        );

        $processor->process($webhook);
    }
}
