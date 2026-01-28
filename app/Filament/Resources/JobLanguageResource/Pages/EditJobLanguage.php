<?php

namespace App\Filament\Resources\JobLanguageResource\Pages;

use App\Filament\Resources\JobLanguageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobLanguage extends EditRecord
{
    protected static string $resource = JobLanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => JobLanguageResource::canDelete(null)),
        ];
    }
}
