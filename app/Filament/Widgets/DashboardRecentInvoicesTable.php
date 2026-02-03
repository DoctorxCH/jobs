<?php

namespace App\Filament\Widgets;

use App\Models\Billing\Invoice;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class DashboardRecentInvoicesTable extends TableWidget
{
    protected static ?string $heading = 'Letzte Rechnungen';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Invoice::query()->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('payment_reference')
                ->label('Referenz')
                ->searchable()
                ->limit(24),
            TextColumn::make('company.legal_name')
                ->label('Company')
                ->default('-')
                ->toggleable(),
            TextColumn::make('status')
                ->label('Status')
                ->badge(),
            TextColumn::make('total_gross_minor')
                ->label('Betrag')
                ->formatStateUsing(fn (?int $state): string => number_format(((int) $state) / 100, 2))
                ->sortable(),
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
