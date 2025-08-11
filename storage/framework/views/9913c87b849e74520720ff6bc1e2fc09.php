<?php $__env->startSection('title', 'Manage Borrows'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Manage Equipment Borrows</h1>
        <button onclick="openOnsiteBorrowModal()" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-plus mr-2"></i> Create Onsite Borrow
        </button>
    </div>

    <!-- Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Pending Requests</h3>
                    <div class="text-3xl font-bold text-yellow-600"><?php echo e($pendingCount); ?></div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-hand-holding text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Active Borrows</h3>
                    <div class="text-3xl font-bold text-blue-600"><?php echo e($activeCount); ?></div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Overdue</h3>
                    <div class="text-3xl font-bold text-red-600"><?php echo e($overdueCount); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Borrow Requests Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Borrow Requests</h2>
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
                        <option value="returned">Returned</option>
                    </select>
                </div>
            </div>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" data-sort="equipment">
                                <div class="flex items-center">
                                    Equipment
                                    <i class="fas fa-sort ml-2 text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" data-sort="duration">
                                <div class="flex items-center">
                                    Duration
                                    <i class="fas fa-sort ml-2 text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" data-sort="status">
                                <div class="flex items-center">
                                    Status
                                    <i class="fas fa-sort ml-2 text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="request-row" 
                                data-user="<?php echo e(strtolower($request->user->name)); ?>" 
                                data-equipment="<?php echo e(strtolower($request->equipment->name)); ?>" 
                                data-status="<?php echo e($request->status); ?>"
                                data-duration="<?php echo e($request->requested_from->format('Y-m-d')); ?>"
                                data-search="<?php echo e(strtolower($request->user->name . ' ' . $request->user->email . ' ' . $request->equipment->name . ' ' . ($request->equipment->category->name ?? '') . ' ' . ($request->purpose ?? ''))); ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($request->user->name); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo e($request->user->email); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($request->equipment->name); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo e($request->equipment->category->name ?? 'Uncategorized'); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><?php echo e(Str::limit($request->purpose, 50)); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php echo e($request->requested_from->format('M d, Y')); ?> -
                                        <?php echo e($request->requested_until->format('M d, Y')); ?>

                                    </div>
                                    <?php if($request->status === 'approved' && $request->requested_until < now()): ?>
                                        <div class="text-xs text-red-600 font-medium">OVERDUE</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (isset($component)) { $__componentOriginal8860cf004fec956b6e41d036eb967550 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8860cf004fec956b6e41d036eb967550 = $attributes; } ?>
<?php $component = App\View\Components\StatusBadge::resolve(['status' => $request->status,'type' => 'request'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php if($request->status === 'pending'): ?>
                                        <form action="<?php echo e(route('admin.equipment.approve-request', $request)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="text-green-600 hover:text-green-900 mr-3">Approve</button>
                                        </form>
                                        <form action="<?php echo e(route('admin.equipment.reject-request', $request)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                                        </form>
                                    <?php elseif($request->status === 'approved' && !$request->returned_at): ?>
                                        <button onclick="openReturnModal(<?php echo e($request->id); ?>)" class="text-blue-600 hover:text-blue-900">
                                            Return
                                        </button>
                                    <?php else: ?>
                                        <span class="text-gray-400"><?php echo e(ucfirst($request->status)); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No borrow requests found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <?php echo e($requests->links()); ?>

            </div>
        </div>
    </div>
</div>

<!-- Onsite Borrow Modal -->
<div id="onsiteBorrowModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Create Onsite Borrow</h3>
            <form action="<?php echo e(route('admin.equipment.borrow-requests.onsite')); ?>" method="POST" class="mt-4" id="onsiteBorrowForm">
                <?php echo csrf_field(); ?>
                
                <div class="space-y-4">
                    <!-- User Selection with RFID -->
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700">User</label>
                        
                        <!-- RFID Input for User -->
                        <div class="mb-2">
                            <div class="flex">
                                <input type="text" 
                                       id="user_rfid_input" 
                                       placeholder="Scan user RFID tag here..."
                                       class="flex-1 rounded-l-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <button type="button" 
                                        id="scan_user_rfid" 
                                        class="px-3 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Click in the input field and scan the user's RFID tag</p>
                        </div>
                        
                        <!-- Manual User Selection -->
                        <select name="user_id" id="user_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Select User Manually</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?> - <?php echo e($user->department); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        
                        <!-- User Info Display -->
                        <div id="user_info" class="mt-2 p-2 bg-green-50 border border-green-200 rounded hidden">
                            <p class="text-sm text-green-800">
                                <strong>Selected:</strong> <span id="selected_user_name"></span>
                                <br>
                                <strong>Department:</strong> <span id="selected_user_department"></span>
                                <br>
                                <strong>Role:</strong> <span id="selected_user_role"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Equipment Selection with RFID -->
                    <div>
                        <label for="equipment_id" class="block text-sm font-medium text-gray-700">Equipment</label>
                        
                        <!-- RFID Input for Equipment -->
                        <div class="mb-2">
                            <div class="flex">
                                <input type="text" 
                                       id="equipment_rfid_input" 
                                       placeholder="Scan equipment RFID tag here..."
                                       class="flex-1 rounded-l-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <button type="button" 
                                        id="scan_equipment_rfid" 
                                        class="px-3 py-2 bg-green-600 text-white rounded-r-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Click in the input field and scan the equipment's RFID tag</p>
                        </div>
                        
                        <!-- Manual Equipment Selection -->
                        <select name="equipment_id" id="equipment_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Select Equipment Manually</option>
                            <?php $__currentLoopData = $availableEquipment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($equipment->id); ?>">
                                    <?php echo e($equipment->name); ?> (<?php echo e($equipment->category->name ?? 'Uncategorized'); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        
                        <!-- Equipment Info Display -->
                        <div id="equipment_info" class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded hidden">
                            <p class="text-sm text-blue-800">
                                <strong>Selected:</strong> <span id="selected_equipment_name"></span>
                                <br>
                                <strong>Category:</strong> <span id="selected_equipment_category"></span>
                                <br>
                                <strong>Status:</strong> <span id="selected_equipment_status"></span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose</label>
                        <textarea name="purpose" id="purpose" rows="3" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                  placeholder="Purpose of borrowing"></textarea>
                    </div>

                    <div>
                        <label for="requested_until" class="block text-sm font-medium text-gray-700">Return Date</label>
                        <input type="datetime-local" 
                               name="requested_until" 
                               id="requested_until"
                               required
                               min="<?php echo e(now()->format('Y-m-d\TH:i')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" 
                            data-action="close-modal" 
                            data-target="onsiteBorrowModal"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create Borrow
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Return Equipment Modal -->
<div id="returnModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Return Equipment</h3>
            <form id="returnForm" method="POST" class="mt-4">
                <?php echo csrf_field(); ?>
                
                <div class="space-y-4">
                    <div>
                        <label for="condition" class="block text-sm font-medium text-gray-700">Equipment Condition</label>
                        <select name="condition" id="condition" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="good">Good</option>
                            <option value="damaged">Damaged</option>
                            <option value="needs_repair">Needs Repair</option>
                        </select>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                  placeholder="Any notes about the equipment's condition"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" 
                            data-action="close-modal"
                            data-target="returnModal"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Return Equipment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<!-- Equipment borrowing functionality is now handled by equipment-manager.js module -->
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
                case 'equipment':
                    aVal = a.dataset.equipment;
                    bVal = b.dataset.equipment;
                    break;
                case 'status':
                    aVal = a.dataset.status;
                    bVal = b.dataset.status;
                    break;
                case 'duration':
                    aVal = a.dataset.duration || '0000-00-00';
                    bVal = b.dataset.duration || '0000-00-00';
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

    // RFID scanning functionality for users
    const userRfidInput = document.getElementById('user_rfid_input');
    const scanUserRfidBtn = document.getElementById('scan_user_rfid');
    const userSelect = document.getElementById('user_id');
    const userInfo = document.getElementById('user_info');
    
    // RFID scanning functionality for equipment
    const equipmentRfidInput = document.getElementById('equipment_rfid_input');
    const scanEquipmentRfidBtn = document.getElementById('scan_equipment_rfid');
    const equipmentSelect = document.getElementById('equipment_id');
    const equipmentInfo = document.getElementById('equipment_info');
    
    // Focus on user RFID input when scan button is clicked
    scanUserRfidBtn.addEventListener('click', function() {
        userRfidInput.focus();
        userRfidInput.select();
    });
    
    // Focus on equipment RFID input when scan button is clicked
    scanEquipmentRfidBtn.addEventListener('click', function() {
        equipmentRfidInput.focus();
        equipmentRfidInput.select();
    });
    
    // Handle user RFID input
    userRfidInput.addEventListener('input', function(e) {
        const rfidTag = e.target.value.trim();
        if (rfidTag.length > 0) {
            // Clear previous timeout
            if (userRfidInput.timeout) {
                clearTimeout(userRfidInput.timeout);
            }
            
            // Set a timeout to search after user stops typing
            userRfidInput.timeout = setTimeout(() => {
                searchUserByRfid(rfidTag);
            }, 500);
        }
    });
    
    // Handle equipment RFID input
    equipmentRfidInput.addEventListener('input', function(e) {
        const rfidTag = e.target.value.trim();
        if (rfidTag.length > 0) {
            // Clear previous timeout
            if (equipmentRfidInput.timeout) {
                clearTimeout(equipmentRfidInput.timeout);
            }
            
            // Set a timeout to search after user stops typing
            equipmentRfidInput.timeout = setTimeout(() => {
                searchEquipmentByRfid(rfidTag);
            }, 500);
        }
    });
    
    // Search user by RFID
    function searchUserByRfid(rfidTag) {
        fetch('<?php echo e(route("admin.users.find-by-rfid")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({ rfid_tag: rfidTag })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                // Show error message
                userInfo.className = 'mt-2 p-2 bg-red-50 border border-red-200 rounded';
                userInfo.innerHTML = `<p class="text-sm text-red-800">${data.error}</p>`;
                userInfo.classList.remove('hidden');
                userSelect.value = '';
            } else {
                // User found, populate the form
                userSelect.value = data.id;
                document.getElementById('selected_user_name').textContent = data.name;
                document.getElementById('selected_user_department').textContent = data.department;
                document.getElementById('selected_user_role').textContent = data.role;
                
                userInfo.className = 'mt-2 p-2 bg-green-50 border border-green-200 rounded';
                userInfo.classList.remove('hidden');
                
                // Clear the RFID input
                userRfidInput.value = '';
            }
        })
        .catch(error => {
            console.error('Error searching user by RFID:', error);
            userInfo.className = 'mt-2 p-2 bg-red-50 border border-red-200 rounded';
            userInfo.innerHTML = '<p class="text-sm text-red-800">Error searching for user. Please try again.</p>';
            userInfo.classList.remove('hidden');
        });
    }
    
    // Search equipment by RFID
    function searchEquipmentByRfid(rfidTag) {
        fetch('<?php echo e(route("admin.equipment.find-by-rfid")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({ rfid_tag: rfidTag })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                // Show error message
                equipmentInfo.className = 'mt-2 p-2 bg-red-50 border border-red-200 rounded';
                equipmentInfo.innerHTML = `<p class="text-sm text-red-800">${data.error}</p>`;
                equipmentInfo.classList.remove('hidden');
                equipmentSelect.value = '';
            } else {
                // Equipment found, populate the form
                equipmentSelect.value = data.id;
                document.getElementById('selected_equipment_name').textContent = data.name;
                document.getElementById('selected_equipment_category').textContent = data.category;
                document.getElementById('selected_equipment_status').textContent = data.status;
                
                equipmentInfo.className = 'mt-2 p-2 bg-green-50 border border-green-200 rounded';
                equipmentInfo.classList.remove('hidden');
                
                // Clear the RFID input
                equipmentRfidInput.value = '';
            }
        })
        .catch(error => {
            console.error('Error searching equipment by RFID:', error);
            equipmentInfo.className = 'mt-2 p-2 bg-red-50 border border-red-200 rounded';
            equipmentInfo.innerHTML = '<p class="text-sm text-red-800">Error searching for equipment. Please try again.</p>';
            equipmentInfo.classList.remove('hidden');
        });
    }
    
    // Clear info displays when manual selection changes
    userSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.querySelector(`option[value="${this.value}"]`);
            if (selectedOption) {
                const parts = selectedOption.textContent.split(' - ');
                document.getElementById('selected_user_name').textContent = parts[0];
                document.getElementById('selected_user_department').textContent = parts[1] || '';
                document.getElementById('selected_user_role').textContent = 'Manual Selection';
                
                userInfo.className = 'mt-2 p-2 bg-blue-50 border border-blue-200 rounded';
                userInfo.classList.remove('hidden');
            }
        } else {
            userInfo.classList.add('hidden');
        }
    });
    
    equipmentSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.querySelector(`option[value="${this.value}"]`);
            if (selectedOption) {
                const optionText = selectedOption.textContent;
                const equipmentName = optionText.split(' (')[0];
                const categoryMatch = optionText.match(/\(([^)]+)\)/);
                const category = categoryMatch ? categoryMatch[1] : 'Unknown';
                
                document.getElementById('selected_equipment_name').textContent = equipmentName;
                document.getElementById('selected_equipment_category').textContent = category;
                document.getElementById('selected_equipment_status').textContent = 'Available';
                
                equipmentInfo.className = 'mt-2 p-2 bg-blue-50 border border-blue-200 rounded';
                equipmentInfo.classList.remove('hidden');
            }
        } else {
            equipmentInfo.classList.add('hidden');
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views/admin/equipment/borrow-requests.blade.php ENDPATH**/ ?>