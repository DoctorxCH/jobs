<?php

namespace App\Filament\Resources\Job\JobResource\Pages;

use App\Filament\Resources\Job\JobResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJob extends CreateRecord
{
    protected static string $resource = JobResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user) {
            $data['company_id'] = $user->company_id;
            $data['created_by_user_id'] = $user->getKey();
        }

        $data['status'] = $data['status'] ?? 'pending_created';

        return $data;
    }
}
