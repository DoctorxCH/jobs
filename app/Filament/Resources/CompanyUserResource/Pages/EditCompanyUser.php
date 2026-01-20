<?php

namespace App\Filament\Resources\CompanyUserResource\Pages;

use App\Filament\Resources\CompanyUserResource;
use App\Services\PermissionService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyUser extends EditRecord
{
    protected static string $resource = CompanyUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => PermissionService::can(CompanyUserResource::getPermissionKey(), 'delete')),
        ];
    }
}
