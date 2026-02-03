<?php

namespace App\Filament\Widgets;

use App\Models\Billing\Payment;
use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget;

class DashboardRevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Umsatz (letzte 14 Tage)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $dates = collect(range(13, 0))
            ->map(fn (int $offset) => CarbonImmutable::today()->subDays($offset))
            ->values();

        $sums = Payment::query()
            ->selectRaw('DATE(received_at) as date, SUM(amount_minor) as total')
            ->whereNotNull('received_at')
            ->where('received_at', '>=', $dates->first()?->startOfDay())
            ->groupBy('date')
            ->pluck('total', 'date');

        $labels = $dates->map(fn (CarbonImmutable $date) => $date->format('d.m'));
        $values = $dates->map(fn (CarbonImmutable $date) => round(((int) ($sums[$date->toDateString()] ?? 0)) / 100, 2));

        return [
            'datasets' => [
                [
                    'label' => 'Umsatz',
                    'data' => $values,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
