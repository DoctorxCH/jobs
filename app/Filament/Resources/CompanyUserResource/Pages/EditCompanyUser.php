<?php

namespace App\Filament\Resources\CompanyUserResource\Pages;

use App\Filament\Resources\CompanyUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyUser extends EditRecord
{
    protected static string $resource = CompanyUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => CompanyUserResource::canDelete($this->record)),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $user = $record->user;

        if (! $user) {
            return;
        }

        $role = (string) ($record->role ?? 'member');

        // Company-role -> Spatie role sync (ohne platform.* zu loeschen)
        $companyRoles = ['company.owner', 'company.member', 'company.recruiter', 'company.viewer'];
        foreach ($companyRoles as $r) {
            if ($user->hasRole($r)) {
                $user->removeRole($r);
            }
        }

        $user->assignRole('company.' . $role);

        // Primary company setzen (Form-State, weil Toggle dehydrated(false))
        $state = $this->form->getState();
        $setPrimary = (bool) ($state['set_primary_company'] ?? false);

        if ($setPrimary) {
            $user->update([
                'company_id' => $record->company_id,
                'is_company_owner' => ($role === 'owner'),
            ]);
        } else {
            if ($role !== 'owner') {
                $user->update(['is_company_owner' => false]);
            }
        }
    }
}
