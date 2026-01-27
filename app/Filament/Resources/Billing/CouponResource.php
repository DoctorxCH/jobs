<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\CouponResource\Pages;
use App\Filament\Resources\Billing\CouponResource\Schemas\CouponForm;
use App\Filament\Resources\Billing\CouponResource\Tables\CouponsTable;
use App\Models\Billing\Coupon;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationLabel = 'Coupons';
    protected static ?string $modelLabel = 'Coupon';
    protected static ?string $pluralModelLabel = 'Coupons';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Schema $schema): Schema
    {
        return CouponForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return CouponsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
