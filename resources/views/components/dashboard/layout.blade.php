@props([
    'title' => __('main.dashboard'),
])

@php
    // Credits badge (simple v1):
    // - resolves company by owner_user_id (works for owner accounts)
    // - available = sum(credit_ledger.change) - sum(active reservations not expired)
    $creditsAvailable = null;

    if (auth()->check()) {
        $companyId = \App\Models\Company::query()
            ->where('owner_user_id', auth()->id())
            ->value('id');

        if ($companyId) {
            $creditsTotal = (int) \Illuminate\Support\Facades\DB::table('credit_ledger')
                ->where('company_id', $companyId)
                ->sum('change');

            $creditsReserved = (int) \Illuminate\Support\Facades\DB::table('credit_reservations')
                ->where('company_id', $companyId)
                ->whereIn('status', ['active', 'reserved'])
                ->where('expires_at', '>', now())
                ->sum('amount');

            $creditsAvailable = max(0, $creditsTotal - $creditsReserved);
        }
    }
@endphp

<x-layouts.pixel :title="$title">
    <section class="mx-auto w-full max-w-7xl">
        

        <div class="grid gap-8 md:grid-cols-[220px_1fr]">
            <aside class="pixel-panel p-4">
                <x-dashboard.menu />
            </aside>

            <main class="pixel-panel p-8">
                {{-- Header row --}}
                <div class="pixel-page__header">
                    
                   
                </div>                {{-- Alerts --}}
                @if (session('status') || session('success'))
                    <div class="mb-6 pixel-outline px-4 py-3 text-sm border-2 border-green-600 bg-green-50 text-green-800">
                        {{ session('status') ?? session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 pixel-outline px-4 py-3 text-sm border-2 border-red-600 bg-red-50 text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 pixel-outline px-4 py-3 text-sm border-2 border-red-600 bg-red-50 text-red-800">
                        <div class="font-bold uppercase tracking-[0.2em] text-xs mb-2">{{ __('main.fix_errors') }}</div>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </section>
</x-layouts.pixel>
