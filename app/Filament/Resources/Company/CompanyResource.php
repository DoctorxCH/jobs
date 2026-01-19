<?php

namespace App\Filament\Resources\Company;

use App\Filament\Resources\Company\CompanyResource\Pages;
use App\Filament\Resources\Company\Schemas\CompanyForm;
use App\Filament\Resources\Company\Tables\CompanyTable;
use App\Models\Company;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationLabel = 'Companies';
    protected static ?string $modelLabel = 'Company';
    protected static ?string $pluralModelLabel = 'Companies';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Schema $schema): Schema
    {
        return CompanyForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanyTable::schema($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        if (! $user) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        if ($user->company_id) {
            return parent::getEloquentQuery()->whereKey($user->company_id);
        }

        return parent::getEloquentQuery()->whereRaw('1 = 0');
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->companyMemberships()
            ->where('company_id', $record->getKey())
            ->where('role', 'owner')
            ->where('status', 'active')
            ->exists();
    }
}
