

<?php $__env->startSection('title', 'User Management'); ?>
<?php $__env->startSection('header', 'User Management'); ?>

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

    <!-- Header with action buttons -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Users</h1>
        <a href="<?php echo e(route('admin.users.create')); ?>" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-plus mr-2"></i> Add New User
        </a>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-filter mr-2 text-blue-600"></i>
                Filters & Search
            </h2>
        </div>
        <div class="p-6">
            <form action="<?php echo e(route('admin.users.index')); ?>" method="GET" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-1 text-gray-500"></i>
                        Search Users
                    </label>
                    <input type="text" 
                           name="search" 
                           id="search" 
                           value="<?php echo e(request('search')); ?>" 
                           placeholder="Name, email, department, or RFID..."
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                </div>

                <!-- Role Filter -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-tag mr-1 text-gray-500"></i>
                        Role
                    </label>
                    <select name="role" 
                            id="role" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-white">
                        <option value="all" <?php echo e(request('role') == 'all' ? 'selected' : ''); ?>>All Roles</option>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($role); ?>" <?php echo e(request('role') == $role ? 'selected' : ''); ?>>
                                <?php echo e(ucfirst($role)); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Department Filter -->
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building mr-1 text-gray-500"></i>
                        Department
                    </label>
                    <select name="department" 
                            id="department" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-white">
                        <option value="all" <?php echo e(request('department') == 'all' ? 'selected' : ''); ?>>All Departments</option>
                        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($dept); ?>" <?php echo e(request('department') == $dept ? 'selected' : ''); ?>>
                                <?php echo e($dept); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex flex-col justify-end space-y-2">
                    <button type="button" 
                            onclick="applyFilters()" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i> Apply Filters
                    </button>
                    <button type="button" 
                            onclick="clearFilters()" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i> Clear
                    </button>
                </div>
            </div>

            <!-- Additional Filter Options -->
            <div class="pt-4 border-t border-gray-200">
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="showOnlyRfid" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Show only users with RFID tags</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="showActiveOnly" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Show only active users</span>
                    </label>
                </div>
            </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-users mr-2 text-gray-600"></i>
                    Users (<?php echo e($users->total()); ?> total)
                </h2>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <i class="fas fa-info-circle"></i>
                    <span>Click on any user row for details</span>
                </div>
            </div>
        </div>
        
        <?php if($users->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-user mr-1"></i> User
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-user-tag mr-1"></i> Role
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-building mr-1"></i> Department
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-id-card mr-1"></i> RFID Tag
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-box mr-1"></i> Requests
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-calendar mr-1"></i> Joined
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-cogs mr-1"></i> Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-150 cursor-pointer" onclick="window.location.href='<?php echo e(route('admin.users.show', $user)); ?>'">>>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full <?php echo e($user->role === 'student' ? 'bg-blue-100' : ($user->role === 'faculty' ? 'bg-green-100' : 'bg-purple-100')); ?> flex items-center justify-center">
                                                <i class="fas fa-user <?php echo e($user->role === 'student' ? 'text-blue-600' : ($user->role === 'faculty' ? 'text-green-600' : 'text-purple-600')); ?>"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($user->name); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo e($user->email); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        <?php echo e($user->role === 'student' ? 'bg-blue-100 text-blue-800' : ''); ?>

                                        <?php echo e($user->role === 'faculty' ? 'bg-green-100 text-green-800' : ''); ?>

                                        <?php echo e($user->role === 'staff' ? 'bg-purple-100 text-purple-800' : ''); ?>">
                                        <?php echo e(ucfirst($user->role)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e($user->department ?? 'N/A'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if($user->rfid_tag): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-id-card mr-1"></i>
                                            <?php echo e($user->rfid_tag); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            <i class="fas fa-times mr-1"></i>
                                            Not set
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo e($user->equipment_requests_count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600'); ?>">
                                        <i class="fas fa-box mr-1"></i>
                                        <?php echo e($user->equipment_requests_count); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-calendar mr-1"></i>
                                        <?php echo e($user->created_at->format('M d, Y')); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-3" onclick="event.stopPropagation();">
                                        <a href="<?php echo e(route('admin.users.show', $user)); ?>" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors duration-150" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.users.edit', $user)); ?>" 
                                           class="text-green-600 hover:text-green-900 transition-colors duration-150" 
                                           title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.users.reset-password', $user)); ?>" 
                                           class="text-yellow-600 hover:text-yellow-900 transition-colors duration-150" 
                                           title="Reset Password">
                                            <i class="fas fa-key"></i>
                                        </a>
                                        <button type="button" 
                                                class="text-red-600 hover:text-red-900 transition-colors duration-150" 
                                                title="Delete User"
                                                data-modal-target="deleteModal<?php echo e($user->id); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($users->appends(request()->query())->links()); ?>

            </div>
        <?php else: ?>
            <div class="px-6 py-12 text-center">
                <div class="max-w-sm mx-auto">
                    <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
                    <p class="text-gray-500 mb-6">No users match the current filters. Try adjusting your search criteria or add a new user.</p>
                    <div class="flex justify-center space-x-3">
                        <button type="button" 
                                onclick="clearFilters()" 
                                class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 transition-colors duration-200">
                            <i class="fas fa-filter mr-2"></i>
                            Clear Filters
                        </button>
                        <a href="<?php echo e(route('admin.users.create')); ?>" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Add New User
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modals -->
<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if (isset($component)) { $__componentOriginal8b7b112f0fae85419ee5abf8337434ab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8b7b112f0fae85419ee5abf8337434ab = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-confirmation-modal','data' => ['modalId' => 'deleteModal'.e($user->id).'','title' => 'Delete User','message' => 'Are you sure you want to delete '.e($user->name).'? This action cannot be undone and will remove all associated data.','itemName' => ''.e($user->name).'','deleteRoute' => ''.e(route('admin.users.destroy', $user)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-confirmation-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modal-id' => 'deleteModal'.e($user->id).'','title' => 'Delete User','message' => 'Are you sure you want to delete '.e($user->name).'? This action cannot be undone and will remove all associated data.','item-name' => ''.e($user->name).'','delete-route' => ''.e(route('admin.users.destroy', $user)).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8b7b112f0fae85419ee5abf8337434ab)): ?>
<?php $attributes = $__attributesOriginal8b7b112f0fae85419ee5abf8337434ab; ?>
<?php unset($__attributesOriginal8b7b112f0fae85419ee5abf8337434ab); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8b7b112f0fae85419ee5abf8337434ab)): ?>
<?php $component = $__componentOriginal8b7b112f0fae85419ee5abf8337434ab; ?>
<?php unset($__componentOriginal8b7b112f0fae85419ee5abf8337434ab); ?>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function applyFilters() {
    document.getElementById('filterForm').submit();
}

function clearFilters() {
    // Clear all form inputs
    document.getElementById('search').value = '';
    document.getElementById('role').value = 'all';
    document.getElementById('department').value = 'all';
    document.getElementById('showOnlyRfid').checked = false;
    document.getElementById('showActiveOnly').checked = false;
    
    // Submit the form to apply cleared filters
    document.getElementById('filterForm').submit();
}

// Real-time search functionality
let searchTimeout;
document.getElementById('search').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        if (e.target.value.length === 0 || e.target.value.length >= 3) {
            applyFilters();
        }
    }, 500);
});

