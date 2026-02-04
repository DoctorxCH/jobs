<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $subject ?? 'Reply' }}</title>
</head>
<body style="margin:0;padding:0;background:#eef2f8;color:#08162f;font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef2f8;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;margin:0 auto;padding:0 16px;">
                    <tr>
                        <td style="padding:16px 0 24px 0;">
                            <div style="font-weight:700;letter-spacing:.2em;text-transform:uppercase;font-size:12px;color:#5b6b85;">365jobs</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#ffffff;border:1px solid rgba(8,22,47,.12);box-shadow:0 8px 20px rgba(8,22,47,0.08);padding:24px;">
                            <div style="font-size:12px;letter-spacing:.28em;text-transform:uppercase;color:#5b6b85;">Reply</div>
                            <h1 style="margin:12px 0 16px 0;font-size:20px;line-height:1.3;color:#08162f;">{{ $subject ?? 'Reply' }}</h1>
                            <div style="font-size:14px;line-height:1.6;color:#334155;">
                                {!! $replyBody !!}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 0 0 0;font-size:12px;color:#5b6b85;">
                            {{ $footer ?? 'This message was sent from 365jobs.' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
