<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\ProductResource\Pages;
use App\Filament\Resources\Billing\ProductResource\Schemas\ProductForm;
use App\Filament\Resources\Billing\ProductResource\Tables\ProductsTable;
use App\Models\Billing\Product;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationLabel = 'Products';
    protected static ?string $modelLabel = 'Product';
    protected static ?string $pluralModelLabel = 'Products';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Schema $schema): Schema
    {
        return ProductForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