// Apply filters when dropdowns change
document.getElementById('role').addEventListener('change', applyFilters);
document.getElementById('department').addEventListener('change', applyFilters);

// Apply filters when checkboxes change
document.getElementById('showOnlyRfid').addEventListener('change', function() {
    if (this.checked) {
        // Add hidden input to form for RFID filter
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'rfid_only';
        hiddenInput.value = '1';
        hiddenInput.id = 'rfidOnlyInput';
        document.getElementById('filterForm').appendChild(hiddenInput);
    } else {
        // Remove hidden input
        const existingInput = document.getElementById('rfidOnlyInput');
        if (existingInput) {
            existingInput.remove();
        }
    }
    applyFilters();
});

document.getElementById('showActiveOnly').addEventListener('change', function() {
    if (this.checked) {
        // Add hidden input to form for active filter
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'active_only';
        hiddenInput.value = '1';
        hiddenInput.id = 'activeOnlyInput';
        document.getElementById('filterForm').appendChild(hiddenInput);
    } else {
        // Remove hidden input
        const existingInput = document.getElementById('activeOnlyInput');
        if (existingInput) {
            existingInput.remove();
        }
    }
    applyFilters();
});

// Set checkbox states based on URL parameters on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('rfid_only') === '1') {
        document.getElementById('showOnlyRfid').checked = true;
    }
    if (urlParams.get('active_only') === '1') {
        document.getElementById('showActiveOnly').checked = true;
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\admin\users\index.blade.php ENDPATH**/ ?>