<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\EntitlementResource\Pages;
use App\Filament\Resources\Billing\EntitlementResource\Schemas\EntitlementForm;
use App\Filament\Resources\Billing\EntitlementResource\Tables\EntitlementsTable;
use App\Models\Billing\Entitlement;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class EntitlementResource extends Resource
{
    protected static ?string $model = Entitlement::class;

    protected static ?string $navigationLabel = 'Entitlements';
    protected static ?string $modelLabel = 'Entitlement';
    protected static ?string $pluralModelLabel = 'Entitlements';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Schema $schema): Schema
    {
        return EntitlementForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return EntitlementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntitlements::route('/'),
            'view' => Pages\ViewEntitlement::route('/{record}'),
        ];
    }
}
