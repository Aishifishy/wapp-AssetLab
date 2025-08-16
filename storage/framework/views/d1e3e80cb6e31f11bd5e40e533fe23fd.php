

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            Laboratory Schedule Calendar
            <?php if($selectedLaboratory): ?>
                - <?php echo e($selectedLaboratory->name); ?>

            <?php endif; ?>
        </h1>
        <div class="flex items-center space-x-4">
            <span class="text-gray-500">Current Term: <?php echo e($currentTerm->name); ?></span>
            <?php if($selectedLaboratory): ?>
                <a href="<?php echo e(route('admin.comlab.schedule.create', $selectedLaboratory)); ?>" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>
                    Add Schedule
                </a>
            <?php endif; ?>
        </div>
    </div>    <!-- Laboratory Information (when specific laboratory is selected) -->
    <?php if($selectedLaboratory): ?>
        <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <?php if (isset($component)) { $__componentOriginal8860cf004fec956b6e41d036eb967550 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8860cf004fec956b6e41d036eb967550 = $attributes; } ?>
<?php $component = App\View\Components\StatusBadge::resolve(['status' => $selectedLaboratory->status,'type' => 'laboratory'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\StatusBadge::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'px-2.5 py-0.5']); ?>
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
                        <span>Capacity: <?php echo e($selectedLaboratory->capacity); ?> students</span>
                    </div>
                </div>
                <a href="<?php echo e(route('admin.comlab.calendar')); ?>" 
                   class="text-sm text-blue-600 hover:text-blue-800">
                    <i class="fas fa-times mr-1"></i>Back to All Laboratories
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Flash Messages -->
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

    <!-- Laboratory Summary Cards (only show when no specific laboratory is selected) -->
    <?php if(!$selectedLaboratory): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
            <?php $__currentLoopData = $laboratories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $laboratory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-900 truncate"><?php echo e($laboratory->name); ?></h3>
                    <?php if (isset($component)) { $__componentOriginal8860cf004fec956b6e41d036eb967550 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8860cf004fec956b6e41d036eb967550 = $attributes; } ?>
