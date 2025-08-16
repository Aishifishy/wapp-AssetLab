

<?php $__env->startSection('title', 'Reservation Details'); ?>
<?php $__env->startSection('header', 'Laboratory Reservation Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if (isset($component)) { $__componentOriginalcca61bfded94b5a7635453a4dc55dd1d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d = $attributes; } ?>
<?php $component = App\View\Components\FlashMessages::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flash-messages'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\FlashMessages::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d)): ?>
<?php $attributes = $__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d; ?>
<?php unset($__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcca61bfded94b5a7635453a4dc55dd1d)): ?>
<?php $component = $__componentOriginalcca61bfded94b5a7635453a4dc55dd1d; ?>
<?php unset($__componentOriginalcca61bfded94b5a7635453a4dc55dd1d); ?>
<?php endif; ?>

    <!-- Reservation Details Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-900">Reservation Information</h2>
            <?php if (isset($component)) { $__componentOriginal8860cf004fec956b6e41d036eb967550 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8860cf004fec956b6e41d036eb967550 = $attributes; } ?>
<?php $component = App\View\Components\StatusBadge::resolve(['status' => $reservation->status,'type' => 'reservation'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\StatusBadge::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'px-3 py-1 text-sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8860cf004fec956b6e41d036eb967550)): ?>
<?php $attributes = $__attributesOriginal8860cf004fec956b6e41d036eb967550; ?>
<?php unset($__attributesOriginal8860cf004fec956b6e41d036eb967550); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8860cf004fec956b6e41d036eb967550)): ?>
<?php $component = $__componentOriginal8860cf004fec956b6e41d036eb967550; ?>
<?php unset($__componentOriginal8860cf004fec956b6e41d036eb967550); ?>
<?php endif; ?>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Laboratory</h3>
                    <p class="mt-1 text-base text-gray-900"><?php echo e($reservation->laboratory->name); ?></p>
                    <p class="text-sm text-gray-600"><?php echo e($reservation->laboratory->building); ?>, Room <?php echo e($reservation->laboratory->room_number); ?></p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Reservation ID</h3>
                    <p class="mt-1 text-base text-gray-900"><?php echo e($reservation->id); ?></p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Date</h3>
                    <p class="mt-1 text-base text-gray-900"><?php echo e($reservation->reservation_date->format('F d, Y')); ?> (<?php echo e($reservation->reservation_date->format('l')); ?>)</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Time</h3>
                    <p class="mt-1 text-base text-gray-900">
                        <?php echo e(\Carbon\Carbon::parse($reservation->start_time)->format('H:i')); ?> - 
                        <?php echo e(\Carbon\Carbon::parse($reservation->end_time)->format('H:i')); ?>

                        <span class="text-sm text-gray-600">(<?php echo e($reservation->duration); ?>)</span>
                    </p>
                </div>

                <?php if($reservation->is_recurring): ?>
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Recurrence Pattern</h3>
                    <p class="mt-1 text-base text-gray-900">
                        <?php echo e(ucfirst($reservation->recurrence_pattern)); ?> until <?php echo e($reservation->recurrence_end_date->format('F d, Y')); ?>

                    </p>
                </div>
                <?php endif; ?>

                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Purpose</h3>
                    <p class="mt-1 text-base text-gray-900"><?php echo e($reservation->purpose); ?></p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Number of Students</h3>
                    <p class="mt-1 text-base text-gray-900"><?php echo e($reservation->num_students); ?></p>
                </div>

                <?php if($reservation->course_code || $reservation->subject || $reservation->section): ?>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Course Information</h3>
                    <p class="mt-1 text-base text-gray-900">
                        <?php if($reservation->course_code): ?>
                            <?php echo e($reservation->course_code); ?>:
                        <?php endif; ?>
                        <?php echo e($reservation->subject ?? 'N/A'); ?>

                        <?php if($reservation->section): ?>
                            (<?php echo e($reservation->section); ?>)
                        <?php endif; ?>
                    </p>
                </div>
                <?php endif; ?>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Submitted On</h3>
                    <p class="mt-1 text-base text-gray-900"><?php echo e($reservation->created_at->format('M d, Y - H:i')); ?></p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Last Updated</h3>
                    <p class="mt-1 text-base text-gray-900"><?php echo e($reservation->updated_at->format('M d, Y - H:i')); ?></p>
                </div>

                <?php if($reservation->status === 'rejected' && $reservation->rejection_reason): ?>
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-sm font-medium text-red-500">Rejection Reason</h3>
                    <p class="mt-1 text-base text-red-600"><?php echo e($reservation->rejection_reason); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <div class="mt-8 flex justify-between">
                <a href="<?php echo e(route('ruser.laboratory.reservations.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Back to Reservations
                </a>
                
                <?php
                    $canCancel = ($reservation->status === 'pending') || 
                                ($reservation->status === 'approved' && 
                                \Carbon\Carbon::parse($reservation->reservation_date . ' ' . $reservation->start_time)
                                ->diffInHours(now()) >= 24);
                ?>
                
                <?php if($canCancel): ?>
                <form action="<?php echo e(route('ruser.laboratory.reservations.cancel', $reservation)); ?>" method="POST">                <?php echo csrf_field(); ?>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 confirm-action"
                            data-confirm-message="Are you sure you want to cancel this reservation?">
                        Cancel Reservation
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.ruser', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\ruser\laboratory\reservation\show.blade.php ENDPATH**/ ?>