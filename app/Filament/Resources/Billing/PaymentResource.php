<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\PaymentResource\Pages;
use App\Filament\Resources\Billing\PaymentResource\Schemas\PaymentForm;
use App\Filament\Resources\Billing\PaymentResource\Tables\PaymentsTable;
use App\Models\Billing\Payment;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationLabel = 'Payments';
    protected static ?string $modelLabel = 'Payment';
    protected static ?string $pluralModelLabel = 'Payments';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Schema $schema): Schema
    {
        return PaymentForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }
}
