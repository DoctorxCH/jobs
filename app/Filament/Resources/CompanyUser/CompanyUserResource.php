<?php

namespace App\Filament\Resources\CompanyUser;

use App\Filament\Resources\CompanyUser\CompanyUserResource\Pages;
use App\Filament\Resources\CompanyUser\Schemas\CompanyUserForm;
use App\Filament\Resources\CompanyUser\Tables\CompanyUserTable;
use App\Models\CompanyUser;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompanyUserResource extends Resource
{
    protected static ?string $model = CompanyUser::class;

    protected static ?string $navigationLabel = 'Team Members';
    protected static ?string $modelLabel = 'Team Member';
    protected static ?string $pluralModelLabel = 'Team Members';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    public static function form(Schema $schema): Schema
    {
        return CompanyUserForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanyUserTable::schema($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyUsers::route('/'),
            'create' => Pages\CreateCompanyUser::route('/create'),
            'edit' => Pages\EditCompanyUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        if (! $user || ! $user->company_id) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()->where('company_id', $user->company_id);
    }

    public static function canViewAny(): bool
    {
        return static::isOwner();
    }

    public static function canCreate(): bool
    {
        return static::isOwner();
    }

    public static function canEdit($record): bool
    {
        return static::isOwner();
    }

    public static function canDelete($record): bool
    {
        return static::isOwner();
    }

    protected static function isOwner(): bool
    {
        $user = auth()->user();

        if (! $user || ! $user->company_id) {
            return false;
        }

        return $user->companyMemberships()
            ->where('company_id', $user->company_id)
            ->where('role', 'owner')
            ->where('status', 'active')
            ->exists();
    }
}
