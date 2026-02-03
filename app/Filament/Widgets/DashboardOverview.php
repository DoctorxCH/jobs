<?php

namespace App\Filament\Widgets;

use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $jobsTotal = Job::query()->count();
        $jobsActive = Job::query()->active()->count();
        $jobsExpired = Job::query()->expired()->count();

        $usersTotal = User::query()->count();

        $companiesTotal = Company::query()->count();
        $companiesActive = Company::query()->where('active', true)->count();

        $invoicesTotal = Invoice::query()
            ->where('currency', 'EUR')
            ->count();
        $invoicesUnpaid = Invoice::query()
            ->whereIn('status', ['issued_unpaid', 'overdue'])
            ->where('currency', 'EUR')
            ->count();
        $invoicesPaid = Invoice::query()
            ->where('status', 'paid')
            ->where('currency', 'EUR')
            ->count();

        $paymentsTotal = Payment::query()
            ->where('currency', 'EUR')
            ->count();
        $paidRevenueMinor = Invoice::query()
            ->where('status', 'paid')
            ->where('currency', 'EUR')
            ->sum('total_gross_minor');
        $paidRevenueFormatted = format_money_minor((int) $paidRevenueMinor, 'EUR');

        return [
            Stat::make('Jobs', number_format($jobsTotal))
                ->description(sprintf('Aktiv: %s · Abgelaufen: %s', number_format($jobsActive), number_format($jobsExpired)))
                ->icon('heroicon-o-briefcase')
                ->color('primary'),
            Stat::make('User', number_format($usersTotal))
                ->icon('heroicon-o-users')
                ->color('success'),
            Stat::make('Companies', number_format($companiesTotal))
                ->description(sprintf('Aktiv: %s', number_format($companiesActive)))
                ->icon('heroicon-o-building-office-2')
                ->color('info'),
            Stat::make('Rechnungen', number_format($invoicesTotal))
                ->description(sprintf('Offen: %s · Bezahlt: %s', number_format($invoicesUnpaid), number_format($invoicesPaid)))
                ->icon('heroicon-o-document-text')
                ->color('warning'),
            Stat::make('Payments', number_format($paymentsTotal))
                ->description('Währung: EUR')
                ->icon('heroicon-o-credit-card')
                ->color('success'),
            Stat::make('Billing Umsatz (EUR)', $paidRevenueFormatted)
                ->description('Summe bezahlter Rechnungen')
                ->icon('heroicon-o-banknotes')
                ->color('primary'),
        ];
    }
}
