<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use App\TransactionStatus;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Section;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_reference')
                    ->label('Ref')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('direction')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'credit' => 'success',
                        'debit' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('amount')
                    ->money('NGN', divideBy: 100)
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(TransactionStatus $state) => match ($state) {
                        TransactionStatus::SUCCESSFUL => 'success',
                        TransactionStatus::PENDING => 'warning',
                        TransactionStatus::FAILED => 'danger',
                        default => 'gray',
                    }),
                // Tables\Columns\TextColumn::make('initiated_at')->dateTime()->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->schema(self::getInfolistSchema()),
            ]);
    }


    public static function getInfolistSchema()
    {
        return [
            Section::make('Transaction Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('transaction_reference')->copyable(),
                    TextEntry::make('user.full_name')->label('User'),
                    TextEntry::make('account_id')->label('Account ID'),
                    TextEntry::make('type')->badge(),
                    TextEntry::make('direction')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'credit' => 'success',
                            'debit' => 'danger',
                            default => 'gray',
                        }),
                    TextEntry::make('amount')->money('NGN', divideBy: 100),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn($state): string => match ($state) {
                            TransactionStatus::SUCCESSFUL => 'success',
                            TransactionStatus::PENDING => 'warning',
                            TransactionStatus::FAILED => 'danger',
                            default => 'gray',
                        }),
                    TextEntry::make('recipient'),
                    TextEntry::make('base_currency')->label('Base Currency'),
                ]),
            Section::make('Timestamps & Meta')
                ->columns(3)
                ->schema([
                    TextEntry::make('initiated_at')->dateTime(),
                    TextEntry::make('completed_at')->dateTime(),
                    TextEntry::make('failed_at')->dateTime(),
                    TextEntry::make('failure_reason')->columnSpanFull(),
                    TextEntry::make('description')->columnSpanFull(),
                    KeyValueEntry::make('metadata')
                        ->columnSpanFull()
                        ->keyLabel('Property')
                        ->valueLabel('Value'),
                ])
        ];
    }
}
