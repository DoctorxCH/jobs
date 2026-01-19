<?php

namespace App\Filament\Resources\PlatformUser;

use App\Filament\Resources\PlatformUser\PlatformUserResource\Pages;
use App\Filament\Resources\PlatformUser\Schemas\PlatformUserForm;
use App\Filament\Resources\PlatformUser\Tables\PlatformUserTable;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PlatformUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Platform Users';
    protected static ?string $modelLabel = 'Platform User';
    protected static ?string $pluralModelLabel = 'Platform Users';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    public static function form(Schema $schema): Schema
    {
        return PlatformUserForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return PlatformUserTable::schema($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlatformUsers::route('/'),
            'create' => Pages\CreatePlatformUser::route('/create'),
            'edit' => Pages\EditPlatformUser::route('/{record}/edit'),
        ];
    }
}
