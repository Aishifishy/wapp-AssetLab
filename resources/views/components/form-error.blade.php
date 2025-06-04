@if($hasError())
    <p class="mt-2 text-sm text-red-600 flex items-center" {{ $attributes }}>
        <svg class="h-4 w-4 mr-1 text-red-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        @if($slot->isNotEmpty())
            {{ $slot }}
        @else
            {{ $getErrorMessage() }}
        @endif
    </p>
@endif
