<?php

namespace App\Filament\Resources\Billing\CouponResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CouponScopesRelationManager extends RelationManager
{
    protected static string $relationship = 'scopes';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('scope_type')
                ->options([
                    'global' => 'Global',
                    'company' => 'Company',
                    'product' => 'Product',
                    'category' => 'Category',
                ])
                ->required(),
            Forms\Components\TextInput::make('scope_id')
                ->numeric()
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('scope_type'),
                Tables\Columns\TextColumn::make('scope_id'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
