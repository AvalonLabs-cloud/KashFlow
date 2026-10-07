<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Select;

class AdminBankAccount extends Page implements HasForms
{
    use InteractsWithForms;
    // protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Admin Bank Account';
    protected static ?string $title = 'Admin Bank Account Details';
    protected static ?string $slug = 'admin-bank-account';
    protected  string $view = 'filament.pages.admin-bank-account';

    public ?array $data = [];

    public function mount(): void
    {
        $admin = User::where('is_admin', true)->first();

        if ($admin) {
            $this->form->fill([
                'account_number' => $admin->account_number,
                'bank_name' => $admin->bank_name,
            ]);
        } else {
            $this->form->fill();
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ComponentsSection::make('Bank Account Details')
                    ->description('Manage the designated application admin account details.')
                    ->schema([
                        TextInput::make('account_number')
                            ->label('Account Number')
                            ->required()
                            ->maxLength(255),


                        Select::make('bank_name')
                            ->label('Select Bank')
                            ->options(config('bankList'))
                            ->searchable()
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $admin = User::where('is_admin', true)->first();
        if (!$admin) {
            Notification::make()
                ->title('Admin user not found.')
                ->danger()
                ->send();

            return;
        }

        $admin->update([
            'account_number' => $data['account_number'],
            'bank_name' => $data['bank_name'],
        ]);

        Notification::make()
            ->title('Bank account details saved successfully.')
            ->success()
            ->send();
    }
}
