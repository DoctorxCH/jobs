<?php

namespace App\Filament\Resources\CompanyCategoryResource\Pages;

use App\Filament\Resources\CompanyCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyCategory extends EditRecord
{
    protected static string $resource = CompanyCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
