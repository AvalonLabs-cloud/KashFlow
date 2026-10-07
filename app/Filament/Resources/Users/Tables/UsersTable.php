<?php

namespace App\Filament\Resources\Users\Tables;


use Filament\Actions\ViewAction;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Filament\Actions\Action;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->sortable()
                    ->searchable(['full_name', 'first_name', 'last_name']),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->boolean()
                    ->state(fn(User $record): bool => filled($record->email_verified_at)),
                Tables\Columns\IconColumn::make('phone_verified_at')
                    ->label('Phone Verified')
                    ->boolean()
                    ->state(fn(User $record): bool => filled($record->phone_verified_at)),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
                // Tables\Columns\TextColumn::make('balance')
                //     ->label('Total Balance')
                //     ->state(fn (User $record, UserBalanceService $service) => $service->getBalanceInKobo($record))
                //     ->money('NGN', divideBy: 100),
                Tables\Columns\TextColumn::make('transactions_count')
                    ->counts('transactions') // Natively handles withCount('transactions')
                    ->label('Transactions')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email Verification')
                    ->nullable(),
                Tables\Filters\TernaryFilter::make('phone_verified_at')
                    ->label('Phone Verification')
                    ->nullable(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Account Status'),
            ])->recordActions([
                ViewAction::make(),
                Action::make('toggle_status')
                    ->label(fn(User $record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->color(fn(User $record) => $record->is_active ? 'danger' : 'success')
                    ->icon(fn(User $record) => $record->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading(fn(User $record) => $record->is_active ? 'Deactivate Account' : 'Activate Account')
                    ->modalDescription('Are you sure you want to change the status of this account? Financial records will remain intact.')
                    ->action(function (User $record) {
                        $record->update(['is_active' => !$record->is_active]);

                        $action = $record->is_active ? 'activated' : 'deactivated';
                        Log::info("Admin user [ID: " . auth('web')->id() . "] $action account for User [ID: {$record->id}]");
                    })

            ]);
    }

    // public static function getPages(): array
    // {
    //     return [
    //         'index' => Pages\ListUsers::route('/'),
    //         'view' => Pages\ViewUser::route('/{record}'),
    //     ];
    // }
}
