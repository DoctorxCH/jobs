<?php

namespace App\Filament\Resources\ResourcePermissionResource\Pages;

use App\Filament\Resources\ResourcePermissionResource;
use App\Services\PermissionService;
use Filament\Resources\Pages\CreateRecord;

class CreateResourcePermission extends CreateRecord
{
    protected static string $resource = ResourcePermissionResource::class;

    protected function afterCreate(): void
    {
        PermissionService::invalidateCache();
    }
}
