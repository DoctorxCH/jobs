<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\ProductPriceResource\Pages;
use App\Models\Billing\ProductPrice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductPriceResource extends Resource
{
    protected static ?string $model = ProductPrice::class;

    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Product Prices';
    protected static ?string $navigationIcon = 'heroicon-o-currency-euro';
    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('product_id')
                ->relationship('product', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('currency')
                ->required()
                ->maxLength(3),
            Forms\Components\TextInput::make('unit_net_amount_minor')
                ->required()
                ->numeric(),
            Forms\Components\Select::make('tax_class_id')
                ->relationship('taxClass', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\DateTimePicker::make('valid_from')
                ->required(),
            Forms\Components\DateTimePicker::make('valid_to'),
            Forms\Components\Toggle::make('active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('product.name')->label('Product')->searchable(),
                Tables\Columns\TextColumn::make('currency'),
                Tables\Columns\TextColumn::make('unit_net_amount_minor')->label('Unit Net'),
                Tables\Columns\IconColumn::make('active')->boolean(),
                Tables\Columns\TextColumn::make('valid_from')->dateTime(),
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
            'index' => Pages\ListProductPrices::route('/'),
            'create' => Pages\CreateProductPrice::route('/create'),
            'edit' => Pages\EditProductPrice::route('/{record}/edit'),
        ];
    }
}
