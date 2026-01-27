<?php

namespace App\Filament\Resources\Billing;

use App\Filament\Resources\Billing\SettingResource\Pages;
use App\Filament\Resources\Billing\SettingResource\Schemas\SettingForm;
use App\Filament\Resources\Billing\SettingResource\Tables\SettingsTable;
use App\Models\Billing\Setting;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationLabel = 'Billing Settings';
    protected static ?string $modelLabel = 'Billing Setting';
    protected static ?string $pluralModelLabel = 'Billing Settings';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Schema $schema): Schema
    {
        return SettingForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return SettingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
