<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\TaxRateResource\Pages;
use App\Models\Billing\TaxRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaxRateResource extends Resource
{
    protected static ?string $model = TaxRate::class;

    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Tax Rates';
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('country_code')->required()->maxLength(2),
            Forms\Components\Select::make('tax_class_id')
                ->relationship('taxClass', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('rate_percent')->numeric()->required(),
            Forms\Components\DatePicker::make('valid_from')->required(),
            Forms\Components\DatePicker::make('valid_to')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country_code'),
                Tables\Columns\TextColumn::make('taxClass.name')->label('Tax Class'),
                Tables\Columns\TextColumn::make('rate_percent'),
                Tables\Columns\TextColumn::make('valid_from')->date(),
                Tables\Columns\TextColumn::make('valid_to')->date(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaxRates::route('/'),
            'create' => Pages\CreateTaxRate::route('/create'),
            'edit' => Pages\EditTaxRate::route('/{record}/edit'),
        ];
    }
}
