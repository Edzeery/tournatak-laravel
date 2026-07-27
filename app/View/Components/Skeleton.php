<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Skeleton extends Component
{
    public int $rows;
    public string $type;
    public bool $table;

    public function __construct(
        int $rows = 5,
        string $type = 'table',
        bool $table = false,
    ) {
        $this->rows = $rows;
        $this->type = $type;
        $this->table = $table || $type === 'table';
    }

    public function render()
    {
        return view('components.skeleton');
    }
}
