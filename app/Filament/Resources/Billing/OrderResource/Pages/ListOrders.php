<?php

namespace App\Filament\Resources\Billing\OrderResource\Pages;

use App\Filament\Resources\Billing\OrderResource;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;
}
