<?php

namespace App\Filament\Resources\Billing\EntitlementResource\Tables;

use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EntitlementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label('Company')
                    ->sortable(),
                TextColumn::make('type')
                    ->sortable(),
                TextColumn::make('quantity_total')
                    ->sortable(),
                TextColumn::make('quantity_remaining')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime(),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
