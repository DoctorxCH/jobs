<?php

namespace App\Filament\Resources\Billing\CouponResource\Pages;

use App\Filament\Resources\Billing\CouponResource;
use Filament\Resources\Pages\ListRecords;

class ListCoupons extends ListRecords
{
    protected static string $resource = CouponResource::class;
}
