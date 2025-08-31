<?php $__env->startSection('title', 'Manage Equipment'); ?>
<?php $__env->startSection('header', 'Barcode Equipment Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header with action buttons -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 space-y-4 sm:space-y-0">
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
            <a href="<?php echo e(route('admin.equipment.barcode.export')); ?>" 
               class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-print mr-2"></i> Export Barcodes
            </a>
            <a href="<?php echo e(route('admin.equipment.borrow-requests')); ?>" 
               class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <i class="fas fa-clipboard-list mr-2"></i> Manage Borrows
            </a>
            <a href="<?php echo e(route('admin.equipment.create')); ?>" 
               class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i> Add New Equipment
            </a>            <a href="<?php echo e(route('admin.equipment.categories.index')); ?>" 
               class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="fas fa-tags mr-2"></i> Manage Equipment Types
            </a>
        </div>
    </div>

    <!-- Equipment Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Available Equipment</h3>
            <div class="text-3xl font-bold text-green-600">
                <?php echo e($equipment->where('status', 'available')->count()); ?>

            </div>
        </div>
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Borrowed Equipment</h3>
            <div class="text-3xl font-bold text-yellow-600">
                <?php echo e($equipment->where('status', 'borrowed')->count()); ?>

            </div>
        </div>
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Unavailable Equipment</h3>
            <div class="text-3xl font-bold text-red-600">
                <?php echo e($equipment->where('status', 'unavailable')->count()); ?>

            </div>
        </div>
    </div>    <!-- Equipment List -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Equipment List</h2>
        
        <!-- Search and Filters -->
        <div class="mb-6">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="text" 
                           id="search" 
                           name="search"
                           value="<?php echo e(request('search')); ?>"
                           placeholder="Search equipment by ID number or equipment type" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <select id="status-filter" 
                        name="status"
                        class="px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="available" <?php echo e(request('status') === 'available' ? 'selected' : ''); ?>>Available</option>
                    <option value="borrowed" <?php echo e(request('status') === 'borrowed' ? 'selected' : ''); ?>>Borrowed</option>
                    <option value="unavailable" <?php echo e(request('status') === 'unavailable' ? 'selected' : ''); ?>>Unavailable</option>
                </select>
                <button type="button" 
                        id="search-btn"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
                <a href="<?php echo e(route('admin.equipment.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <i class="fas fa-times mr-2"></i> Clear
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Borrower</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $equipment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo e($item->name); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500"><?php echo e($item->category->name ?? 'Uncategorized'); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    <?php echo e($item->barcode ?? ($item->rfid_tag ? $item->rfid_tag . ' (Legacy)' : 'Not Set')); ?>

                                </div>
                            </td>                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if (isset($component)) { $__componentOriginal8860cf004fec956b6e41d036eb967550 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8860cf004fec956b6e41d036eb967550 = $attributes; } ?>
<?php $component = App\View\Components\StatusBadge::resolve(['status' => $item->status,'type' => 'equipment'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    <?php echo e($item->currentBorrower ? $item->currentBorrower->name : 'None'); ?>

                                </div>
                            </td>                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex flex-col sm:flex-row space-y-1 sm:space-y-0 sm:space-x-2">
                                    <button data-action="edit-equipment" data-equipment-id="<?php echo e($item->id); ?>" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                    <?php if($item->barcode): ?>
                                        <a href="<?php echo e(route('admin.equipment.barcode.single', $item->id)); ?>" 
                                           class="text-green-600 hover:text-green-900" 
                                           title="Print Barcode Label">
                                            <i class="fas fa-print mr-1"></i>Label
                                        </a>
                                    <?php endif; ?>
                                    <?php if($item->status !== 'borrowed'): ?>
                                        <form action="<?php echo e(route('admin.equipment.destroy', $item)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-red-600 hover:text-red-900 confirm-action" data-confirm-message="Are you sure you want to delete this equipment?">
                                                <i class="fas fa-trash mr-1"></i>Delete
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No equipment found. <a href="<?php echo e(route('admin.equipment.create')); ?>" class="text-blue-600 hover:text-blue-900">Add some</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Equipment Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Edit Equipment</h3>
            <form id="editForm" method="POST" class="mt-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_name">ID Number</label>
                    <input type="text" name="name" id="edit_name" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_description">Description</label>
                    <textarea name="description" id="edit_description" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_category">Equipment Type</label>
                    <select name="category_id" id="edit_category" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_barcode">Barcode</label>
                    <div class="flex rounded-md shadow-sm">
                        <input type="text" name="barcode" id="edit_barcode"
                            class="flex-1 shadow appearance-none border rounded-l w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <button type="button" onclick="generateBarcode('edit_barcode')"
                            class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r bg-gray-50 text-gray-500 text-sm hover:bg-gray-100">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_rfid">RFID Tag (Legacy - Optional)</label>
                    <input type="text" name="rfid_tag" id="edit_rfid"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="RFID tag (optional)">
                    <p class="text-xs text-amber-600 mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        RFID is legacy. Barcode is recommended.
                    </p>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_status">Status</label>
                    <select name="status" id="edit_status" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                        <option value="borrowed">Borrowed</option>
                    </select>
                </div>                <div class="flex justify-end mt-6">
                    <button type="button" data-action="close-modal" data-target="editModal" 
                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 mr-2">Cancel</button>
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('js/equipment-manager.js')); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const statusFilter = document.getElementById('status-filter');
    const searchBtn = document.getElementById('search-btn');
    
    // Handle search button click
    searchBtn.addEventListener('click', function() {
        performSearch();
    });
    
    // Handle Enter key in search input
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });
    
    // Handle status filter change
    statusFilter.addEventListener('change', function() {
        performSearch();
    });
    
    function performSearch() {
        const searchTerm = searchInput.value.trim();
        const status = statusFilter.value;
        
        // Build URL with search parameters
        const url = new URL(window.location.href.split('?')[0]);
        const params = new URLSearchParams();
        
        if (searchTerm) {
            params.append('search', searchTerm);
        }
        if (status) {
            params.append('status', status);
        }
        
        // Redirect with search parameters
        if (params.toString()) {
            url.search = params.toString();
        }
        
        window.location.href = url.toString();
    }
    
    function generateBarcode(inputId) {
        const barcode = 'EQP' + String(Math.floor(Math.random() * 900000) + 100000);
        document.getElementById(inputId).value = barcode;
    }
    
    // Initialize equipment manager for modals and other functionality
    if (typeof EquipmentManager !== 'undefined') {
        const equipmentManager = new EquipmentManager();
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views/admin/equipment/index.blade.php ENDPATH**/ ?>