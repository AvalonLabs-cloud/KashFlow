<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\AdminTransaction;
use App\Models\AdminHeldBalance;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\AdminTransactionStatus;
use App\AdminHeldBalanceStatus;

class AdminCashout extends Page
{
    protected static ?string $navigationLabel = 'Admin Cashout';
    protected static ?string $title = 'Admin Cashout Management';
    protected static ?string $slug = 'admin-cashout';
    protected  string $view = 'filament.pages.admin-cash-out';

    public function getAdminUser(): ?User
    {
        return User::where('is_admin', true)->first();
    }


    public function getAvailableBalanceInKobo(): int
    {
        $admin = $this->getAdminUser();

        if (!$admin) {
            return 0;
        }

        $totalCredits = (int) AdminTransaction::where('admin_id', $admin->id)
            ->where('status', AdminTransactionStatus::Success)
            ->where('direction', 'credit')
            ->sum('amount');

        $totalDebits = (int) AdminTransaction::where('admin_id', $admin->id)
            ->where('status', AdminTransactionStatus::Success)
            ->where('direction', 'debit')
            ->sum('amount');

        $totalHeld = (int) AdminHeldBalance::where('status', AdminHeldBalanceStatus::Active)
            ->sum('amount');

        return $totalCredits - $totalDebits - $totalHeld;
    }


    public function getAvailableBalanceInNaira(): float
    {
        return $this->getAvailableBalanceInKobo() / 100;
    }


    public function getFormattedBalanceProperty(): string
    {
        return '₦' . number_format($this->getAvailableBalanceInNaira(), 2);
    }


    public function cashoutAction(): Action
    {
        return Action::make('cashout')
            ->label('Cashout')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading('Confirm Cashout Request')
            ->modalDescription('Are you sure you want to process a cashout for the entire available balance?')
            ->disabled(fn(): bool => $this->getAdminUser() === null || $this->getAvailableBalanceInKobo() <= 0)
            ->action(function (): void {
                $this->processCashout();
            });
    }


    public function processCashout(): void
    {
        $admin = $this->getAdminUser();


        if (! $admin) {
            Notification::make()
                ->title('Admin user not found.')
                ->danger()
                ->send();

            return;
        }

        $balanceInKobo = $this->getAvailableBalanceInKobo();


        if ($balanceInKobo <= 0) {
            Notification::make()
                ->title('Cashout Failed')
                ->body('Available balance must be greater than zero to initiate a cashout.')
                ->danger()
                ->send();

            return;
        }

        try {

            $endpointUrl = url('/admin/cashout');

            $response = Http::acceptJson()
                ->timeout(15)
                ->post($endpointUrl, [
                    'amount' => $balanceInKobo,
                    'account_number' => $admin->account_number,
                    'bank_name' => $admin->bank_name,
                ]);

            if ($response->successful()) {
                Notification::make()
                    ->title('Cashout request submitted successfully.')
                    ->success()
                    ->send();
            } else {
                $errorMessage = $response->json('message') ?? 'The cashout endpoint rejected the request.';

                Notification::make()
                    ->title('Cashout Failed')
                    ->body($errorMessage)
                    ->danger()
                    ->send();
            }
        } catch (Throwable $e) {
            Log::error('Cashout processing exception: ' . $e->getMessage());

            Notification::make()
                ->title('Cashout Error')
                ->body('An unexpected error occurred while communicating with the cashout service. Please try again.')
                ->danger()
                ->send();
        }
    }

    public function mount(): void
    {

        if (session()->has('error')) {
            Notification::make()
                ->title(session('error'))
                ->success()
                ->send();
        }
    }
}
