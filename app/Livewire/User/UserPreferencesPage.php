<?php

namespace App\Livewire\User;

use App\Models\UserPreference;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class UserPreferencesPage extends Component
{
    public string $locale = 'ar';
    public string $theme = 'system';
    public string $timezone = 'Africa/Algiers';
    public string $date_format = 'd/m/Y';
    public bool $notify_email = true;
    public bool $notify_push = false;
    public bool $sidebar_collapsed = false;
    public string $density = 'comfortable';

    public array $locales = ['ar' => 'العربية', 'en' => 'English', 'fr' => 'Français', 'es' => 'Español'];

    public array $themes = ['light', 'dark', 'system'];

    public array $dateFormats = ['d/m/Y', 'm/d/Y', 'Y-m-d', 'd M Y', 'M d, Y'];

    public array $timezones = [
        'Africa/Algiers',
        'Africa/Cairo',
        'Africa/Tunis',
        'Africa/Casablanca',
        'Europe/Paris',
        'Europe/London',
        'Europe/Madrid',
        'Europe/Berlin',
        'America/New_York',
        'America/Los_Angeles',
        'Asia/Dubai',
        'Asia/Riyadh',
        'Asia/Beirut',
        'Asia/Istanbul',
        'UTC',
    ];

    public array $densities = ['comfortable', 'compact'];

    public function mount(): void
    {
        $user = auth()->user();
        $pref = $user->preference;

        if ($pref) {
            $this->locale = $pref->locale ?? 'ar';
            $this->theme = $pref->theme ?? 'system';
            $this->timezone = $pref->timezone ?? 'Africa/Algiers';
            $this->date_format = $pref->date_format ?? 'd/m/Y';
            $this->notify_email = $pref->notify_email ?? true;
            $this->notify_push = $pref->notify_push ?? false;
            $this->sidebar_collapsed = $pref->sidebar_collapsed ?? false;
            $this->density = $pref->density ?? 'comfortable';
        }
    }

    public function save(): void
    {
        $user = auth()->user();

        $validated = $this->validate([
            'locale' => 'required|in:ar,en,fr,es',
            'theme' => 'required|in:light,dark,system',
            'timezone' => 'required|string',
            'date_format' => ['required', Rule::in(['d/m/Y', 'm/d/Y', 'Y-m-d', 'd M Y', 'M d, Y'])],
            'notify_email' => 'boolean',
            'notify_push' => 'boolean',
            'sidebar_collapsed' => 'boolean',
            'density' => 'required|in:comfortable,compact',
        ]);

        $user->preference()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        $this->dispatch('swal:success', message: __('app.preferences_saved'));
    }

    public function render()
    {
        return view('livewire.user.user-preferences-page', [
            'title' => __('app.page_title_preferences'),
        ]);
    }
}
