<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\CreditLedgerResource\Pages;
use App\Models\Billing\CreditLedger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CreditLedgerResource extends Resource
{
    protected static ?string $model = CreditLedger::class;

    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Credit Ledger';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('change')->disabled(),
            Forms\Components\TextInput::make('reason')->disabled(),
            Forms\Components\TextInput::make('reference_type')->disabled(),
            Forms\Components\TextInput::make('reference_id')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('company.legal_name')->label('Company'),
                Tables\Columns\TextColumn::make('change'),
                Tables\Columns\TextColumn::make('reason'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCreditLedger::route('/'),
            'view' => Pages\ViewCreditLedger::route('/{record}'),
        ];
    }
}
