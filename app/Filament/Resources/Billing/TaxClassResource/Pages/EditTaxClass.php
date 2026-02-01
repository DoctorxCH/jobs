<?php

namespace App\Filament\Resources\Billing\TaxClassResource\Pages;

use App\Filament\Resources\Billing\TaxClassResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaxClass extends EditRecord
{
    protected static string $resource = TaxClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
