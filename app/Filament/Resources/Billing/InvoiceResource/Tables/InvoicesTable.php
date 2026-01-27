<?php

namespace App\Filament\Resources\Billing\InvoiceResource\Tables;

use App\Models\Billing\Invoice;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesTable
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
                TextColumn::make('total_gross_minor_snapshot')
                    ->label('Total (minor)')
                    ->sortable(),
                TextColumn::make('issued_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('exportSuperFaktura')
                    ->label('Export/Retry SF')
                    ->requiresConfirmation()
                    ->action(function (Invoice $record): void {
                        // TODO: export via SuperFakturaService.
                    }),
                Action::make('downloadPdf')
                    ->label('Download PDF')
                    ->action(function (Invoice $record): void {
                        // TODO: redirect or stream PDF.
                    }),
                Action::make('markPaid')
                    ->label('Mark Paid')
                    ->requiresConfirmation()
                    ->action(function (Invoice $record): void {
                        // TODO: create payment and mark invoice paid.
                    }),
                Action::make('markOverdue')
                    ->label('Mark Overdue')
                    ->requiresConfirmation()
                    ->action(function (Invoice $record): void {
                        // TODO: mark invoice overdue.
                    }),
                Action::make('cancelInvoice')
                    ->label('Cancel')
                    ->requiresConfirmation()
                    ->action(function (Invoice $record): void {
                        // TODO: cancel unpaid invoice.
                    }),
                Action::make('createCreditNote')
                    ->label('Create Credit Note')
                    ->requiresConfirmation()
                    ->action(function (Invoice $record): void {
                        // TODO: create credit note for paid invoice.
                    }),
            ])
            ->defaultSort('issued_at', 'desc');
    }
}
