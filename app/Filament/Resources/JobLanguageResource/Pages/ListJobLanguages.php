<?php

namespace App\Filament\Resources\JobLanguageResource\Pages;

use App\Filament\Resources\JobLanguageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobLanguages extends ListRecords
{
    protected static string $resource = JobLanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => JobLanguageResource::canCreate()),
        ];
    }
}
