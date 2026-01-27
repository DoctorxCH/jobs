<?php

namespace App\Filament\Resources\Billing\CouponResource\Tables;

use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('discount_type')
                    ->sortable(),
                TextColumn::make('discount_value')
                    ->sortable(),
                IconColumn::make('active')
                    ->boolean(),
                TextColumn::make('valid_from')
                    ->dateTime(),
                TextColumn::make('valid_to')
                    ->dateTime(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('code');
    }
}
