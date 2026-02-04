<?php

namespace App\Filament\Resources\LegalPageResource\Pages;

use App\Filament\Resources\LegalPageResource;
use App\Models\LegalPage;
use Filament\Resources\Pages\ListRecords;

class ListLegalPages extends ListRecords
{
    protected static string $resource = LegalPageResource::class;

    public function mount(): void
    {
        $record = LegalPage::first();

        if ($record) {
            $this->redirect(LegalPageResource::getUrl('edit', ['record' => $record->id]));
        } else {
            $this->redirect(LegalPageResource::getUrl('create'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
