<?php

namespace App\Filament\Resources\Billing\ProductPriceResource\Pages;

use App\Filament\Resources\Billing\ProductPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductPrices extends ListRecords
{
    protected static string $resource = ProductPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
