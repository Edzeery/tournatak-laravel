<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DatePicker extends Component
{
    public string $name;
    public ?string $value;
    public ?string $label;
    public bool $required;
    public bool $enableTime;
    public string $dateFormat;
    public string $altFormat;
    public ?string $placeholder;
    public ?string $error;
    public bool $inline;

    public function __construct(
        string $name = 'date',
        ?string $value = null,
        ?string $label = null,
        bool $required = false,
        bool $enableTime = false,
        string $dateFormat = 'Y-m-d',
        string $altFormat = 'd/m/Y',
        ?string $placeholder = null,
        ?string $error = null,
        bool $inline = false,
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->label = $label;
        $this->required = $required;
        $this->enableTime = $enableTime;
        $this->dateFormat = $dateFormat;
        $this->altFormat = $altFormat;
        $this->placeholder = $placeholder;
        $this->error = $error;
        $this->inline = $inline;
    }

    public function render()
    {
        return view('components.date-picker');
    }
}
