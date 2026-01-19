<?php

namespace App\Filament\Resources\CompanyUser\CompanyUserResource\Pages;

use App\Filament\Resources\CompanyUser\CompanyUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCompanyUser extends CreateRecord
{
    protected static string $resource = CompanyUserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user && $user->company_id) {
            $data['company_id'] = $user->company_id;
        }

        return $data;
    }
}
