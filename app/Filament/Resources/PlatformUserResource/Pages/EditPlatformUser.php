<?php

namespace App\Filament\Resources\PlatformUserResource\Pages;

use App\Filament\Resources\PlatformUserResource;
use App\Services\PermissionService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlatformUser extends EditRecord
{
    protected static string $resource = PlatformUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => PermissionService::can(PlatformUserResource::getPermissionKey(), 'delete')),
        ];
    }
}
