<?php

namespace App\Filament\Resources\ResourcePermissionResource\Pages;

use App\Filament\Resources\ResourcePermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResourcePermissions extends ListRecords
{
    protected static string $resource = ResourcePermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
