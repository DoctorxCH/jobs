<?php

namespace App\Filament\Resources\ContactSettingResource\Pages;

use App\Filament\Resources\ContactSettingResource;
use App\Models\ContactSetting;
use Filament\Resources\Pages\ListRecords;

class ListContactSettings extends ListRecords
{
    protected static string $resource = ContactSettingResource::class;

    public function mount(): void
    {
        parent::mount();

        $record = ContactSetting::query()->first();
        if (! $record) {
            $record = ContactSetting::query()->create();
        }

        $this->redirect(ContactSettingResource::getUrl('edit', ['record' => $record]));
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
