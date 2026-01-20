<?php

namespace App\Filament\Resources\CompanyUserResource\Pages;

use App\Filament\Resources\CompanyUserResource;
use App\Models\Company;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateCompanyUser extends CreateRecord
{
    protected static string $resource = CompanyUserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Fallback: set company to user's primary company if missing
        if (empty($data['company_id']) && auth()->user()?->company_id) {
            $data['company_id'] = auth()->user()->company_id;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $company = Company::find($this->record->company_id);

        if (! $company) {
            return;
        }

        // Seats-Limit erzwingen (Owner zählt mit)
        if (! $company->hasFreeSeats()) {
            $this->record->delete();

            throw ValidationException::withMessages([
                'company_id' => 'No free seats available for this company.',
            ]);
        }

        // Optional: user.primary company setzen
        if (request()->boolean('set_primary_company')) {
            $user = $this->record->user;
            if ($user) {
                $user->update(['company_id' => $company->id]);
            }
        }
    }
}
