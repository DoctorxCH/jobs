<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\InvoiceResource\Pages;
use App\Models\Billing\Invoice;
use App\Services\Billing\InvoiceService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Invoices';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('status')->disabled(),
            Forms\Components\TextInput::make('currency')->disabled(),
            Forms\Components\TextInput::make('total_gross_minor')->disabled(),
            Forms\Components\TextInput::make('payment_reference')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('company.legal_name')->label('Company')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('total_gross_minor')->label('Total'),
                Tables\Columns\TextColumn::make('issued_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('export')
                    ->label('Export to SuperFaktura')
                    ->visible(fn (Invoice $record) => $record->external?->sync_status !== 'ok')
                    ->action(fn (Invoice $record) => app(InvoiceService::class)->exportToSuperfaktura($record)),
                Tables\Actions\Action::make('retry')
                    ->label('Retry Export')
                    ->visible(fn (Invoice $record) => $record->external?->sync_status === 'failed')
                    ->action(fn (Invoice $record) => app(InvoiceService::class)->retryExport($record)),
                Tables\Actions\Action::make('markPaid')
                    ->label('Mark Paid')
                    ->visible(fn (Invoice $record) => $record->status === 'issued_unpaid')
                    ->form([
                        Forms\Components\TextInput::make('amount_minor')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->default(fn (Invoice $record) => $record->total_gross_minor),
                        Forms\Components\TextInput::make('bank_reference')
                            ->label('Bank Reference')
                            ->maxLength(255)
                            ->nullable(),
                    ])
                    ->action(function (Invoice $record, array $data) {
                        app(InvoiceService::class)->markPaid(
                            $record,
                            (int) $data['amount_minor'],
                            $data['bank_reference'] ?? null,
                            auth()->user()
                        );
                    }),
                Tables\Actions\Action::make('markOverdue')
                    ->label('Mark Overdue')
                    ->visible(fn (Invoice $record) => $record->status === 'issued_unpaid')
                    ->action(fn (Invoice $record) => app(InvoiceService::class)->markOverdue($record)),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->visible(fn (Invoice $record) => $record->status === 'issued_unpaid')
                    ->action(fn (Invoice $record) => app(InvoiceService::class)->cancelUnpaid($record)),
                Tables\Actions\Action::make('creditNote')
                    ->label('Create Credit Note')
                    ->visible(fn (Invoice $record) => $record->status === 'paid')
                    ->form([
                        Forms\Components\Textarea::make('reason')->required(),
                    ])
                    ->action(function (Invoice $record, array $data) {
                        app(InvoiceService::class)->createCreditNoteFromPaid($record, $data['reason']);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
