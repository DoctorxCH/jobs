<?php

namespace App\Filament\Widgets;

use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

        $invoicesTotal = Invoice::query()->count();
        $invoicesUnpaid = Invoice::query()
            ->whereIn('status', ['issued_unpaid', 'overdue'])
            ->count();
        $invoicesPaid = Invoice::query()->where('status', 'paid')->count();

        $paymentsTotal = Payment::query()->count();
        $revenueByCurrency = $this->getRevenueByCurrency();
        $paidRevenueMinor = Invoice::query()
            ->where('status', 'paid')
            ->sum('total_gross_minor');
        $paidRevenueFormatted = number_format(((int) $paidRevenueMinor) / 100, 2);

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
                ->description('Umsatz: ' . ($revenueByCurrency->isNotEmpty() ? $revenueByCurrency->implode(' · ') : '—'))
                ->icon('heroicon-o-credit-card')
                ->color('success'),
            Stat::make('Billing Umsatz', $paidRevenueFormatted)
                ->description('Summe bezahlter Rechnungen')
                ->icon('heroicon-o-banknotes')
                ->color('primary'),
        ];
    }

    /**
     * @return Collection<int, string>
     */
    private function getRevenueByCurrency(): Collection
    {
        return Payment::query()
            ->select('currency', DB::raw('SUM(amount_minor) as total_minor'))
            ->groupBy('currency')
            ->orderBy('currency')
            ->pluck('total_minor', 'currency')
            ->map(function ($totalMinor, $currency): string {
                $amount = number_format(((int) $totalMinor) / 100, 2);

                return strtoupper((string) $currency) . ' ' . $amount;
            })
            ->values();
    }
}
