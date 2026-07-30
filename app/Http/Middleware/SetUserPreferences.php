<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class SetUserPreferences
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $preference = auth()->user()->preference;

            if ($preference) {
                // Set locale from user preference
                app()->setLocale($preference->locale);

                // Set Carbon locale for date formatting
                Carbon::setLocale($preference->locale);

                // Share preference data with all views
                view()->share('userPreference', $preference);
            }
        }

        // Inject data-theme attribute for dark/light mode
        $theme = $this->resolveTheme();
        view()->share('resolvedTheme', $theme);

        return $next($request);
    }

    protected function resolveTheme(): string
    {
        if (auth()->check()) {
            $preference = auth()->user()->preference;

            if ($preference && $preference->theme !== 'system') {
                return $preference->theme;
            }
        }

        // Default to light for now; could read from cookie/session
        return 'light';
    }
}
