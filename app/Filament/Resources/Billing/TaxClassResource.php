<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\TaxClassResource\Pages;
use App\Models\Billing\TaxClass;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaxClassResource extends Resource
{
    protected static ?string $model = TaxClass::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Tax classes';
    protected static ?int $navigationSort = 70;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Tax class')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(150),

                    Forms\Components\TextInput::make('key')
                        ->maxLength(150)
                        ->helperText('nothing special, just a numeric key for easier identification.'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('key')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListTaxClasses::route('/'),
            'create' => Pages\CreateTaxClass::route('/create'),
            'edit' => Pages\EditTaxClass::route('/{record}/edit'),
        ];
    }
}
