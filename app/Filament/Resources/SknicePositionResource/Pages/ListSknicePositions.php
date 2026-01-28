<?php

namespace App\Filament\Resources\SknicePositionResource\Pages;

use App\Filament\Resources\SknicePositionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSknicePositions extends ListRecords
{
    protected static string $resource = SknicePositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => SknicePositionResource::canCreate()),
        ];
    }
}
