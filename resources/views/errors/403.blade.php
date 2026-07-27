<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ isRtl() ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - {{ config('app.name') }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; background:#0a0e1a; font-family:'Cairo',sans-serif; }
        .error-page { text-align:center; max-width:500px; padding:2rem; }
        .error-code { font-size:8rem; font-weight:900; background:linear-gradient(135deg,#ffc107,#ff9800); -webkit-background-clip:text; -webkit-text-fill-color:transparent; line-height:1; margin-bottom:0.5rem; }
        .error-icon { font-size:4rem; color:rgba(239,68,68,0.3); margin-bottom:1rem; animation:pulse 2s ease-in-out infinite; }
        .error-title { color:#fff; font-size:1.5rem; font-weight:700; margin-bottom:0.75rem; }
        .error-message { color:rgba(255,255,255,0.5); font-size:1rem; margin-bottom:2rem; line-height:1.6; }
        .error-btn { display:inline-flex; align-items:center; gap:8px; padding:12px 32px; background:linear-gradient(135deg,#ffc107,#ff9800); color:#0a0e1a; font-weight:700; border:none; border-radius:12px; text-decoration:none; font-size:1rem; transition:transform 0.2s,box-shadow 0.2s; }
        .error-btn:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(255,193,7,0.3); color:#0a0e1a; }
        @keyframes pulse { 0%,100%{opacity:0.2;transform:scale(1)} 50%{opacity:0.4;transform:scale(1.05)} }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-icon"><i class="bi bi-shield-lock"></i></div>
        <div class="error-code">403</div>
        <div class="error-title">
            @if(app()->getLocale() === 'ar')
                الوصول ممنوع
            @elseif(app()->getLocale() === 'fr')
                Accès refusé
            @elseif(app()->getLocale() === 'es')
                Acceso denegado
            @else
                Access Denied
            @endif
        </div>
        <div class="error-message">
            @if(app()->getLocale() === 'ar')
                عذراً، ليس لديك صلاحية للوصول إلى هذه الصفحة.<br>تواصل مع المسؤول إذا كنت تعتقد أن هذا خطأ.
            @elseif(app()->getLocale() === 'fr')
                Désolé, vous n'avez pas la permission d'accéder à cette page.<br>Contactez l'administrateur si vous pensez qu'il s'agit d'une erreur.
            @elseif(app()->getLocale() === 'es')
                Lo sentimos, no tienes permiso para acceder a esta página.<br>Contacta al administrador si crees que esto es un error.
            @else
                Sorry, you don't have permission to access this page.<br>Please contact the administrator if you believe this is an error.
            @endif
        </div>
        <a href="{{ route('home') }}" class="error-btn">
            <i class="bi bi-house-fill"></i>
            @if(app()->getLocale() === 'ar')
                العودة للرئيسية
            @elseif(app()->getLocale() === 'fr')
                Retour à l'accueil
            @elseif(app()->getLocale() === 'es')
                Volver al inicio
            @else
                Go Home
            @endif
        </a>
    </div>
</body>
</html>
