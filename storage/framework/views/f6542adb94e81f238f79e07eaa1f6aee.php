

<?php $__env->startSection('title', 'Borrow Equipment'); ?>
<?php $__env->startSection('header', 'Borrow Equipment'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    <!-- Breadcrumb Navigation -->
    <?php if(isset($selectedCategory)): ?>
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="<?php echo e(route('ruser.equipment.borrow')); ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z" />
                    </svg>
                    Equipment Categories
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2"><?php echo e($selectedCategory->name); ?></span>
                </div>
            </li>
        </ol>
    </nav>
    <?php endif; ?>

    <div class="bg-white shadow-sm rounded-lg">
        <div class="p-6">
            <?php if(isset($selectedCategory)): ?>
                <!-- Category Header -->
                <div class="mb-6 pb-6 border-b">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800"><?php echo e($selectedCategory->name); ?></h2>
                            <?php if($selectedCategory->description): ?>
                                <p class="text-gray-600 mt-1"><?php echo e($selectedCategory->description); ?></p>
                            <?php endif; ?>
                            <p class="text-sm text-gray-500 mt-2">
                                <?php echo e($equipment->total()); ?> <?php echo e(Str::plural('item', $equipment->total())); ?> available for borrowing
                            </p>
                        </div>
                        <a href="<?php echo e(route('ruser.equipment.borrow')); ?>" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Categories
                        </a>
                    </div>
                </div>

                <!-- Search Filter -->
                <div class="mb-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" id="search" placeholder="Search equipment in <?php echo e($selectedCategory->name); ?>..." 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <select id="status-filter" class="w-full sm:w-auto px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Status</option>
                            <option value="available">Available</option>
                            <option value="borrowed">Currently Borrowed</option>
                        </select>
                    </div>
                </div>
            <?php else: ?>
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Equipment Categories</h2>
                <p class="text-gray-600 mb-6">Select a category to browse available equipment for borrowing.</p>
                
                <!-- Category Selection Message -->
                <div class="text-center py-8">
                    <svg class="mx-auto h-16 w-16 text-blue-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14-7H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Browse Equipment by Category</h3>
                    <p class="text-sm text-gray-500 mb-4">Choose from our organized equipment categories to find what you need quickly.</p>
                    <a href="<?php echo e(route('ruser.equipment.borrow')); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                        View Categories
                    </a>
                </div>
            <?php endif; ?>

            <!-- Equipment Grid -->
            <?php if(isset($selectedCategory) && $equipment->count() > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php $__currentLoopData = $equipment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white border rounded-lg overflow-hidden hover:shadow-md transition-shadow" data-equipment-name="<?php echo e(strtolower($item->name)); ?>" data-status="<?php echo e($item->status ?? 'available'); ?>">
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900"><?php echo e($item->name); ?></h3>
                                <p class="text-sm text-gray-600"><?php echo e($selectedCategory->name); ?></p>
                                <?php if($item->model): ?>
                                    <p class="text-xs text-gray-500">Model: <?php echo e($item->model); ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                <?php echo e(($item->status ?? 'available') === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                <?php echo e(ucfirst($item->status ?? 'Available')); ?>

                            </span>
                        </div>
                        
                        <?php if($item->description): ?>
                            <p class="mt-2 text-sm text-gray-600"><?php echo e(Str::limit($item->description, 100)); ?></p>
                        <?php endif; ?>
                        
                        <div class="mt-4 flex items-center justify-between">
                            <div class="text-xs text-gray-500">
                                <?php if($item->brand): ?>
                                    <span><?php echo e($item->brand); ?></span>
                                <?php endif; ?>
                                <?php if($item->serial_number): ?>
                                    <span class="ml-2">SN: <?php echo e(Str::limit($item->serial_number, 10)); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <?php if(($item->status ?? 'available') === 'available'): ?>
                                <button data-equipment-id="<?php echo e($item->id); ?>" 
                                        class="borrow-btn btn-primary w-full py-2 rounded-lg">
                                    Borrow Equipment
                                </button>
                            <?php else: ?>
                                <button disabled 
                                        class="w-full py-2 rounded-lg bg-gray-300 text-gray-500 cursor-not-allowed">
                                    Currently Unavailable
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                <?php echo e($equipment->appends(request()->query())->links()); ?>

            </div>
            <?php elseif(isset($selectedCategory)): ?>
                <!-- No Equipment in Category -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14-7H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No Equipment Found</h3>
                    <p class="mt-2 text-sm text-gray-500">There are currently no equipment items available in the <?php echo e($selectedCategory->name); ?> category.</p>
                    <div class="mt-4">
                        <a href="<?php echo e(route('ruser.equipment.borrow')); ?>" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                            Browse Other Categories
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Borrow Modal -->
<div id="borrowModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Borrow Equipment</h3>
            <form id="borrowForm" action="<?php echo e(route('ruser.equipment.request')); ?>" method="POST" class="mt-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="equipment_id" id="equipment_id">
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="purpose">Purpose</label>
                    <textarea name="purpose" id="purpose" required rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="Please describe why you need this equipment..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="requested_from">From Date</label>
                    <input type="datetime-local" name="requested_from" id="requested_from" required
                           min="<?php echo e(now()->format('Y-m-d\TH:i')); ?>"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="requested_until">Until Date</label>
                    <input type="datetime-local" name="requested_until" id="requested_until" required
                           min="<?php echo e(now()->format('Y-m-d\TH:i')); ?>"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="flex justify-end mt-6">
                    <button type="button" class="btn-secondary mr-2" data-action="close-modal" data-target="borrowModal">Cancel</button>
                    <button type="submit" class="btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('search');
    const statusFilter = document.getElementById('status-filter');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterEquipment);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterEquipment);
    }
    
    function filterEquipment() {
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const statusFilter = document.getElementById('status-filter')?.value || '';
        const equipmentCards = document.querySelectorAll('[data-equipment-name]');
        
        equipmentCards.forEach(card => {
            const name = card.getAttribute('data-equipment-name');
            const status = card.getAttribute('data-status');
            
            const matchesSearch = !searchTerm || name.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter;
            
            if (matchesSearch && matchesStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    // Borrow modal functionality
    const borrowModal = document.getElementById('borrowModal');
    const borrowForm = document.getElementById('borrowForm');
    const equipmentIdInput = document.getElementById('equipment_id');
    
    // Handle borrow button clicks
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('borrow-btn')) {
            const equipmentId = e.target.getAttribute('data-equipment-id');
            equipmentIdInput.value = equipmentId;
            borrowModal.classList.remove('hidden');
        }
        
        // Close modal
        if (e.target.getAttribute('data-action') === 'close-modal' || 
            (e.target === borrowModal)) {
            borrowModal.classList.add('hidden');
        }
    });
    
    // Handle form submission
    if (borrowForm) {
        borrowForm.addEventListener('submit', function(e) {
            const fromDate = new Date(document.getElementById('requested_from').value);
            const untilDate = new Date(document.getElementById('requested_until').value);
            
            if (untilDate <= fromDate) {
                e.preventDefault();
                alert('Return date must be after the borrow date.');
                return false;
            }
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.ruser', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views/ruser/equipment/borrow.blade.php ENDPATH**/ ?>