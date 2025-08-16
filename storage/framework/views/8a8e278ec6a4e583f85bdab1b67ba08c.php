<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => 'Reject Reservation',
    'actionUrl' => '',
    'modalId' => 'rejection-modal',
    'formId' => 'reject-form',
    'reasonFieldId' => 'rejection_reason',
    'reasonLabel' => 'Rejection Reason',
    'submitText' => 'Reject Reservation',
    'cancelText' => 'Cancel'
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
    'title' => 'Reject Reservation',
    'actionUrl' => '',
    'modalId' => 'rejection-modal',
    'formId' => 'reject-form',
    'reasonFieldId' => 'rejection_reason',
    'reasonLabel' => 'Rejection Reason',
    'submitText' => 'Reject Reservation',
    'cancelText' => 'Cancel'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div id="<?php echo e($modalId); ?>" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900"><?php echo e($title); ?></h3>
            <button type="button" onclick="closeRejectModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="<?php echo e($formId); ?>" action="<?php echo e($actionUrl); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <label for="<?php echo e($reasonFieldId); ?>" class="block text-sm font-medium text-gray-700 mb-1"><?php echo e($reasonLabel); ?></label>
                <textarea id="<?php echo e($reasonFieldId); ?>" name="rejection_reason" rows="4" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="button" 
                        data-action="close-reject-modal" 
                        class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 mr-3">
                    <?php echo e($cancelText); ?>

                </button>
                <button type="submit" class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700">
                    <?php echo e($submitText); ?>

                </button>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\components\rejection-modal.blade.php ENDPATH**/ ?>