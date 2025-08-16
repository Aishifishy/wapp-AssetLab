<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'direction' => 'flex-col sm:flex-row',
    'justify' => 'sm:justify-between',
    'spacing' => 'space-y-3 sm:space-y-0 sm:space-x-3'
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'direction' => 'flex-col sm:flex-row',
    'justify' => 'sm:justify-between',
    'spacing' => 'space-y-3 sm:space-y-0 sm:space-x-3'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div <?php echo e($attributes->merge(['class' => "flex {$direction} {$justify} {$spacing}"])); ?>>
    <?php echo e($slot); ?>

</div>
<?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\components\responsive-action-group.blade.php ENDPATH**/ ?>