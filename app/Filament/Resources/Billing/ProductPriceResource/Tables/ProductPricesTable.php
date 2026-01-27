<?php

namespace App\Filament\Resources\Billing\ProductPriceResource\Tables;

use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductPricesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('currency')
                    ->sortable(),
                TextColumn::make('unit_net_amount_minor')
                    ->label('Unit Net (minor)')
                    ->sortable(),
                TextColumn::make('taxClass.name')
                    ->label('Tax Class'),
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
            ->defaultSort('valid_from', 'desc');
    }
}
