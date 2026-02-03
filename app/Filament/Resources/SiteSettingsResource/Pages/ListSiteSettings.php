<?php

namespace App\Filament\Resources\SiteSettingsResource\Pages;

use App\Filament\Resources\SiteSettingsResource;
use App\Models\SiteSetting;
use Filament\Resources\Pages\ListRecords;

class ListSiteSettings extends ListRecords
{
    protected static string $resource = SiteSettingsResource::class;

    public function mount(): void
    {
        SiteSetting::ensureRow();

        $this->redirect(SiteSettingsResource::getUrl('edit', ['record' => 1]));
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
