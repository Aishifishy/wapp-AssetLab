@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'p-6',
    'shadow' => 'shadow-sm'
])

<div {{ $attributes->merge(['class' => "bg-white rounded-lg {$shadow} overflow-hidden"]) }}>
    @if($title || $subtitle)
        <div class="px-6 py-4 border-b border-gray-200">
            @if($title)
                <h2 class="text-lg font-medium text-gray-900">{{ $title }}</h2>
            @endif
            @if($subtitle)
                <p class="text-sm text-gray-600 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    
    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
