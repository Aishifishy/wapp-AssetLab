<span {{ $attributes->merge([
    'class' => 'px-2 py-1 inline-flex justify-center items-center text-xs leading-5 font-semibold rounded-full ' . $getBadgeClasses()
]) }}>
    {{ $getDisplayText() }}
</span>
