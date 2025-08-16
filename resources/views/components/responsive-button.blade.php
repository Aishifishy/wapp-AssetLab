@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'icon' => null,
    'responsive' => true,
    'fullWidth' => false
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';

$variants = [
    'primary' => 'bg-blue-600 hover:bg-blue-700 text-white border border-transparent focus:ring-blue-500',
    'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-700 border border-gray-300 focus:ring-gray-500',
    'success' => 'bg-green-600 hover:bg-green-700 text-white border border-transparent focus:ring-green-500',
    'danger' => 'bg-red-600 hover:bg-red-700 text-white border border-transparent focus:ring-red-500',
    'warning' => 'bg-yellow-600 hover:bg-yellow-700 text-white border border-transparent focus:ring-yellow-500',
    'purple' => 'bg-purple-600 hover:bg-purple-700 text-white border border-transparent focus:ring-purple-500',
    'indigo' => 'bg-indigo-600 hover:bg-indigo-700 text-white border border-transparent focus:ring-indigo-500',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
];

$responsiveClass = $responsive ? ($fullWidth ? 'w-full sm:w-auto' : '') : '';
$classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size] . ' ' . $responsiveClass . ' rounded-md shadow-sm';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i class="{{ $icon }} mr-2"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i class="{{ $icon }} mr-2"></i>
        @endif
        {{ $slot }}
    </button>
@endif
