@extends('layouts.admin')

@section('title', 'Manage Borrows')

@section('content')
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
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pending Requests</h3>
            <div class="text-3xl font-bold text-yellow-600">{{ $pendingCount }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Active Borrows</h3>
            <div class="text-3xl font-bold text-blue-600">{{ $activeCount }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Overdue</h3>
            <div class="text-3xl font-bold text-red-600">{{ $overdueCount }}</div>
        </div>
    </div>

    <!-- Borrow Requests Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Borrow Requests</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($requests as $request)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->equipment->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->equipment->category->name ?? 'Uncategorized' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ Str::limit($request->purpose, 50) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $request->requested_from->format('M d, Y') }} -
                                        {{ $request->requested_until->format('M d, Y') }}
                                    </div>
                                    @if($request->status === 'approved' && $request->requested_until < now())
                                        <div class="text-xs text-red-600 font-medium">OVERDUE</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$request->status" type="request" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($request->status === 'pending')
                                        <form action="{{ route('admin.equipment.approve-request', $request) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900 mr-3">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.equipment.reject-request', $request) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                                        </form>
                                    @elseif($request->status === 'approved' && !$request->returned_at)
                                        <button onclick="openReturnModal({{ $request->id }})" class="text-blue-600 hover:text-blue-900">
                                            Return
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No borrow requests found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Onsite Borrow Modal -->
<div id="onsiteBorrowModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Create Onsite Borrow</h3>
            <form action="{{ route('admin.equipment.borrow-requests.onsite') }}" method="POST" class="mt-4" id="onsiteBorrowForm">
                @csrf
                
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
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->department }}</option>
                            @endforeach
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
                            @foreach($availableEquipment as $equipment)
                                <option value="{{ $equipment->id }}">
                                    {{ $equipment->name }} ({{ $equipment->category->name ?? 'Uncategorized' }})
                                </option>
                            @endforeach
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
                               min="{{ now()->format('Y-m-d\TH:i') }}"
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
                @csrf
                
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

@push('scripts')
<!-- Equipment borrowing functionality is now handled by equipment-manager.js module -->
<script>
document.addEventListener('DOMContentLoaded', function() {
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
        fetch('{{ route("admin.users.find-by-rfid") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
        fetch('{{ route("admin.equipment.find-by-rfid") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
@endpush
@endsection