

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Laboratory Management</h1>
        <a href="<?php echo e(route('admin.laboratory.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            <i class="fas fa-arrow-left mr-2"></i> Back to Laboratories
        </a>
    </div>

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

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600" 
                    data-tab="reservations">
                <i class="fas fa-calendar-check mr-2"></i>
                Reservations
            </button>
            <button class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                    data-tab="overrides">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Schedule Overrides
            </button>
        </nav>
    </div>

    <!-- Reservations Tab Content -->
    <div id="reservations-tab" class="tab-content">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending Requests</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($pendingCount); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Approved Today</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($approvedTodayCount); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Rejected Today</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($rejectedTodayCount); ?></p>
                    </div>
                </div>
            </div>
        </div>

    <!-- Pending Requests -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-clock text-yellow-500 mr-2"></i>
                    <h3 class="text-lg font-medium text-gray-900">Laboratory Reservation Requests</h3>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Search Function -->
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search requests..." 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <!-- Status Filter -->
                    <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm w-40">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="requestsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" data-sort="user">
                                <div class="flex items-center">
                                    User
                                    <i class="fas fa-sort ml-2 text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" data-sort="laboratory">
                                <div class="flex items-center">
                                    Laboratory
                                    <i class="fas fa-sort ml-2 text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" data-sort="date">
                                <div class="flex items-center">
                                    Date & Time
                                    <i class="fas fa-sort ml-2 text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" data-sort="status">
                                <div class="flex items-center">
                                    Status
                                    <i class="fas fa-sort ml-2 text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin Actions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $pendingRequests->concat($recentRequests); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="request-row" 
                            data-user="<?php echo e(strtolower($request->user->name)); ?>" 
                            data-laboratory="<?php echo e(strtolower($request->laboratory->name)); ?>" 
                            data-status="<?php echo e($request->status); ?>"
                            data-date="<?php echo e($request->reservation_date ? $request->reservation_date->format('Y-m-d') : ''); ?>"
                            data-search="<?php echo e(strtolower($request->user->name . ' ' . $request->user->email . ' ' . $request->laboratory->name . ' ' . ($request->purpose ?? ''))); ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo e($request->user->name); ?></div>
                                <div class="text-sm text-gray-500"><?php echo e($request->user->email); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo e($request->laboratory->name); ?></div>
                                <div class="text-sm text-gray-500"><?php echo e($request->laboratory->building); ?> - <?php echo e($request->laboratory->room_number); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <?php if($request->purpose): ?>
                                        <?php echo e(Str::limit($request->purpose, 50)); ?>

                                    <?php else: ?>
                                        <span class="text-gray-400">No purpose provided</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?php if($request->reservation_date && $request->formatted_start_time && $request->formatted_end_time): ?>
                                        <?php echo e($request->reservation_date->format('M d, Y')); ?>

                                        <br>
                                        <?php echo e($request->formatted_start_time); ?> - <?php echo e($request->formatted_end_time); ?>

                                    <?php else: ?>
                                        <span class="text-gray-400">Date/Time not available</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-xs text-gray-500"><?php echo e($request->duration); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium
                                        <?php if($request->status === 'approved'): ?> bg-green-100 text-green-800
                                        <?php elseif($request->status === 'rejected'): ?> bg-red-100 text-red-800
                                        <?php else: ?> bg-yellow-100 text-yellow-800 <?php endif; ?>">
                                        <?php echo e(ucfirst($request->status)); ?>

                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($request->status === 'approved' && $request->approvedBy): ?>
                                    <div class="text-xs text-green-600">
                                        <div class="font-medium">Approved by:</div>
                                        <div><?php echo e($request->approvedBy->name); ?></div>
                                        <div class="text-gray-500"><?php echo e($request->approved_at->format('M d, Y g:i A')); ?></div>
                                    </div>
                                <?php elseif($request->status === 'rejected' && $request->rejectedBy): ?>
                                    <div class="text-xs text-red-600">
                                        <div class="font-medium">Rejected by:</div>
                                        <div><?php echo e($request->rejectedBy->name); ?></div>
                                        <div class="text-gray-500"><?php echo e($request->rejected_at->format('M d, Y g:i A')); ?></div>
                                        <?php if($request->rejection_reason): ?>
                                            <div class="text-gray-600 mt-1"><?php echo e(Str::limit($request->rejection_reason, 30)); ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif($request->status === 'pending'): ?>
                                    <div class="text-xs text-gray-400">
                                        <div>Awaiting admin action</div>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <?php if($request->status === 'pending'): ?>
                                    <form action="<?php echo e(route('admin.laboratory.approve-request', $request)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" 
                                                class="text-green-600 hover:text-green-900 mr-3"
                                                onclick="return confirm('Are you sure you want to approve this request?')">
                                            Approve
                                        </button>
                                    </form>
                                    <button type="button" 
                                            class="text-red-600 hover:text-red-900"
                                            data-modal-target="rejectModal<?php echo e($request->id); ?>">
                                        Reject
                                    </button>
                                <?php else: ?>
                                    <span class="text-gray-400">Processed <?php echo e($request->updated_at->diffForHumans()); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <?php if($request->status === 'pending'): ?>
                        <!-- Reject Modal -->
                        <div id="rejectModal<?php echo e($request->id); ?>" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                    <form action="<?php echo e(route('admin.laboratory.reject-request', $request)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                    <i class="fas fa-times text-red-600"></i>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                        Reject Reservation Request
                                                    </h3>
                                                    <div class="mt-2">
                                                        <p class="text-sm text-gray-500">
                                                            Please provide a reason for rejecting this request. This will be sent to the requester.
                                                        </p>
                                                    </div>
                                                    <div class="mt-4">
                                                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Rejection Reason</label>
                                                        <textarea name="rejection_reason" id="rejection_reason" rows="3" 
                                                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" 
                                                                  placeholder="Enter reason for rejection..." required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                Reject Request
                                            </button>
                                            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" data-modal-close>
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No reservation requests found
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const table = document.getElementById('requestsTable');
    const rows = table.querySelectorAll('.request-row');
    
    let sortDirection = {};
    let currentSort = null;

    // Search functionality
    searchInput.addEventListener('input', function() {
        filterTable();
    });

    // Status filter functionality
    statusFilter.addEventListener('change', function() {
        filterTable();
    });

    // Sorting functionality
    table.querySelectorAll('th[data-sort]').forEach(header => {
        header.addEventListener('click', function() {
            const sortKey = this.dataset.sort;
            sortTable(sortKey);
        });
    });

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;

        rows.forEach(row => {
            const searchData = row.dataset.search;
            const statusData = row.dataset.status;
            
            const matchesSearch = searchData.includes(searchTerm);
            const matchesStatus = !statusValue || statusData === statusValue;
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function sortTable(sortKey) {
        // Toggle sort direction
        if (currentSort === sortKey) {
            sortDirection[sortKey] = sortDirection[sortKey] === 'asc' ? 'desc' : 'asc';
        } else {
            sortDirection[sortKey] = 'asc';
            currentSort = sortKey;
        }

        // Update sort icons
        table.querySelectorAll('th[data-sort] i').forEach(icon => {
            icon.className = 'fas fa-sort ml-2 text-gray-400';
        });
        
        const currentHeader = table.querySelector(`th[data-sort="${sortKey}"] i`);
        if (sortDirection[sortKey] === 'asc') {
            currentHeader.className = 'fas fa-sort-up ml-2 text-gray-600';
        } else {
            currentHeader.className = 'fas fa-sort-down ml-2 text-gray-600';
        }

        // Convert NodeList to Array and sort
        const rowsArray = Array.from(rows);
        const tbody = table.querySelector('tbody');
        
        rowsArray.sort((a, b) => {
            let aVal, bVal;
            
            switch(sortKey) {
                case 'user':
                    aVal = a.dataset.user;
                    bVal = b.dataset.user;
                    break;
                case 'laboratory':
                    aVal = a.dataset.laboratory;
                    bVal = b.dataset.laboratory;
                    break;
                case 'status':
                    aVal = a.dataset.status;
                    bVal = b.dataset.status;
                    break;
                case 'date':
                    aVal = a.dataset.date || '0000-00-00';
                    bVal = b.dataset.date || '0000-00-00';
                    break;
                default:
                    return 0;
            }
            
            if (sortDirection[sortKey] === 'asc') {
                return aVal.localeCompare(bVal);
            } else {
                return bVal.localeCompare(aVal);
            }
        });

        // Remove all rows and re-append in sorted order
        rowsArray.forEach(row => {
            tbody.removeChild(row);
        });
        
        rowsArray.forEach(row => {
            tbody.appendChild(row);
        });
    }

    // Modal functionality
    document.addEventListener('click', function(e) {
        if (e.target.hasAttribute('data-modal-target') || e.target.closest('[data-modal-target]')) {
            const trigger = e.target.hasAttribute('data-modal-target') ? e.target : e.target.closest('[data-modal-target]');
            const modalId = trigger.getAttribute('data-modal-target');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
            }
        }
        
        if (e.target.hasAttribute('data-modal-close') || e.target.closest('[data-modal-close]')) {
            const modal = e.target.closest('[role="dialog"]');
            if (modal) {
                modal.classList.add('hidden');
            }
        }
    });

    // Close modal when clicking backdrop
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
            const modal = e.target.closest('[role="dialog"]');
            if (modal) {
                modal.classList.add('hidden');
            }
        }
    });
});
            }
        }
    </div>
