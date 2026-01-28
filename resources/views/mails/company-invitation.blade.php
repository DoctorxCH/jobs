<p>You have been invited to join a company team.</p>

<p>
    Role: <strong>{{ $invite->role }}</strong><br>
    Expires: <strong>{{ $invite->expires_at ? $invite->expires_at->toDateTimeString() : 'never' }}</strong>
</p>

<p>
    Click this link to accept and set your password:
    <br>
    <a href="{{ route('company.invite.accept', ['token' => $invite->token]) }}">
        {{ route('company.invite.accept', ['token' => $invite->token]) }}
    </a>
</p>
