<!doctype html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <p>You have been invited to join <strong>{{ $company->legal_name }}</strong> as <strong>{{ $invite->role }}</strong>.</p>

    <p>
        Accept invitation:
        <a href="{{ $acceptUrl }}">{{ $acceptUrl }}</a>
    </p>

    @if($invite->expires_at)
        <p>This invitation expires at: {{ $invite->expires_at->toDateTimeString() }}</p>
    @endif
</body>
</html>
