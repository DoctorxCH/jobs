<?php

namespace App\Filament\Resources\CookieSettingResource\Pages;

use App\Filament\Resources\CookieSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCookieSetting extends EditRecord
{
    protected static string $resource = CookieSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('forceReconsent')
                ->label('Re-Consent erzwingen (naechster Besuch)')
                ->requiresConfirmation()
                ->action(function () {
                    $record = $this->getRecord();
                    $record->update(['consent_version' => $record->consent_version + 1]);
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
