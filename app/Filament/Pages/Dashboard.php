<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardOverview::class,
        ];
    }
}
