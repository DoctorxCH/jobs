<?php

namespace App\Filament\Resources\Billing\PaymentResource\Pages;

use App\Filament\Resources\Billing\PaymentResource;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;
}
