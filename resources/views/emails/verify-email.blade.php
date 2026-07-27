<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.email_verify_title') }}</title>
    <style>
        body { margin:0; padding:0; background:#0a0e1a; font-family:'Cairo',Tahoma,Arial,sans-serif; color:#fff; }
        .container { max-width:560px; margin:40px auto; background:#111827; border-radius:16px; padding:48px 40px; border:1px solid rgba(255,193,7,0.15); }
        .logo { text-align:center; margin-bottom:32px; }
        .logo span { font-size:1.6rem; font-weight:bold; color:#ffc107; }
        h1 { text-align:center; font-size:1.4rem; color:#fff; margin-bottom:8px; }
        p { color:rgba(255,255,255,0.6); text-align:center; font-size:0.95rem; line-height:1.7; }
        .btn { display:block; width:100%; padding:16px; background:#ffc107; color:#0a0e1a; text-decoration:none; text-align:center; border-radius:12px; font-weight:bold; font-size:1.05rem; margin:28px 0; }
        .footer { text-align:center; color:rgba(255,255,255,0.3); font-size:0.8rem; margin-top:32px; padding-top:24px; border-top:1px solid rgba(255,255,255,0.08); }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <span>🏆 {{ config('app.name') }}</span>
        </div>

        <h1>{{ __('app.email_verify_heading') }}</h1>
        <p>
            {{ __('app.email_verify_thanks', ['name' => config('app.name')]) }}
            <br>{{ __('app.email_verify_click_below') }}
        </p>

        <a href="{{ route('verification.verify', ['id' => $userId, 'hash' => $token]) }}" class="btn">
            {{ __('app.email_verify_button') }}
        </a>

        <p style="font-size:0.85rem;">
            {{ __('app.email_not_you') }}
        </p>

        <div class="footer">
            <p>{{ __('app.auto_message') }}</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('app.all_rights_reserved') }}</p>
        </div>
    </div>
</body>
</html>
