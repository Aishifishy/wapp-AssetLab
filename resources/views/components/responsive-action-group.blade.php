@props([
    'direction' => 'flex-col sm:flex-row',
    'justify' => 'sm:justify-between',
    'spacing' => 'space-y-3 sm:space-y-0 sm:space-x-3'
])

<div {{ $attributes->merge(['class' => "flex {$direction} {$justify} {$spacing}"]) }}>
    {{ $slot }}
</div>
