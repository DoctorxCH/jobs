<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\OrderResource\Pages;
use App\Filament\Resources\Billing\OrderResource\Schemas\OrderForm;
use App\Filament\Resources\Billing\OrderResource\Tables\OrdersTable;
use App\Models\Billing\Order;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationLabel = 'Orders';
    protected static ?string $modelLabel = 'Order';
    protected static ?string $pluralModelLabel = 'Orders';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Schema $schema): Schema
    {
        return OrderForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
