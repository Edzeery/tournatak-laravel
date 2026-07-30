<?php

namespace App\View\Components;

use Illuminate\View\Component;

class EmptyState extends Component
{
    public string $icon;

    public ?string $title;

    public ?string $message;

    public ?string $actionLabel;

    public ?string $actionUrl;

    public function __construct(
        string $icon = 'bi-inbox',
        ?string $title = null,
        ?string $message = null,
        ?string $actionLabel = null,
        ?string $actionUrl = null,
    ) {
        $this->icon = $icon;
        $this->title = $title;
        $this->message = $message;
        $this->actionLabel = $actionLabel;
        $this->actionUrl = $actionUrl;
    }

    public function render()
    {
        return view('components.empty-state');
    }
}
