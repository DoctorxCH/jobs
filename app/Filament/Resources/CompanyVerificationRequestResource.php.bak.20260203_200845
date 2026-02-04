<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyVerificationRequestResource\Pages;
use App\Filament\Resources\CompanyVerificationRequestResource\Tables\CompanyVerificationRequestsTable;
use App\Models\CompanyVerificationRequest;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class CompanyVerificationRequestResource extends Resource
{
    protected static ?string $model = CompanyVerificationRequest::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Verification Center';
    protected static ?string $modelLabel = 'Verification Request';
    protected static ?string $pluralModelLabel = 'Verification Requests';
    protected static ?string $navigationGroup = 'Company';
    protected static ?int $navigationSort = 30;

    public static function canViewAny(): bool
    {
        return static::isPlatformAdmin();
    }

    public static function table(Table $table): Table
    {
        return CompanyVerificationRequestsTable::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyVerificationRequests::route('/'),
        ];
    }

    private static function isPlatformAdmin(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return method_exists($user, 'hasAnyRole') && $user->hasAnyRole([
            'platform.super_admin',
            'platform.admin',
            'platform.manager',
            'platform.finance',
            'platform.support',
        ]);
    }
}
