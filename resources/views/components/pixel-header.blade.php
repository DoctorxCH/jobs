@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();

    $brandNumAccent = '365';
    $brandMain = 'jobs';
    $brandAccent = '.sk';

    $initials = null;
    if ($user) {
        $name = trim((string)($user->name ?? $user->email ?? ''));
        $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $first = $parts[0] ?? '';
        $last  = $parts[count($parts) - 1] ?? '';
        $initials = strtoupper(mb_substr($first, 0, 1) . mb_substr($last, 0, 1));
        $initials = trim($initials) ?: 'U';
    }

    $companyName = null;
    if (isset($company)) {
        if (is_array($company)) {
            $companyName = $company['name'] ?? null;
        } else {
            $companyName = $company->name ?? null;
        }
    }
@endphp
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


<header class="px-6 pt-6">
    <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-6">
        {{-- Logo --}}
        <a href="{{ url('/') }}" class="brand365 group" id="brand365">
<!-- Pixel-Kalender (2-layer flip: current klappt bis 90°, next liegt darunter) -->
<span class="pxcal" aria-hidden="true">
  <span class="pxcal__shadow" aria-hidden="true"></span>
  <span class="pxcal__frame" aria-hidden="true"></span>
  <span class="pxcal__rings" aria-hidden="true"></span>

  <!-- NEXT sheet (unten sichtbar) -->
  <span class="pxcal__sheet pxcal__sheet--next" id="pxcalNext" aria-hidden="true">
    <span class="pxcal__header" aria-hidden="true"></span>

    <span class="pxcal__month" id="pxcalNextMonth">MAR</span>

    <span class="pxcal__mini" aria-hidden="true">
      <i></i><i></i><i></i><i></i><i></i><i></i>
      <i></i><i></i><i></i><i></i><i></i><i></i>
    </span>

    <span class="pxcal__fold" aria-hidden="true"></span>
  </span>

  <!-- CURRENT sheet (oben, klappt weg) -->
  <span class="pxcal__sheet pxcal__sheet--current" id="pxcalCurrent" aria-hidden="true">
    <span class="pxcal__header" aria-hidden="true"></span>

    <span class="pxcal__month" id="pxcalCurrentMonth">FEB</span>

    <span class="pxcal__mini" aria-hidden="true">
      <i></i><i></i><i></i><i></i><i></i><i></i>
      <i></i><i></i><i></i><i></i><i></i><i></i>
    </span>

    <span class="pxcal__fold" aria-hidden="true"></span>
  </span>
</span>

  <span class="brand365__text">
    <span class="brand365__name">
      <span class="accent">{{ $brandNumAccent }}</span>{{ $brandMain }}<span class="accent">{{ $brandAccent }}</span>
    </span>
    <span class="brand365__tag">Minimal • Pixel • Clear</span>
  </span>
</a>


        {{-- Right side --}}
        <div class="flex items-center gap-3">
            {{-- Favorites icon (shows if cookies have favorites) --}}
            <a href="{{ route('frontend.favorites') }}" class="relative pixel-outline flex items-center h-12 px-3" id="fav-link" style="display: none; color: currentColor;">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/>
                </svg>
                <span class="absolute -right-2 -top-2 h-4 w-4 rounded-full bg-red-500 text-white text-[10px] flex items-center justify-center" id="fav-count">0</span>
            </a>

            @auth
                @if (isset($creditsAvailable))
                    <a href="{{ route('frontend.billing.products.index') }}" class="pixel-outline flex items-center gap-2 px-3 h-12 text-xs transition-colors hover:text-[var(--accent)]" title="Credits">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M21 9.5C21 11.9853 16.9706 14 12 14M21 9.5C21 7.01472 16.9706 5 12 5C7.02944 5 3 7.01472 3 9.5M21 9.5V15C21 17.2091 16.9706 19 12 19M12 14C7.02944 14 3 11.9853 3 9.5M12 14V19M3 9.5V15C3 17.2091 7.02944 19 12 19M7 18.3264V13.2422M17 18.3264V13.2422" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>                        
                        <span class="font-bold">{{ $creditsAvailable }}</span>
                    </a>
                @endif
            @endauth

            @guest
                <a class="pixel-outline flex items-center h-12 px-4 text-xs uppercase tracking-[0.2em]" href="{{ route('frontend.login') }}">
                    Login
                </a>
                <a class="pixel-button flex items-center h-12 px-4 text-xs" href="{{ route('frontend.register') }}">
                    Register
                </a>
            @endguest

            @auth
                <div class="relative">
                    <button
                        class="pixel-outline flex items-center gap-3 px-3 h-12 text-xs uppercase tracking-[0.2em]"
                        data-dropdown-toggle="pixel-user-menu"
                        type="button"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <span class="pixel-outline grid h-8 w-8 place-items-center text-[10px] font-bold">
                            {{ $initials }}
                        </span>

                        <span class="hidden sm:block">
                            @php
                                $companyLegalName = \App\Models\Company::query()
                                    ->where('owner_user_id', auth()->id())
                                    ->value('legal_name');
                            @endphp
                            @if($companyLegalName)
                                <span class="text-[10px] text-slate-500 block">{{ $companyLegalName }}</span>
                            @endif

                            <span class="flex items-center gap-2 font-bold normal-case tracking-normal">
                                <span>{{ auth()->user()->name }}</span>
                            </span>
                        </span>

                        <span class="text-slate-500">▼</span>
                    </button>

                    <div
                        id="pixel-user-menu"
                        class="pixel-frame absolute right-0 mt-2 hidden w-56 p-2"
                        aria-hidden="true"
                    >
                        <a class="block px-3 py-2 text-xs uppercase tracking-[0.2em] hover:text-slate-900"
                           href="{{ route('frontend.dashboard') }}">
                            Dashboard
                        </a>

                        <a class="block px-3 py-2 text-xs uppercase tracking-[0.2em] hover:text-slate-900"
                           href="{{ route('frontend.jobs.create') }}">
                            Post a Job
                        </a>

                        <div class="my-2 border-t border-slate-200"></div>

                        <form method="POST" action="{{ route('frontend.logout') }}">
                            @csrf
                            <button class="block w-full px-3 py-2 text-left text-xs uppercase tracking-[0.2em] hover:text-slate-900" type="submit">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</header>

<script>
(function () {
    const COOKIE_NAME = 'job_favs_v1';

    function getCookie(name) {
        const m = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()[\]\\\/+^])/g,'\\$1') + '=([^;]*)'));
        return m ? decodeURIComponent(m[1]) : null;
    }

    function readFavs() {
        try {
            const raw = getCookie(COOKIE_NAME);
            if (!raw) return [];
            const arr = JSON.parse(raw);
            return Array.isArray(arr) ? arr.map(Number) : [];
        } catch (e) {
            return [];
        }
    }

    function updateFavIcon() {
        const favs = readFavs();
        const link = document.getElementById('fav-link');
        const count = document.getElementById('fav-count');
        const svg = link.querySelector('svg');
        
        if (favs.length > 0) {
            link.style.display = 'flex';
            link.style.color = '#ef4444';
            svg.style.fill = '#ef4444';
            count.textContent = favs.length;
        } else {
            link.style.display = 'none';
        }
    }

    // Make updateFavIcon available globally for other pages
    window.updateHeaderFavIcon = updateFavIcon;

    document.addEventListener('DOMContentLoaded', updateFavIcon);
    updateFavIcon();
    
    // Listen for storage changes from other tabs/windows
    window.addEventListener('storage', updateFavIcon);
})();
</script>
