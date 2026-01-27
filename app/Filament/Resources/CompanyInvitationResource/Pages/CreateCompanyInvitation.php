<?php

namespace App\Filament\Resources\CompanyInvitationResource\Pages;

use App\Filament\Resources\CompanyInvitationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateCompanyInvitation extends CreateRecord
{
    protected static string $resource = CompanyInvitationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if (! empty($user?->company_id) && empty($data['company_id'])) {
            $data['company_id'] = $user->company_id;
        }

        $data['created_by_user_id'] = $user?->id;
        $data['token'] = $data['token'] ?? Str::random(40);
        $data['expires_at'] = $data['expires_at'] ?? now()->addDays(7);

        return $data;
    }
}
