<p>You have been invited to join a company.</p>

<p>
    Role: <strong>{{ $invite->role }}</strong><br>
    Expires: <strong>{{ $invite->expires_at ? $invite->expires_at->format('d.m.Y H:i') : 'Never' }}</strong>
</p>

<p>
    <a href="{{ $acceptUrl }}">Accept invitation</a>
</p>