<?php $component = App\View\Components\StatusBadge::resolve(['status' => $laboratory->status,'type' => 'laboratory'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\StatusBadge::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
                <p class="text-xs text-gray-500 mb-3"><?php echo e($laboratory->building); ?> - Room <?php echo e($laboratory->room_number); ?></p>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-600">
                        <?php if(isset($schedules[$laboratory->id])): ?>
                            <?php echo e($schedules[$laboratory->id]->count()); ?> schedule(s)
                        <?php else: ?>
                            No schedules
                        <?php endif; ?>                    </span>
                    <a href="<?php echo e(route('admin.comlab.calendar', ['laboratory_id' => $laboratory->id])); ?>" 
                       class="text-xs text-blue-600 hover:text-blue-800">
                        View Schedule
                    </a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <!-- Schedule View Type Toggle -->
    <div class="mb-4 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <span class="text-sm font-medium text-gray-700">View:</span>
            <div class="flex rounded-md shadow-sm">                <button id="tabular-view" 
                        data-view-type="tabular"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <i class="fas fa-table mr-2"></i>Tabular
                </button>
                <button id="calendar-view" 
                        data-view-type="calendar"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-r-md hover:bg-gray-50 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 border-l-0">
                    <i class="fas fa-calendar mr-2"></i>Calendar
                </button>
            </div>
        </div>
        <div class="flex items-center space-x-2 text-xs">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-blue-100 border border-blue-200 rounded mr-1"></div>
                <span>Regular Class</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-yellow-100 border border-yellow-200 rounded mr-1"></div>
                <span>Special Class</span>
            </div>
        </div>
    </div>

    <!-- Tabular View -->
    <div id="tabular-content" class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                        $allSchedules = collect();
                        foreach($schedules as $labSchedules) {
                            $allSchedules = $allSchedules->merge($labSchedules);
                        }
                        $allSchedules = $allSchedules->sortBy(['laboratory.name', 'day_of_week', 'start_time']);
                        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    ?>
                    
                    <?php $__empty_1 = true; $__currentLoopData = $allSchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($schedule->laboratory->name); ?></div>
                                    <div class="text-sm text-gray-500 ml-2"><?php echo e($schedule->laboratory->building); ?> - <?php echo e($schedule->laboratory->room_number); ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo e($schedule->subject_name); ?></div>
                                <?php if($schedule->subject_code): ?>
                                    <div class="text-sm text-gray-500"><?php echo e($schedule->subject_code); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($schedule->instructor_name); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($schedule->section); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($days[$schedule->day_of_week]); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e(\Carbon\Carbon::parse($schedule->start_time)->format('h:i A')); ?> - 
                                <?php echo e(\Carbon\Carbon::parse($schedule->end_time)->format('h:i A')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php echo e($schedule->type === 'regular' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                    <?php echo e(ucfirst($schedule->type)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="<?php echo e(route('admin.comlab.schedule.edit', [$schedule->laboratory, $schedule])); ?>" 
                                   class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>                                <button type="button" 
                                        class="text-red-600 hover:text-red-800 delete-schedule-btn"
                                        data-form-id="deleteForm<?php echo e($schedule->id); ?>"
                                        data-confirm-message="Are you sure you want to delete this schedule?">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="deleteForm<?php echo e($schedule->id); ?>" 
                                      action="<?php echo e(route('admin.comlab.schedule.destroy', [$schedule->laboratory, $schedule])); ?>" 
                                      method="POST" 
                                      class="hidden">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                </form>
                            </td>
                        </tr>                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                <?php if($selectedLaboratory): ?>
                                    No schedules found for <?php echo e($selectedLaboratory->name); ?> in the current term.
                                <?php else: ?>
                                    No schedules found for the current term.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>    <!-- Calendar View (Hidden by default) -->
    <div id="calendar-content" class="hidden space-y-6">
        <?php
            $laboratoriesToShow = $selectedLaboratory ? collect([$selectedLaboratory]) : $laboratories;
        ?>
        <?php $__currentLoopData = $laboratoriesToShow; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $laboratory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(isset($schedules[$laboratory->id]) || !$selectedLaboratory): ?>
            <div id="lab-<?php echo e($laboratory->id); ?>" class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <i class="fas fa-desktop text-gray-500 mr-2"></i>
                            <span class="text-lg font-medium text-gray-900">
                                <?php echo e($laboratory->name); ?> (<?php echo e($laboratory->building); ?> - <?php echo e($laboratory->room_number); ?>)
                            </span>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php echo e($laboratory->status === 'available' ? 'bg-green-100 text-green-800' : 
                                   ($laboratory->status === 'in_use' ? 'bg-blue-100 text-blue-800' :
                                   ($laboratory->status === 'under_maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))); ?>">
                                <?php echo e(str_replace('_', ' ', ucfirst($laboratory->status))); ?>

                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="w-24 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <?php $__currentLoopData = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e($day); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                    $timeSlots = [];
                                    $startTime = strtotime('07:00');
                                    $endTime = strtotime('21:00');
                                    while($startTime <= $endTime) {
                                        $timeSlots[] = date('H:i', $startTime);
                                        $startTime = strtotime('+30 minutes', $startTime);
                                    }
                                ?>

                                <?php $__currentLoopData = $timeSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($index % 2 == 0): ?>
                                        <tr>
                                            <td class="text-center align-middle border-r px-3 py-2" rowspan="2">
                                                <?php echo e(date('h:i A', strtotime($time))); ?>

                                            </td>
                                            <?php for($day = 0; $day <= 6; $day++): ?>
                                                <td class="relative border-r h-8 px-3 py-2">
                                                    <?php if(isset($schedules[$laboratory->id])): ?>
                                                        <?php $__currentLoopData = $schedules[$laboratory->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if($schedule->day_of_week === $day && strtotime($schedule->start_time) === strtotime($time)): ?>
                                                                <div class="absolute inset-0 <?php echo e($schedule->type === 'regular' ? 'bg-blue-50' : 'bg-yellow-50'); ?> p-2">
                                                                    <div class="flex justify-between items-start">
                                                                        <div class="text-sm">
                                                                            <div class="font-medium text-gray-900"><?php echo e($schedule->subject_name); ?></div>
                                                                            <div class="text-gray-500"><?php echo e($schedule->instructor_name); ?></div>
                                                                            <div class="text-gray-500"><?php echo e($schedule->section); ?></div>
                                                                        </div>
                                                                        <div class="flex space-x-1">
                                                                            <a href="<?php echo e(route('admin.comlab.schedule.edit', [$laboratory, $schedule])); ?>" 
                                                                               class="text-blue-600 hover:text-blue-800">
                                                                                <i class="fas fa-edit"></i>
                                                                            </a>                                                                            <button type="button" 
                                                                                    class="text-red-600 hover:text-red-800 delete-schedule-btn"
                                                                                    data-form-id="deleteForm<?php echo e($schedule->id); ?>Cal"
                                                                                    data-confirm-message="Are you sure you want to delete this schedule?">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                            <form id="deleteForm<?php echo e($schedule->id); ?>Cal" 
                                                                                  action="<?php echo e(route('admin.comlab.schedule.destroy', [$laboratory, $schedule])); ?>" 
                                                                                  method="POST" 
                                                                                  class="hidden">
                                                                                <?php echo csrf_field(); ?>
                                                                                <?php echo method_field('DELETE'); ?>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endfor; ?>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <?php for($day = 0; $day <= 6; $day++): ?>
                                                <td class="relative border-r h-8 px-3 py-2">
                                                    <?php if(isset($schedules[$laboratory->id])): ?>
                                                        <?php $__currentLoopData = $schedules[$laboratory->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if($schedule->day_of_week === $day && 
                                                                strtotime($schedule->start_time) <= strtotime($time) && 
                                                                strtotime($schedule->end_time) > strtotime($time)): ?>
                                                                <div class="absolute inset-0 <?php echo e($schedule->type === 'regular' ? 'bg-blue-50' : 'bg-yellow-50'); ?> opacity-50"></div>
                                                            <?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endfor; ?>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>                    </div>
                </div>
            </div>            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        
        <!-- Show message when selected laboratory has no schedules -->
        <?php if($selectedLaboratory && !isset($schedules[$selectedLaboratory->id])): ?>
            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-calendar-times text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No schedules found</h3>
                <p class="text-gray-500 mb-4">
                    <?php echo e($selectedLaboratory->name); ?> doesn't have any schedules for the current term.
                </p>
                <a href="<?php echo e(route('admin.comlab.schedule.create', $selectedLaboratory)); ?>" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Add Schedule
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<!-- Calendar view management is now handled by calendar-view.js module -->
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\admin\comlab\calendar.blade.php ENDPATH**/ ?>