<x-filament-panels::page>
    <div class="max-w-xl mx-auto w-full">

            <div class="flex flex-col items-center justify-center space-y-4">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Available Balance
                </span>

                <div class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    {{ $this->getFormattedBalanceProperty() }}
                </div>

                @if (! $this->getAdminUser())
                    <p class="text-xs text-danger-600 dark:text-danger-400 font-medium">
                        Warning: No admin user account found in database.
                    </p>
                @elseif ($this->getAvailableBalanceInKobo() <= 0)
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Insufficient balance for cashout.
                    </p>
                @endif

                <div class="pt-2">
                    {{ $this->cashoutAction }}
                </div>
            </div>

    </div>
</x-filament-panels::page>
