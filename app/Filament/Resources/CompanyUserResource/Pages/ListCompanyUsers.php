<?php

namespace App\Filament\Resources\CompanyUserResource\Pages;

use App\Filament\Resources\CompanyUserResource;
use App\Services\PermissionService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyUsers extends ListRecords
{
    protected static string $resource = CompanyUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => PermissionService::can(CompanyUserResource::getPermissionKey(), 'create')),
        ];
    }
}
