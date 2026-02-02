<?php

namespace App\Filament\Resources\SknacePositionResource\Pages;

use App\Filament\Resources\SknacePositionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSknacePositions extends ListRecords
{
    protected static string $resource = SknacePositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => SknacePositionResource::canCreate()),
        ];
    }
}
