<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use App\Models\CompanyUser;
use Filament\Resources\Pages\CreateRecord;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['owner_user_id'] = auth()->id();
        $data['status'] = $data['status'] ?? 'pending';

        return $data;
    }

    protected function afterCreate(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        CompanyUser::firstOrCreate(
            [
                'company_id' => $this->record->id,
                'user_id' => $user->id,
            ],
            [
                'role' => 'owner',
                'status' => 'active',
                'accepted_at' => now(),
            ]
        );

        $user->update([
            'company_id' => $this->record->id,
            'is_company_owner' => true,
        ]);
    }
}