</div>

<!-- Schedule Overrides Tab Content -->
<div id="overrides-tab" class="tab-content hidden">
    <!-- Summary Cards for Overrides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100">
                    <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Overrides</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($activeOverridesCount ?? 0); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-calendar-plus text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Recent Overrides</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($recentOverrides->count()); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100">
                        <i class="fas fa-plus text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Quick Actions</p>
                        <p class="text-xs text-gray-400">Create override</p>
                    </div>
                </div>
                <a href="<?php echo e(route('admin.laboratory.create-override')); ?>" 
                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-1"></i>
                    Create Override
                </a>
            </div>
        </div>
    </div>

    <!-- Override Management -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>
                    <h3 class="text-lg font-medium text-gray-900">Schedule Overrides</h3>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="<?php echo e(route('admin.laboratory.schedule-overrides')); ?>" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All Overrides
                    </a>
                    <a href="<?php echo e(route('admin.laboratory.create-override')); ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Create Override
                    </a>
                </div>
            </div>
        </div>
        <div class="p-6">
            <?php if($recentOverrides->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Override Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Original Schedule</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $recentOverrides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $override): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($override->laboratory->name); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo e($override->laboratory->building); ?> - <?php echo e($override->laboratory->room_number); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo e($override->override_date->format('M d, Y')); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo e($override->override_date->format('l')); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        <?php if($override->override_type === 'cancel'): ?> bg-red-100 text-red-800
                                        <?php elseif($override->override_type === 'reschedule'): ?> bg-yellow-100 text-yellow-800
                                        <?php else: ?> bg-blue-100 text-blue-800 <?php endif; ?>">
                                        <?php echo e(ucfirst($override->override_type)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if($override->originalSchedule): ?>
                                        <div class="text-sm text-gray-900"><?php echo e($override->originalSchedule->subject_name); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo e($override->originalSchedule->time_range); ?></div>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400">No original schedule</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><?php echo e(Str::limit($override->reason, 50)); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo e($override->createdBy->name); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo e($override->created_at->diffForHumans()); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($override->isCurrentlyActive()): ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php if($override->isCurrentlyActive()): ?>
                                        <form action="<?php echo e(route('admin.laboratory.deactivate-override', $override)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Are you sure you want to deactivate this override?')">
                                                Deactivate
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-400">No actions</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-exclamation-triangle text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Schedule Overrides</h3>
                    <p class="text-gray-500 mb-6">You haven't created any schedule overrides yet.</p>
                    <a href="<?php echo e(route('admin.laboratory.create-override')); ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Create Your First Override
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Help Section for Overrides -->
    <div class="bg-blue-50 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-xl mt-1"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">About Schedule Overrides</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Schedule overrides allow you to modify regular class schedules for specific dates without affecting the recurring pattern:</p>
                    <ul class="mt-2 list-disc list-inside space-y-1">
                        <li><strong>Cancel:</strong> Remove a class for a specific date</li>
                        <li><strong>Reschedule:</strong> Change the time of a class for a specific date</li>
                        <li><strong>Replace:</strong> Completely replace a class with different details for a specific date</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.getAttribute('data-tab');
            console.log('Clicked tab:', targetTab); // Debug line
            
            // Remove active classes from all tabs
            tabButtons.forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Activate clicked tab
            button.classList.remove('border-transparent', 'text-gray-500');
            button.classList.add('border-blue-500', 'text-blue-600');
            
            // Show target content
            const targetContent = document.getElementById(targetTab + '-tab');
            console.log('Looking for element:', targetTab + '-tab'); // Debug line
            if (targetContent) {
                targetContent.classList.remove('hidden');
                console.log('Successfully showed tab:', targetTab + '-tab'); // Debug line
            } else {
                console.log('Tab content not found:', targetTab + '-tab'); // Debug line
            }
        });
    });
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views/admin/laboratory/reservations.blade.php ENDPATH**/ ?>