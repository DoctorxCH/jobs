<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\ProductPriceResource\Pages;
use App\Filament\Resources\Billing\ProductPriceResource\Schemas\ProductPriceForm;
use App\Filament\Resources\Billing\ProductPriceResource\Tables\ProductPricesTable;
use App\Models\Billing\ProductPrice;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProductPriceResource extends Resource
{
    protected static ?string $model = ProductPrice::class;

    protected static ?string $navigationLabel = 'Product Prices';
    protected static ?string $modelLabel = 'Product Price';
    protected static ?string $pluralModelLabel = 'Product Prices';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-currency-euro';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Schema $schema): Schema
    {
        return ProductPriceForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductPricesTable::configure($table);
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
