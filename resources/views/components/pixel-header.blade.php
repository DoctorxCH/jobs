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
            @guest
                <a class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]" href="{{ route('frontend.login') }}">
                    Login
                </a>
                <a class="pixel-button px-4 py-2 text-xs" href="{{ route('frontend.register') }}">
                    Register
                </a>
            @endguest

            @auth
                <div class="relative">
                    <button
                        class="pixel-outline flex items-center gap-3 px-3 py-2 text-xs uppercase tracking-[0.2em]"
                        data-dropdown-toggle="pixel-user-menu"
                        type="button"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <span class="pixel-outline grid h-8 w-8 place-items-center text-[10px] font-bold">
                            {{ $initials }}
                        </span>

                        <span class="hidden sm:block">
                            Signed in<br>
                            <span class="font-bold normal-case tracking-normal">{{ auth()->user()->name }}</span>
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
                           href="{{ route('frontend.jobs.index') }}">
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
