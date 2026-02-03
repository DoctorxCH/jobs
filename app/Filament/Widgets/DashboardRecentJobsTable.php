<?php

namespace App\Filament\Widgets;

use App\Models\Job;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class DashboardRecentJobsTable extends TableWidget
{
    protected static ?string $heading = 'Letzte Jobs';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Job::query()->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('title')
                ->label('Titel')
                ->searchable()
                ->limit(40),
            TextColumn::make('company.legal_name')
                ->label('Company')
                ->default('-')
                ->toggleable(),
            TextColumn::make('status')
                ->label('Status')
                ->badge(),
            TextColumn::make('created_at')
                ->label('Erstellt')
                ->dateTime('d.m.Y H:i')
                ->sortable(),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [8, 12, 20];
    }
}
