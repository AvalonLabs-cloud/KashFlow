<?php

namespace App\Filament\Pages;

use App\Models\TransactionFeeSetting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionFeeSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.transaction-fee-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = TransactionFeeSetting::firstOrCreate(
            ['id' => 1],
            [
                'percentage' => 0,
                'is_active' => false,
            ]
        );

        $this->form->fill([
            'percentage' => $setting->percentage,
            'is_active' => $setting->is_active,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Global Transaction Fee')
                    ->description(
                        'Configure the global percentage fee automatically deducted on transactions.'
                    )
                    ->components([
                        TextInput::make('percentage')
                            ->label('Fee Percentage (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(20)
                            ->step(0.01)
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Enable Fee Deduction'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $setting = TransactionFeeSetting::firstOrCreate(
            ['id' => 1],
            [
                'percentage' => 0,
                'is_active' => false,
            ]
        );

        $setting->update([
            'percentage' => $state['percentage'],
            'is_active' => $state['is_active'],
        ]);

        Notification::make()
            ->success()
            ->title('Settings saved successfully.')
            ->send();
    }
}