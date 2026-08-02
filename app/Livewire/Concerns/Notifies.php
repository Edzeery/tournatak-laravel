<?php

namespace App\Livewire\Concerns;

trait Notifies
{
    protected function notify(string $type, string $message): void
    {
        $this->dispatch('toast', type: $type, message: $message);
    }
}
