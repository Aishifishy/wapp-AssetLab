@props([
    'title',
    'subtitle' => null
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 space-y-3 sm:space-y-0']) }}>
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-sm text-gray-600 mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    
    @if(isset($actions))
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
            {{ $actions }}
        </div>
    @endif
</div>
