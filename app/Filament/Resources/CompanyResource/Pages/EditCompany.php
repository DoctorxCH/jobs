<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use Filament\Resources\Pages\EditRecord;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Owner darf nicht geändert werden (auch wenn Feld vorhanden wäre)
        unset($data['owner_user_id']);

        return $data;
    }
}
