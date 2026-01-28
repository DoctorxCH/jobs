<?php

namespace App\Filament\Resources\TopPartnerResource\Pages;

use App\Filament\Resources\TopPartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTopPartners extends ListRecords
{
    protected static string $resource = TopPartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
