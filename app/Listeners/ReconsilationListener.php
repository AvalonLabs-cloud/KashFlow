<?php

namespace App\Listeners;

use App\Events\Reconsilation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\TransactionReconsilation;
use App\Domains\Reconsilation\Services\TransactionResolutionService;

class ReconsilationListener implements ShouldQueue
{
    use InteractsWithQueue; 

    public function __construct(
        protected TransactionResolutionService $transactionResolutionService
    )
    {
        
    }

    public function handle(Reconsilation $event): void
    {
        $reconsilation = TransactionReconsilation::find($event->reconsilationId);
        $this->transactionResolutionService->execute(reconsilation:$reconsilation);
    }
}
