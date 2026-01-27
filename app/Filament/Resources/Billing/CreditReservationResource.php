<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\CreditReservationResource\Pages;
use App\Filament\Resources\Billing\CreditReservationResource\Schemas\CreditReservationForm;
use App\Filament\Resources\Billing\CreditReservationResource\Tables\CreditReservationsTable;
use App\Models\Billing\CreditReservation;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CreditReservationResource extends Resource
{
    protected static ?string $model = CreditReservation::class;

    protected static ?string $navigationLabel = 'Credit Reservations';
    protected static ?string $modelLabel = 'Credit Reservation';
    protected static ?string $pluralModelLabel = 'Credit Reservations';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Schema $schema): Schema
    {
        return CreditReservationForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return CreditReservationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCreditReservations::route('/'),
            'view' => Pages\ViewCreditReservation::route('/{record}'),
        ];
    }
}
