<?php

namespace App\Filament\Resources\SiteSettingsResource\Pages;

use App\Filament\Resources\SiteSettingsResource;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Filament\Resources\Pages\EditRecord;

class EditSiteSettings extends EditRecord
{
    protected static string $resource = SiteSettingsResource::class;

    public function mount(int | string $record): void
    {
        SiteSetting::ensureRow();

        parent::mount($record);
    }

    protected function afterSave(): void
    {
        Cache::forget('site_settings.current');
        Cache::forget('site_settings.default_locale');
    }
}
