@if(($showCookieBanner ?? false) && isset($cookieSettings))
    <div class="cookie-banner cookie-banner--{{ $cookieSettings->position }} cookie-banner--{{ $cookieSettings->align }} cookie-banner--{{ $cookieSettings->theme }}">
        <div class="cookie-banner__box">
            <div class="cookie-banner__content">
                <div class="cookie-banner__title">{{ $cookieSettings->title }}</div>
                <div class="cookie-banner__msg">{!! nl2br(e($cookieSettings->message)) !!}</div>
            </div>

            <div class="cookie-banner__actions">
                <form method="POST" action="{{ route('cookies.consent') }}">
                    @csrf
                    <input type="hidden" name="level" value="essential">
                    <input type="hidden" name="redirect" value="{{ url()->current() }}">
                    <button type="submit">{{ $cookieSettings->btn_essential }}</button>
                </form>

                <form method="POST" action="{{ route('cookies.consent') }}">
                    @csrf
                    <input type="hidden" name="level" value="stats">
                    <input type="hidden" name="redirect" value="{{ url()->current() }}">
                    <button type="submit">{{ $cookieSettings->btn_stats }}</button>
                </form>
            </div>
        </div>
    </div>
@endif
