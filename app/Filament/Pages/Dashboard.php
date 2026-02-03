<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardOverview;
use App\Filament\Widgets\DashboardJobsChart;
use App\Filament\Widgets\DashboardRecentInvoicesTable;
use App\Filament\Widgets\DashboardRecentJobsTable;
use App\Filament\Widgets\DashboardRevenueChart;
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

    public function getWidgets(): array
    {
        return [
            DashboardJobsChart::class,
            DashboardRevenueChart::class,
            DashboardRecentJobsTable::class,
            DashboardRecentInvoicesTable::class,
        ];
    }
}
