<?php

namespace App\Livewire\Admin;

use App\Models\CompetitionDomain;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CompetitionDomainsPage extends Component
{
    public function render()
    {
        return view('livewire.admin.competition-domains-page', [
            'title' => __('app.page_title_manage_domains'),
            'domains' => CompetitionDomain::withCount('competitions')
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
