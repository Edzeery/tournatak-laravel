<?php

namespace App\Livewire\Public;

use App\Models\Competition;
use App\Models\CompetitionDomain;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class CompetitionsPage extends Component
{
    #[Url(as: 'domain')]
    public ?string $domain = null;

    public function render()
    {
        $domains = CompetitionDomain::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('livewire.public.competitions-page', [
            'title' => __('app.page_title_competitions'),
            'competitions' => Competition::where('approval_status', 'approved')
                ->with(['type', 'subtype', 'organizer'])
                ->inDomains($this->domain)
                ->latest()
                ->paginate(12),
            'domains' => $domains,
            'activeDomain' => $this->domain ? $domains->firstWhere('slug', $this->domain) : null,
        ]);
    }
}
