<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\BvnConsent;
use App\BvnConsentStatus;

class SimulateBvnUpdateVerificationStatus implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected BvnConsent $bvnConsent)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->bvnConsent->update([
            'status' => BvnConsentStatus::APPROVED,
        ]);
    }
}
