<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Billing\Invoice;
use App\Models\Billing\Order;
use App\Models\Company;
use App\Models\CompanyInvitation;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Company: owner OR member (falls User-Helper existiert)
        $companyId = null;
        if ($user && method_exists($user, 'effectiveCompanyId')) {
            $companyId = $user->effectiveCompanyId();
        }

        if (!$companyId) {
            $companyId = Company::query()
                ->where('owner_user_id', $user->id)
                ->value('id');
        }

        $company = $companyId
            ? Company::query()->with(['users'])->find($companyId)
            : null;

        // --- Seats ---
        $seatsPurchased = (int) ($company->seats_purchased ?? 0);
        $seatsLocked = (int) ($company->seats_locked ?? 0); // admin-reserviert

        $seatsUsed = 0;
        if ($companyId) {
            $seatsUsed = (int) DB::table('company_user')
                ->where('company_id', $companyId)
                ->whereIn('status', ['pending','active']) // optional: ['active']
                ->count();
        }

        $seatsFree = max(0, $seatsPurchased - $seatsLocked - $seatsUsed);

        // --- Credits (exact schema: credit_ledger.change - active reservations not expired) ---
        $creditsAvailable = null;
        if ($companyId && Schema::hasTable('credit_ledger') && Schema::hasTable('credit_reservations')) {
            $creditsTotal = (int) DB::table('credit_ledger')
                ->where('company_id', $companyId)
                ->sum('change');

            $creditsReserved = (int) DB::table('credit_reservations')
                ->where('company_id', $companyId)
                ->whereIn('status', ['active', 'reserved'])
                ->where('expires_at', '>', now())
                ->sum('amount');

            $creditsAvailable = max(0, $creditsTotal - $creditsReserved);
        }

        // --- Jobs counts ---
        $jobCounts = [
            'total' => 0,
            'active' => 0,
            'draft' => 0,
            'inactive' => 0,
            'expired' => 0,
        ];

        $jobs = collect();
        if ($companyId) {
            $jobs = Job::query()
                ->where('company_id', $companyId)
                ->orderByDesc('updated_at')
                ->get();

            $jobCounts['total'] = $jobs->count();
            $now = now();

            $jobCounts['expired'] = $jobs->filter(fn ($j) => $j->expires_at && $j->expires_at->lt($now))->count();

            $jobCounts['active'] = $jobs->filter(function ($j) use ($now) {
                if (($j->status ?? null) !== 'published') return false;
                if (!$j->expires_at) return true;
                return $j->expires_at->gte($now);
            })->count();

            $jobCounts['draft'] = $jobs->where('status', 'draft')->count();

            // "inactive" = alles was weder active noch draft ist (inkl archived, paused, etc.)
            $jobCounts['inactive'] = $jobs->filter(function ($j) use ($now) {
                $isActive = (($j->status ?? null) === 'published') && (!$j->expires_at || $j->expires_at->gte($now));
                $isDraft = (($j->status ?? null) === 'draft');
                return !$isActive && !$isDraft;
            })->count();
        }

        // --- Invoices counts (exact: open by invoice status, pending by external sync status) ---
        $invoiceCounts = [
            'open' => 0,        // unpaid invoices (status)
            'pending' => 0,     // sync pending (invoice_external.status)
            'overdue' => 0,     // due_at < now and unpaid
        ];

        $invoices = collect();

        if ($companyId && Schema::hasTable('invoices')) {
            $invQ = Invoice::query()->where('company_id', $companyId);

            // Final/closed invoice statuses in our system
            $finalStatuses = ['paid', 'cancelled', 'credit_note'];

            // "open" = not final
            $invoiceCounts['open'] = (clone $invQ)
                ->whereNotIn('status', $finalStatuses)
                ->count();

            // "overdue" = open + due_at passed
            $invoiceCounts['overdue'] = (clone $invQ)
                ->whereNotIn('status', $finalStatuses)
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->count();

            // "pending" = external sync pending (column name differs)
            $syncCol = null;
            foreach (['status', 'sync_status', 'sync_state', 'state', 'sync'] as $col) {
                if (Schema::hasColumn('invoice_external', $col)) {
                    $syncCol = $col;
                    break;
                }
            }

            $invoiceCounts['pending'] = 0;

            if ($syncCol) {
                $invoiceCounts['pending'] = (int) DB::table('invoices as i')
                    ->join('invoice_external as ie', 'ie.invoice_id', '=', 'i.id')
                    ->where('i.company_id', $companyId)
                    ->where("ie.$syncCol", 'pending')
                    ->count();
            }
            $invoices = $invQ->with('external')->orderByDesc('created_at')->limit(50)->get();


            // list: include external, and (optional) sort sync pending first if we know the column
            $invListQ = $invQ->with('external');

            if ($syncCol) {
                $invListQ->orderByRaw("CASE WHEN EXISTS (
                    SELECT 1 FROM invoice_external ie
                    WHERE ie.invoice_id = invoices.id AND ie.$syncCol = 'pending'
                ) THEN 0 ELSE 1 END");
            }

            $invoices = $invQ
                ->with('external')
                ->orderByDesc('created_at')
                ->paginate(10, ['*'], 'invoices_page');            
        }   
