

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">System Reports & Analytics</h1>
                <p class="text-gray-600 mt-1">Comprehensive system statistics and insights</p>
                <div class="mt-2 text-sm text-gray-500">
                    Showing data from <?php echo e($startDate->format('M d, Y')); ?> to <?php echo e($endDate->format('M d, Y')); ?>

                </div>
            </div>
            <div class="flex flex-wrap gap-2 mt-4 sm:mt-0">
                <a href="<?php echo e(route('admin.super-admin.export.users')); ?>" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-download mr-2"></i>Export Users
                </a>
                <a href="<?php echo e(route('admin.super-admin.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-filter mr-2 text-blue-600"></i>Report Filters
            </h2>
        </div>
        <div class="p-6">
            <form method="GET" action="<?php echo e(route('admin.super-admin.reports')); ?>" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700 mb-2">Time Period</label>
                        <select name="period" id="period" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" onchange="toggleCustomDates()">
                            <option value="week" <?php echo e($period == 'week' ? 'selected' : ''); ?>>This Week</option>
                            <option value="month" <?php echo e($period == 'month' ? 'selected' : ''); ?>>This Month</option>
                            <option value="term" <?php echo e($period == 'term' ? 'selected' : ''); ?>>Academic Term</option>
                            <option value="year" <?php echo e($period == 'year' ? 'selected' : ''); ?>>This Year</option>
                            <option value="custom" <?php echo e($period == 'custom' ? 'selected' : ''); ?>>Custom Range</option>
                        </select>
                    </div>
                    
                    <div id="academic-term-select" class="<?php echo e($period != 'term' ? 'hidden' : ''); ?>">
                        <label for="academic_term_id" class="block text-sm font-medium text-gray-700 mb-2">Academic Term</label>
                        <select name="academic_term_id" id="academic_term_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Current Term</option>
                            <?php $__currentLoopData = $academicTerms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($term->id); ?>" <?php echo e($academicTermId == $term->id ? 'selected' : ''); ?>>
                                    <?php echo e($term->academicYear->year_name ?? ''); ?> - <?php echo e($term->term_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div id="date-from" class="<?php echo e($period != 'custom' ? 'hidden' : ''); ?>">
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                        <input type="date" name="date_from" id="date_from" value="<?php echo e($dateFrom); ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <div id="date-to" class="<?php echo e($period != 'custom' ? 'hidden' : ''); ?>">
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                        <input type="date" name="date_to" id="date_to" value="<?php echo e($dateTo); ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                        <i class="fas fa-chart-line mr-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- User Statistics -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-users mr-2 text-blue-600"></i>User Statistics
            </h2>
        </div>
        <div class="p-6">
            <!-- Overall Stats -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 mb-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600"><?php echo e($userStats['total_users']); ?></div>
                    <div class="text-sm text-gray-500 mt-1">Total Users</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600"><?php echo e($userStats['active_users_period']); ?></div>
                    <div class="text-sm text-gray-500 mt-1">Active in Period</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-indigo-600"><?php echo e($userStats['new_users_period']); ?></div>
                    <div class="text-sm text-gray-500 mt-1">New in Period</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600"><?php echo e($userStats['faculty_count']); ?></div>
                    <div class="text-sm text-gray-500 mt-1">Faculty</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600"><?php echo e($userStats['student_count']); ?></div>
                    <div class="text-sm text-gray-500 mt-1">Students</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-600"><?php echo e($userStats['staff_count']); ?></div>
                    <div class="text-sm text-gray-500 mt-1">Staff</div>
                </div>
            </div>
            
            <!-- Period Growth Stats -->
            <div class="border-t pt-6">
                <h3 class="text-sm font-medium text-gray-700 mb-4">New Users in Selected Period</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-yellow-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-yellow-600"><?php echo e($userStats['faculty_new_period']); ?></div>
                        <div class="text-sm text-gray-500 mt-1">New Faculty</div>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600"><?php echo e($userStats['student_new_period']); ?></div>
                        <div class="text-sm text-gray-500 mt-1">New Students</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-gray-600"><?php echo e($userStats['staff_new_period']); ?></div>
                        <div class="text-sm text-gray-500 mt-1">New Staff</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Statistics -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-user-shield mr-2 text-purple-600"></i>Administrator Statistics
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600"><?php echo e($adminStats['total_admins']); ?></div>
                    <div class="text-sm text-gray-500 mt-1">Total Admins</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600"><?php echo e($adminStats['super_admins']); ?></div>
                    <div class="text-sm text-gray-500 mt-1">Super Admins</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-indigo-600"><?php echo e($adminStats['regular_admins']); ?></div>
                    <div class="text-sm text-gray-500 mt-1">Regular Admins</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600"><?php echo e($adminStats['new_admins_period']); ?></div>
                    <div class="text-sm text-gray-500 mt-1">New in Period</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Equipment Statistics -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-tools mr-2 text-orange-600"></i>Equipment Statistics
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600"><?php echo e($equipmentStats['total_equipment']); ?></div>
                        <div class="text-sm text-gray-500 mt-1">Total Equipment</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600"><?php echo e($equipmentStats['available_equipment']); ?></div>
                        <div class="text-sm text-gray-500 mt-1">Available</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600"><?php echo e($equipmentStats['borrowed_equipment']); ?></div>
                        <div class="text-sm text-gray-500 mt-1">Borrowed</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600"><?php echo e($equipmentStats['maintenance_equipment']); ?></div>
                        <div class="text-sm text-gray-500 mt-1">Maintenance</div>
                    </div>
                </div>
                <hr class="my-4 border-gray-200">
                <div class="space-y-4">
                    <h3 class="text-sm font-medium text-gray-700">Period Activity</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                            <span class="text-sm text-gray-600">Total Requests</span>
                            <span class="text-lg font-semibold text-blue-600"><?php echo e($equipmentStats['requests_period']); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <span class="text-sm text-gray-600">Approved</span>
                            <span class="text-lg font-semibold text-green-600"><?php echo e($equipmentStats['approved_requests_period']); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                            <span class="text-sm text-gray-600">Rejected</span>
                            <span class="text-lg font-semibold text-red-600"><?php echo e($equipmentStats['rejected_requests_period']); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                            <span class="text-sm text-gray-600">Pending</span>
                            <span class="text-lg font-semibold text-yellow-600"><?php echo e($equipmentStats['pending_requests']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Laboratory Statistics -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-flask mr-2 text-emerald-600"></i>Laboratory Statistics
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600"><?php echo e($labStats['total_laboratories']); ?></div>
                        <div class="text-sm text-gray-500 mt-1">Total Labs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600"><?php echo e($labStats['available_laboratories']); ?></div>
                        <div class="text-sm text-gray-500 mt-1">Available</div>
                    </div>
                </div>
                <hr class="my-4 border-gray-200">
                <div class="space-y-4">
                    <h3 class="text-sm font-medium text-gray-700">Period Activity</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                            <span class="text-sm text-gray-600">Total Reservations</span>
                            <span class="text-lg font-semibold text-blue-600"><?php echo e($labStats['reservations_period']); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <span class="text-sm text-gray-600">Approved</span>
                            <span class="text-lg font-semibold text-green-600"><?php echo e($labStats['approved_reservations_period']); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Completed</span>
                            <span class="text-lg font-semibold text-gray-600"><?php echo e($labStats['completed_reservations_period']); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                            <span class="text-sm text-gray-600">Pending</span>
                            <span class="text-lg font-semibold text-yellow-600"><?php echo e($labStats['pending_reservations']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-chart-line mr-2 text-indigo-600"></i>6-Month Trends
            </h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Users</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment Requests</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lab Reservations</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $monthlyTrends; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($trend['month']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?php echo e($trend['new_users']); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                    <?php echo e($trend['equipment_requests']); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                    <?php echo e($trend['lab_reservations']); ?>

                                </span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Popular Items -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Most Requested Equipment -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-star mr-2 text-yellow-600"></i>Most Requested Equipment
                </h2>
                <p class="text-sm text-gray-500 mt-1">In selected period</p>
            </div>
            <div class="p-6">
                <?php if($popularEquipment->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $popularEquipment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-gray-900"><?php echo e($equipment->name); ?></h3>
                                <p class="text-xs text-gray-500"><?php echo e($equipment->category->name ?? 'No Category'); ?></p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-semibold text-orange-600"><?php echo e($equipment->borrow_requests_count); ?></div>
                                <div class="text-xs text-gray-500">requests</div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-6">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No equipment requests in this period</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Most Reserved Laboratories -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-trophy mr-2 text-emerald-600"></i>Most Reserved Laboratories
                </h2>
                <p class="text-sm text-gray-500 mt-1">In selected period</p>
            </div>
            <div class="p-6">
                <?php if($popularLabs->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $popularLabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-gray-900"><?php echo e($lab->name); ?></h3>
                                <p class="text-xs text-gray-500">Room <?php echo e($lab->room_number); ?>, <?php echo e($lab->building); ?></p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-semibold text-emerald-600"><?php echo e($lab->reservations_count); ?></div>
                                <div class="text-xs text-gray-500">reservations</div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-6">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No lab reservations in this period</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-bolt mr-2 text-purple-600"></i>Quick Actions
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="<?php echo e(route('admin.super-admin.admins.create')); ?>" class="flex flex-col items-center justify-center p-6 bg-blue-50 hover:bg-blue-100 rounded-lg transition duration-150 ease-in-out text-center">
                    <i class="fas fa-user-plus text-3xl text-blue-600 mb-3"></i>
                    <span class="text-sm font-medium text-blue-900">Add New Admin</span>
                </a>
                <a href="<?php echo e(route('admin.super-admin.users.create')); ?>" class="flex flex-col items-center justify-center p-6 bg-green-50 hover:bg-green-100 rounded-lg transition duration-150 ease-in-out text-center">
                    <i class="fas fa-user-plus text-3xl text-green-600 mb-3"></i>
                    <span class="text-sm font-medium text-green-900">Add New User</span>
                </a>
                <a href="<?php echo e(route('admin.equipment.index')); ?>" class="flex flex-col items-center justify-center p-6 bg-orange-50 hover:bg-orange-100 rounded-lg transition duration-150 ease-in-out text-center">
                    <i class="fas fa-tools text-3xl text-orange-600 mb-3"></i>
                    <span class="text-sm font-medium text-orange-900">Manage Equipment</span>
                </a>
                <a href="<?php echo e(route('admin.laboratory.index')); ?>" class="flex flex-col items-center justify-center p-6 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition duration-150 ease-in-out text-center">
                    <i class="fas fa-flask text-3xl text-emerald-600 mb-3"></i>
                    <span class="text-sm font-medium text-emerald-900">Manage Labs</span>
                </a>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-heartbeat mr-2 text-red-600"></i>System Health
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-3xl text-green-500"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Database Connection</h3>
                        <p class="text-sm text-gray-500">Healthy</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-3xl text-green-500"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Super Admin Access</h3>
                        <p class="text-sm text-gray-500"><?php echo e($adminStats['super_admins']); ?> Active</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <?php if($equipmentStats['pending_requests'] > 10): ?>
                            <i class="fas fa-exclamation-triangle text-3xl text-yellow-500"></i>
                        <?php else: ?>
                            <i class="fas fa-check-circle text-3xl text-green-500"></i>
                        <?php endif; ?>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Pending Requests</h3>
                        <p class="text-sm text-gray-500"><?php echo e($equipmentStats['pending_requests']); ?> Equipment, <?php echo e($labStats['pending_reservations']); ?> Lab</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleCustomDates() {
    const period = document.getElementById('period').value;
    const academicTermSelect = document.getElementById('academic-term-select');
    const dateFrom = document.getElementById('date-from');
    const dateTo = document.getElementById('date-to');
    
    // Hide all first
    academicTermSelect.classList.add('hidden');
    dateFrom.classList.add('hidden');
    dateTo.classList.add('hidden');
    
    // Show relevant fields
    if (period === 'term') {
        academicTermSelect.classList.remove('hidden');
    } else if (period === 'custom') {
        dateFrom.classList.remove('hidden');
        dateTo.classList.remove('hidden');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleCustomDates();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views/admin/super-admin/reports.blade.php ENDPATH**/ ?>