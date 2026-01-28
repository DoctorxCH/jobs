<?php

namespace App\Filament\Resources\Billing\CouponResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CouponRedemptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'redemptions';

    public function canCreate(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('company_id')->sortable(),
                Tables\Columns\TextColumn::make('user_id')->sortable(),
                Tables\Columns\TextColumn::make('order_id')->sortable(),
                Tables\Columns\TextColumn::make('invoice_id')->sortable(),
                Tables\Columns\TextColumn::make('discount_minor')->sortable(),
                Tables\Columns\TextColumn::make('currency')->sortable(),
                Tables\Columns\TextColumn::make('redeemed_at')->dateTime()->sortable(),
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
