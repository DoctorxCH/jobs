<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use Filament\Resources\Pages\EditRecord;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Owner darf nicht geändert werden
        unset($data['owner_user_id']);

        return $data;
    }

    protected function canEdit(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        // Platform Admins
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['platform.super_admin', 'platform.admin'])) {
            return true;
        }

        // Company Owner
        return (bool) $user->is_company_owner && $user->company_id === $this->record->id;
    }
}