// --- Team ---
        $members = $company ? $company->users()->orderBy('name')->get() : collect();
        $invitations = $companyId
            ? CompanyInvitation::query()->where('company_id', $companyId)->latest()->limit(10)->get()
            : collect();

        // --- ToDos ---
        $todos = [];
        if ($company) {
            if (!$company->verified_at) {
                $todos[] = [
                    'label' => 'Company not verified',
                    'hint' => 'Complete your company profile and request verification.',
                    'url' => route('frontend.profile'),
                ];
            }

            if (($invoiceCounts['overdue'] ?? 0) > 0) {
                $todos[] = [
                    'label' => 'Overdue invoices',
                    'hint' => 'Please pay overdue invoices to avoid service restrictions.',
                    'url' => route('frontend.billing.invoices.index'),
                ];
            }

            if (is_int($creditsAvailable) && $creditsAvailable <= 0) {
                $todos[] = [
                    'label' => 'No credits available',
                    'hint' => 'Buy credits to post or renew jobs.',
                    'url' => route('frontend.billing.products.index'),
                ];
            }

            // Jobs expiring soon (next 7 days)
            $expiringSoon = $jobs->filter(function ($j) {
                if (!$j->expires_at) return false;
                return $j->expires_at->isBetween(now(), now()->addDays(7));
            })->count();

            if ($expiringSoon > 0) {
                $todos[] = [
                    'label' => 'Jobs expiring soon',
                    'hint' => $expiringSoon . ' job(s) will expire within 7 days.',
                    'url' => route('frontend.jobs.index'),
                ];
            }
        }

        // --- Activity (best-effort, simple + cheap) ---
        $activity = [];

        if ($companyId) {
            // Jobs
            foreach (Job::query()->where('company_id', $companyId)->orderByDesc('updated_at')->limit(5)->get() as $j) {
                $activity[] = [
                    'at' => $j->updated_at ?? $j->created_at,
                    'type' => 'job',
                    'title' => $j->title ?: 'Untitled job',
                    'hint' => 'Job updated',
                    'url' => route('frontend.jobs.edit', $j),
                ];
            }

            // Invoices
            if (Schema::hasTable('invoices')) {
                foreach (Invoice::query()->where('company_id', $companyId)->orderByDesc('created_at')->limit(5)->get() as $inv) {
                    $label = 'Invoice created';
                    if (Schema::hasColumn('invoices', 'status') && $inv->status) {
                        $label .= ' (' . $inv->status . ')';
                    }

                    $activity[] = [
                        'at' => $inv->created_at,
                        'type' => 'invoice',
                        'title' => 'Invoice',
                        'hint' => $label,
                        'url' => route('frontend.billing.invoices.show', $inv),
                    ];
                }
            }

            // Orders
            if (Schema::hasTable('orders')) {
                foreach (Order::query()->where('company_id', $companyId)->orderByDesc('created_at')->limit(5)->get() as $o) {
                    $activity[] = [
                        'at' => $o->created_at,
                        'type' => 'order',
                        'title' => 'Order',
                        'hint' => 'Order placed',
                        'url' => route('frontend.billing.orders.show', $o),
                    ];
                }
            }

            // Invitations
            foreach (CompanyInvitation::query()->where('company_id', $companyId)->latest()->limit(5)->get() as $inv) {
                $activity[] = [
                    'at' => $inv->created_at,
                    'type' => 'team',
                    'title' => $inv->email ?? 'Invitation',
                    'hint' => 'Team invite sent',
                    'url' => route('frontend.team'),
                ];
            }
        }

        $activityAll = collect($activity)
            ->filter(fn ($a) => !empty($a['at']))
            ->sortByDesc('at')
            ->values();

        $activityPage = max(1, (int) request()->query('activity_page', 1));
        $activityPerPage = 10;

        $activity = new \Illuminate\Pagination\LengthAwarePaginator(
            $activityAll->forPage($activityPage, $activityPerPage)->values(),
            $activityAll->count(),
            $activityPerPage,
            $activityPage,
            [
                'path' => request()->url(),
                'query' => array_merge(request()->query(), ['tab' => 'activity']),
                'pageName' => 'activity_page',
            ]
        );

        return view('dashboard.index', [
            'user' => $user,
            'company' => $company,

            'seatsPurchased' => $seatsPurchased,
            'seatsLocked' => $seatsLocked,
            'seatsFree' => $seatsFree,

            'creditsAvailable' => $creditsAvailable,
            'jobCounts' => $jobCounts,
            'jobs' => $jobs,

            'invoiceCounts' => $invoiceCounts,
            'invoices' => $invoices,

            'members' => $members,
            'invitations' => $invitations,

            'todos' => $todos,
            'activity' => $activity,
        ]);

        

    }
}
