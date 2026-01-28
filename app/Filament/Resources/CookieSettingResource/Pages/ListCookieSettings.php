<?php

namespace App\Filament\Resources\CookieSettingResource\Pages;

use App\Filament\Resources\CookieSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCookieSettings extends ListRecords
{
    protected static string $resource = CookieSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
