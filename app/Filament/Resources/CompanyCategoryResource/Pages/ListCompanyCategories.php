<?php

namespace App\Filament\Resources\CompanyCategoryResource\Pages;

use App\Filament\Imports\CompanyCategoryImporter;
use App\Filament\Resources\CompanyCategoryResource;
use Filament\Actions;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyCategories extends ListRecords
{
    protected static string $resource = CompanyCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make('import')
                ->label('Import (Excel)')
                ->importer(CompanyCategoryImporter::class),
        ];
    }
}
