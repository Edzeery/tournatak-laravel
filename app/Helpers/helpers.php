<?php

if (! function_exists('isRtl')) {
    function isRtl(): bool
    {
        return in_array(app()->getLocale(), ['ar']);
    }
}

if (! function_exists('isRtlLocale')) {
    function isRtlLocale(string $locale): bool
    {
        return in_array($locale, ['ar']);
    }
}

if (! function_exists('formatDate')) {
    function formatDate(?Carbon\Carbon $date, string $format = 'd/m/Y'): ?string
    {
        return $date?->translatedFormat($format);
    }
}

if (! function_exists('formatDateTime')) {
    function formatDateTime(?Carbon\Carbon $date, string $format = 'd/m/Y H:i'): ?string
    {
        return $date?->translatedFormat($format);
    }
}
