@props(['variant' => 'secondary'])

<span {{ $attributes->class(['badge', 'bg-' . $variant]) }}>
    {{ $slot }}
</span>
