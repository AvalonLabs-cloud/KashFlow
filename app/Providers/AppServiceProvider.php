<?php

namespace App\Providers;

use App\Integrations\PhoneVerification\Contracts\PhoneNumberVerification;
use App\Integrations\PhoneVerification\Flutterwave\FlutterWavePhoneVerification;
use App\Models\LedgerEntry;
use App\Models\Transfer;
use App\Observers\LedgerEntryObserver;
use App\Observers\TransferObserver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PhoneNumberVerification::class, FlutterWavePhoneVerification::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        Transfer::observe(TransferObserver::class);
        // LedgerEntry::observe(LedgerEntryObserver::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
