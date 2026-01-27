<?php

namespace App\Filament\Resources\Billing\PaymentResource\Tables;

use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('invoice_id')
                    ->sortable(),
                TextColumn::make('status')
                    ->sortable(),
                TextColumn::make('amount_minor')
                    ->label('Amount (minor)')
                    ->sortable(),
                TextColumn::make('currency')
                    ->sortable(),
                TextColumn::make('received_at')
                    ->dateTime(),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
