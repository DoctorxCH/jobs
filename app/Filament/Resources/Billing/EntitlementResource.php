<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\EntitlementResource\Pages;
use App\Models\Billing\Entitlement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EntitlementResource extends Resource
{
    protected static ?string $model = Entitlement::class;

    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Entitlements';
    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('type')->disabled(),
            Forms\Components\TextInput::make('quantity_total')->disabled(),
            Forms\Components\TextInput::make('quantity_remaining')->disabled(),
            Forms\Components\TextInput::make('starts_at')->disabled(),
            Forms\Components\TextInput::make('ends_at')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('company.legal_name')->label('Company'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('quantity_remaining'),
                Tables\Columns\TextColumn::make('starts_at')->dateTime(),
                Tables\Columns\TextColumn::make('ends_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntitlements::route('/'),
            'view' => Pages\ViewEntitlement::route('/{record}'),
        ];
    }
}
