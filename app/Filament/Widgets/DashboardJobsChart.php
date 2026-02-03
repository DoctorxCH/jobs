<?php

namespace App\Filament\Widgets;

use App\Models\Job;
use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget;

class DashboardJobsChart extends ChartWidget
{
    protected static ?string $heading = 'Jobs (letzte 14 Tage)';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $dates = collect(range(13, 0))
            ->map(fn (int $offset) => CarbonImmutable::today()->subDays($offset))
            ->values();

        $counts = Job::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', $dates->first()?->startOfDay())
            ->groupBy('date')
            ->pluck('total', 'date');

        $labels = $dates->map(fn (CarbonImmutable $date) => $date->format('d.m'));
        $values = $dates->map(fn (CarbonImmutable $date) => (int) ($counts[$date->toDateString()] ?? 0));

        return [
            'datasets' => [
                [
                    'label' => 'Neue Jobs',
                    'data' => $values,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
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
