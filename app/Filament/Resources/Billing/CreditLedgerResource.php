<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\CreditLedgerResource\Pages;
use App\Filament\Resources\Billing\CreditLedgerResource\Schemas\CreditLedgerForm;
use App\Filament\Resources\Billing\CreditLedgerResource\Tables\CreditLedgerTable;
use App\Models\Billing\CreditLedger;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CreditLedgerResource extends Resource
{
    protected static ?string $model = CreditLedger::class;

    protected static ?string $navigationLabel = 'Credit Ledger';
    protected static ?string $modelLabel = 'Credit Ledger Entry';
    protected static ?string $pluralModelLabel = 'Credit Ledger';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Schema $schema): Schema
    {
        return CreditLedgerForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return CreditLedgerTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCreditLedgerEntries::route('/'),
            'view' => Pages\ViewCreditLedgerEntry::route('/{record}'),
        ];
    }
}
