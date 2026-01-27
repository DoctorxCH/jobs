<?php

namespace App\Filament\Resources\Billing\CouponResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CouponRedemptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'redemptions';

    public static function canCreate(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id'),
                Tables\Columns\TextColumn::make('invoice_id'),
                Tables\Columns\TextColumn::make('company_id'),
                Tables\Columns\TextColumn::make('user_id'),
                Tables\Columns\TextColumn::make('discount_minor'),
                Tables\Columns\TextColumn::make('currency'),
                Tables\Columns\TextColumn::make('redeemed_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }
}
