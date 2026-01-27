<?php

namespace App\Filament\Resources\Billing\OrderResource\Tables;

use App\Models\Billing\Order;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label('Company')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->sortable(),
                TextColumn::make('currency')
                    ->sortable(),
                TextColumn::make('total_gross_minor')
                    ->label('Total (minor)')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('createInvoice')
                    ->label('Create Invoice')
                    ->requiresConfirmation()
                    ->action(function (Order $record): void {
                        // TODO: create invoice from order snapshot.
                    }),
                Action::make('exportSuperFaktura')
                    ->label('Export to SF')
                    ->requiresConfirmation()
                    ->action(function (Order $record): void {
                        // TODO: export via SuperFakturaService.
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
