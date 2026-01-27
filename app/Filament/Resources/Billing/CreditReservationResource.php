<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\CreditReservationResource\Pages;
use App\Models\Billing\CreditReservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CreditReservationResource extends Resource
{
    protected static ?string $model = CreditReservation::class;

    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Credit Reservations';
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('amount')->disabled(),
            Forms\Components\TextInput::make('status')->disabled(),
            Forms\Components\TextInput::make('expires_at')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('company.legal_name')->label('Company'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('expires_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCreditReservations::route('/'),
            'view' => Pages\ViewCreditReservation::route('/{record}'),
        ];
    }
}
