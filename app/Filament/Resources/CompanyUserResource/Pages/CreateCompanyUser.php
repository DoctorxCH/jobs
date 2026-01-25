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
        // Fallback: set company to user's effective company if missing
        if (empty($data['company_id']) && auth()->user() && method_exists(auth()->user(), 'effectiveCompanyId')) {
            $data['company_id'] = auth()->user()->effectiveCompanyId();
        }

        if (empty($data['company_id']) && auth()->user()?->company_id) {
            $data['company_id'] = auth()->user()->company_id;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $company = Company::find($record->company_id);

        if (! $company) {
            return;
        }

        // Seats-Limit erzwingen
        if (! $company->hasFreeSeats()) {
            $record->delete();

            throw ValidationException::withMessages([
                'company_id' => 'No free seats available for this company.',
            ]);
        }

        // Company-role -> Spatie role sync (ohne platform.* zu loeschen)
        $user = $record->user;
        if ($user) {
            $role = (string) ($record->role ?? 'member');

            if ($role === 'owner' && ! static::isPlatformAdmin()) {
                // sollte durch UI gar nicht moeglich sein, aber sicher ist sicher
                $record->delete();

                throw ValidationException::withMessages([
                    'role' => 'Only platform admins may assign owner role.',
                ]);
            }

            $companyRoles = ['company.owner', 'company.member', 'company.recruiter', 'company.viewer'];
            foreach ($companyRoles as $r) {
                if ($user->hasRole($r)) {
                    $user->removeRole($r);
                }
            }

            $spatieRole = 'company.' . $role;
            $user->assignRole($spatieRole);

            // Primary company setzen (Form-State, weil Toggle dehydrated(false))
            $state = $this->form->getState();
            $setPrimary = (bool) ($state['set_primary_company'] ?? false);

            if ($setPrimary) {
                $user->update([
                    'company_id' => $company->id,
                    'is_company_owner' => ($role === 'owner'),
                ]);
            } else {
                // Owner-Flag sauber halten
                if ($role !== 'owner') {
                    $user->update(['is_company_owner' => false]);
                }
            }
        }
    }

    private static function isPlatformAdmin(): bool
    {
        $user = auth()->user();

        return $user && method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole(['platform.super_admin', 'platform.admin'])
            : false;
    }
}
