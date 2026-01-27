<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\PaymentResource\Pages;
use App\Models\Billing\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Payments';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('status')->disabled(),
            Forms\Components\TextInput::make('amount_minor')->disabled(),
            Forms\Components\TextInput::make('currency')->disabled(),
            Forms\Components\TextInput::make('bank_reference')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('invoice_id')->label('Invoice'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('amount_minor'),
                Tables\Columns\TextColumn::make('currency'),
                Tables\Columns\TextColumn::make('received_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }
}
