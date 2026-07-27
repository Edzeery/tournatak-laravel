<?php

if (!function_exists('isRtl')) {
    function isRtl(): bool
    {
        return in_array(app()->getLocale(), ['ar']);
    }
}

if (!function_exists('isRtlLocale')) {
    function isRtlLocale(string $locale): bool
    {
        return in_array($locale, ['ar']);
    }
}
