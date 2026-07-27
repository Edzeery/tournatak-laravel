<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale') ?? $request->cookie('locale') ?? config('app.locale', 'ar');

        if (!in_array($locale, ['ar', 'en', 'fr', 'es'])) {
            $locale = config('app.locale', 'ar');
        }

        app()->setLocale($locale);

        return $next($request)->withCookie(
            cookie('locale', $locale, 60 * 24 * 365)
        );
    }
}
