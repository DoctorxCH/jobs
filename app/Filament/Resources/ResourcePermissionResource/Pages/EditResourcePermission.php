<?php

namespace App\Filament\Resources\ResourcePermissionResource\Pages;

use App\Filament\Resources\ResourcePermissionResource;
use App\Services\PermissionService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResourcePermission extends EditRecord
{
    protected static string $resource = ResourcePermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(fn () => PermissionService::invalidateCache()),
        ];
    }

    protected function afterSave(): void
    {
        PermissionService::invalidateCache();
    }
}
