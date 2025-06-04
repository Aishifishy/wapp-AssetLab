<span {{ $attributes->merge([
    'class' => 'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ' . $getBadgeClasses()
]) }}>
    {{ $getDisplayText() }}
</span>
