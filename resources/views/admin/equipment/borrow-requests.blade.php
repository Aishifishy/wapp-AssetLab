@extends('layouts.admin')

@section('title', 'Manage Borrows')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 space-y-3 sm:space-y-0">
        <h1 class="text-2xl font-semibold text-gray-800">Manage Equipment Borrows</h1>
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Notification Feature (Placeholder) -->
            <button onclick="alert('Email notification feature coming soon!')" 
                    class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 opacity-75 cursor-not-allowed"
                    disabled>
                <i class="fas fa-envelope mr-2"></i> Notify Borrowers
            </button>
            <button onclick="openOnsiteBorrowModal()" 
                    class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-hand-holding mr-2"></i> Onsite Borrow/Checkout
            </button>
        </div>
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
                    <div class="text-3xl font-bold text-yellow-600">{{ $pendingCount }}</div>
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
                    <div class="text-3xl font-bold text-blue-600">{{ $activeCount }}</div>
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
                    <div class="text-3xl font-bold text-red-600">{{ $overdueCount }}</div>
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
                    <!-- Per Page Selector -->
                    <div class="flex items-center space-x-2">
                        <label for="perPageSelect" class="text-sm text-gray-600">Show:</label>
                        <select id="perPageSelect" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 w-20">
                            <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page') == 10 || !request('per_page') ? 'selected' : '' }}>10</option>
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-sm text-gray-600">per page</span>
                    </div>
                    <!-- Search Function -->
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search requests..." 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 w-48" data-sort="duration">
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin Actions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($requests as $request)
                            <tr class="request-row" 
                                data-user="{{ strtolower($request->user->name) }}" 
                                data-equipment="{{ strtolower($request->equipment->name) }}" 
                                data-status="{{ $request->status }}"
                                data-duration="{{ $request->requested_from->format('Y-m-d') }}"
                                data-search="{{ strtolower($request->user->name . ' ' . $request->user->email . ' ' . $request->equipment->name . ' ' . ($request->equipment->category->name ?? '') . ' ' . ($request->purpose ?? '')) }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->equipment->category->name ?? 'Uncategorized' }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->equipment->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ Str::limit($request->purpose, 50) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <div class="mb-.5">{{ $request->requested_from->format('M d, Y') }} -</div>
                                        <div class="text-xs text-gray-600">{{ $request->requested_from->format('g:i A') }}</div>
                                    </div>
                                    <div class="text-sm text-gray-900 mt-2">
                                        <div class="mb-.5">{{ $request->requested_until->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-600">{{ $request->requested_until->format('g:i A') }}</div>
                                    </div>
                                    @if(($request->status === 'approved' || $request->status === 'checked_out') && !$request->returned_at && $request->requested_until < now())
                                        <div class="text-xs text-red-600 font-medium mt-1">OVERDUE</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col items-center">
                                        @php
                                            $displayStatus = $request->status;
                                            if ($request->returned_at) {
                                                $displayStatus = 'returned';
                                            } elseif ($request->isCheckedOut() && !$request->returned_at) {
                                                $displayStatus = 'checked_out';
                                            }
                                        @endphp
                                        <x-status-badge :status="$displayStatus" type="request" />
                                        
                                        @if($request->isCheckedOut())
                                            <div class="text-xs text-gray-500 mt-1">
                                                <div>Checked out: {{ $request->checked_out_at->format('M d, Y g:i A') }}</div>
                                            </div>
                                        @endif
                                        
                                        @if($request->returned_at)
                                            <div class="text-xs text-green-600 mt-1">
                                                <div>Returned: {{ $request->returned_at->format('M d, Y g:i A') }}</div>
                                                @if($request->return_condition)
                                                    <div class="text-xs {{ $request->return_condition === 'good' ? 'text-green-600' : ($request->return_condition === 'damaged' ? 'text-red-600' : 'text-yellow-600') }}">
                                                        Condition: {{ ucfirst($request->return_condition) }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($request->status === 'approved' && $request->approvedBy)
                                        <div class="text-xs text-green-600">
                                            <div class="font-medium">Approved by:</div>
                                            <div>{{ $request->approvedBy->name }}</div>
                                            <div class="text-gray-500">{{ $request->approved_at->format('M d, Y g:i A') }}</div>
                                        </div>
                                    @elseif($request->status === 'rejected' && $request->rejectedBy)
                                        <div class="text-xs text-red-600">
                                            <div class="font-medium">Rejected by:</div>
                                            <div>{{ $request->rejectedBy->name }}</div>
                                            <div class="text-gray-500">{{ $request->rejected_at->format('M d, Y g:i A') }}</div>
                                        </div>
                                    @elseif($request->status === 'pending')
                                        <div class="text-xs text-gray-400">
                                            <div>Awaiting admin action</div>
                                        </div>
                                    @endif
                                    
                                    @if($request->isCheckedOut() && $request->checkedOutBy)
                                        <div class="text-xs text-blue-600 mt-2">
                                            <div class="font-medium">Checked out by:</div>
                                            <div>{{ $request->checkedOutBy->name }}</div>
                                            <div>{{ $request->checked_out_at->format('M d, Y g:i A') }}</div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($request->status === 'pending')
                                        <button onclick="previewConflicts({{ $request->id }})" class="text-yellow-600 hover:text-yellow-900 mr-3 text-xs">
                                            <i class="fas fa-eye mr-1"></i>Preview
                                        </button>
                                        <form action="{{ route('admin.equipment.approve-request', $request) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900 mr-3">Approve</button>
                                        </form>
                                        <button onclick="openRejectModal({{ $request->id }})" class="text-red-600 hover:text-red-900">Reject</button>
                                    @elseif($request->status === 'approved' && !$request->isCheckedOut() && !$request->returned_at)
                                        <form action="{{ route('admin.equipment.checkout-request', $request) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:text-blue-900 mr-3">Check Out</button>
                                        </form>
                                    @elseif($request->isCheckedOut() && !$request->returned_at)
                                        <button onclick="openReturnModal({{ $request->id }})" class="text-purple-600 hover:text-purple-900">
                                            Return
                                        </button>
                                    @else
                                        <span class="text-gray-400">{{ ucfirst($request->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
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
            <h3 class="text-lg font-medium leading-6 text-gray-900">Onsite Borrow & Checkout</h3>
            <p class="text-sm text-gray-600 mt-1">Scan user and equipment details. This will either check out an approved request or create a new onsite borrow.</p>
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
                                class="mt-1 block w-full px-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
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
                        
                        <!-- Action Preview -->
                        <div id="action_preview" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded hidden">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                <div>
                                    <p class="text-sm font-medium text-blue-800" id="action_text">Scan equipment to see what action will be performed</p>
                                    <p class="text-xs text-blue-600" id="action_detail"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Equipment Selection with RFID -->
                    <div>
                        <label for="equipment_id" class="block text-sm font-medium text-gray-700">Equipment</label>
                        
                        <!-- Barcode Input for Equipment -->
                        <div class="mb-2">
                            <div class="flex">
                                <input type="text" 
                                       id="equipment_barcode_input" 
                                       placeholder="Scan equipment barcode here..."
                                       class="flex-1 rounded-l-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <button type="button" 
                                        id="scan_equipment_barcode" 
                                        class="px-3 py-2 bg-green-600 text-white rounded-r-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Click in the input field and scan the equipment's barcode</p>
                        </div>
                        
                        <!-- Manual Equipment Selection -->
                        <select name="equipment_id" id="equipment_id" required
                                class="mt-1 block w-full px-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
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
                        Process Borrow/Checkout
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
                                class="mt-1 block w-full px-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
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
    const searchInput = document.getElementById('searchInput');
    const perPageSelect = document.getElementById('perPageSelect');
    const table = document.getElementById('requestsTable');
    const rows = table.querySelectorAll('.request-row');
    
    let sortDirection = {};
    let currentSort = null;

    // Search functionality
    searchInput.addEventListener('input', function() {
        filterTable();
    });

    // Per page functionality
    perPageSelect.addEventListener('change', function() {
        const url = new URL(window.location);
        url.searchParams.set('per_page', this.value);
        url.searchParams.delete('page'); // Reset to first page when changing per_page
        window.location.href = url.toString();
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

        rows.forEach(row => {
            const searchData = row.dataset.search;
            
            const matchesSearch = searchData.includes(searchTerm);
            
            if (matchesSearch) {
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
    
    // Barcode scanning functionality for equipment
    const equipmentBarcodeInput = document.getElementById('equipment_barcode_input');
    const scanEquipmentBarcodeBtn = document.getElementById('scan_equipment_barcode');
    const equipmentSelect = document.getElementById('equipment_id');
    const equipmentInfo = document.getElementById('equipment_info');
    
    // Focus on user RFID input when scan button is clicked
    scanUserRfidBtn.addEventListener('click', function() {
        userRfidInput.focus();
        userRfidInput.select();
    });
    
    // Focus on equipment barcode input when scan button is clicked
    scanEquipmentBarcodeBtn.addEventListener('click', function() {
        equipmentBarcodeInput.focus();
        equipmentBarcodeInput.select();
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
    
    // Handle equipment barcode input
    equipmentBarcodeInput.addEventListener('input', function(e) {
        const barcode = e.target.value.trim();
        if (barcode.length > 0) {
            // Clear previous timeout
            if (equipmentBarcodeInput.timeout) {
                clearTimeout(equipmentBarcodeInput.timeout);
            }
            
            // Set a timeout to search after user stops typing
            equipmentBarcodeInput.timeout = setTimeout(() => {
                searchEquipmentByBarcode(barcode);
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
                
                // Check for action preview
                checkActionPreview();
            }
        })
        .catch(error => {
            console.error('Error searching user by RFID:', error);
            userInfo.className = 'mt-2 p-2 bg-red-50 border border-red-200 rounded';
            userInfo.innerHTML = '<p class="text-sm text-red-800">Error searching for user. Please try again.</p>';
            userInfo.classList.remove('hidden');
            hideActionPreview();
        });
    }
    
    // Search equipment by barcode
    function searchEquipmentByBarcode(barcode) {
        fetch('{{ route("admin.equipment.find-by-code") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: barcode, type: 'barcode' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                // Show error message
                equipmentInfo.className = 'mt-2 p-2 bg-red-50 border border-red-200 rounded';
                equipmentInfo.innerHTML = `<p class="text-sm text-red-800">${data.error}</p>`;
                equipmentInfo.classList.remove('hidden');
                equipmentSelect.value = '';
                hideActionPreview();
            } else {
                // Equipment found, populate the form
                equipmentSelect.value = data.id;
                document.getElementById('selected_equipment_name').textContent = data.name;
                document.getElementById('selected_equipment_category').textContent = data.category;
                document.getElementById('selected_equipment_status').textContent = data.status;
                
                equipmentInfo.className = 'mt-2 p-2 bg-green-50 border border-green-200 rounded';
                equipmentInfo.classList.remove('hidden');
                
                // Clear the barcode input
                equipmentBarcodeInput.value = '';
                
                // Check for action preview
                checkActionPreview();
            }
        })
        .catch(error => {
            console.error('Error searching equipment by barcode:', error);
            equipmentInfo.className = 'mt-2 p-2 bg-red-50 border border-red-200 rounded';
            equipmentInfo.innerHTML = '<p class="text-sm text-red-800">Error searching for equipment. Please try again.</p>';
            equipmentInfo.classList.remove('hidden');
            hideActionPreview();
        });
    }
    
    // Check what action will be performed and show preview
    function checkActionPreview() {
        const userId = userSelect.value;
        const equipmentId = equipmentSelect.value;
        
        if (!userId || !equipmentId) {
            hideActionPreview();
            return;
        }
        
        // Check if there's an existing approved request
        fetch('{{ route("admin.equipment.check-approved-request") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                user_id: userId, 
                equipment_id: equipmentId 
            })
        })
        .then(response => response.json())
        .then(data => {
            const actionPreview = document.getElementById('action_preview');
            const actionText = document.getElementById('action_text');
            const actionDetail = document.getElementById('action_detail');
            
            if (data.hasApprovedRequest) {
                actionText.textContent = '✓ Will CHECK OUT approved equipment request';
                actionDetail.textContent = `Request submitted: ${data.requestDate} | Purpose: ${data.purpose}`;
                actionPreview.className = 'mt-2 p-3 bg-green-50 border border-green-200 rounded';
            } else {
                actionText.textContent = '+ Will CREATE NEW onsite borrow and check out';
                actionDetail.textContent = 'No existing approved request found. New borrow will be created.';
                actionPreview.className = 'mt-2 p-3 bg-blue-50 border border-blue-200 rounded';
            }
            
            actionPreview.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error checking approved request:', error);
            hideActionPreview();
        });
    }
    
    // Hide action preview
    function hideActionPreview() {
        document.getElementById('action_preview').classList.add('hidden');
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
                
                // Check for action preview
                checkActionPreview();
            }
        } else {
            userInfo.classList.add('hidden');
            hideActionPreview();
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
                
                // Check for action preview
                checkActionPreview();
            }
        } else {
            equipmentInfo.classList.add('hidden');
            hideActionPreview();
        }
    });

    // Conflict Preview Functionality
    async function previewConflicts(requestId) {
        try {
            const response = await fetch(`/admin/equipment/requests/${requestId}/preview-conflicts`);
            const data = await response.json();
            
            if (data.has_conflicts) {
                showConflictModal(data);
            } else {
                // No conflicts, show success message and offer direct approval
                if (confirm('No conflicts found. Would you like to approve this request now?')) {
                    document.querySelector(`form[action*="/requests/${requestId}/approve"] button`).click();
                }
            }
        } catch (error) {
            console.error('Error previewing conflicts:', error);
            alert('Error checking for conflicts. Please try again.');
        }
    }

    function showConflictModal(conflictData) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.id = 'conflictModal';
        
        const conflictList = conflictData.conflicting_requests.map(conflict => `
            <div class="border-l-4 border-red-400 bg-red-50 p-4 mb-3">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            <strong>Request #${conflict.id}</strong> by ${conflict.user_name}
                        </p>
                        <p class="text-sm text-red-600">
                            Time: ${new Date(conflict.requested_from).toLocaleString()} - ${new Date(conflict.requested_until).toLocaleString()}
                        </p>
                        <p class="text-sm text-red-600">Purpose: ${conflict.purpose}</p>
                    </div>
                </div>
            </div>
        `).join('');

        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center mb-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Time Slot Conflicts Detected</h3>
                            <p class="text-sm text-gray-500">
                                Found ${conflictData.conflict_count} conflicting ${conflictData.conflict_count === 1 ? 'request' : 'requests'} for the same equipment.
                            </p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-900 mb-2">Conflicting Requests:</h4>
                        ${conflictList}
                    </div>
                    
                    ${conflictData.auto_rejection_enabled ? `
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Auto-rejection Enabled</h3>
                                    <div class="mt-1 text-sm text-yellow-700">
                                        If you approve this request, the ${conflictData.conflict_count} conflicting ${conflictData.conflict_count === 1 ? 'request' : 'requests'} will be automatically rejected.
                                    </div>
                                </div>
                            </div>
                        </div>
                    ` : ''}
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <button onclick="closeConflictModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button onclick="proceedWithApproval()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            ${conflictData.auto_rejection_enabled ? 'Approve & Auto-reject Conflicts' : 'Approve Anyway'}
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        window.currentRequestId = requestId; // Store for approval action
    }

    function closeConflictModal() {
        const modal = document.getElementById('conflictModal');
        if (modal) {
            modal.remove();
        }
        window.currentRequestId = null;
    }

    function proceedWithApproval() {
        if (window.currentRequestId) {
            // Find and submit the approval form
            const form = document.querySelector(`form[action*="/requests/${window.currentRequestId}/approve"]`);
            if (form) {
                closeConflictModal();
                form.submit();
            }
        }
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('conflictModal');
        if (modal && event.target === modal) {
            closeConflictModal();
        }
    });

    // Reject Modal Functions
    window.openRejectModal = function(requestId) {
        document.getElementById('rejectForm').action = `/admin/equipment/requests/${requestId}/reject`;
        document.getElementById('rejectModal').classList.remove('hidden');
    };

    window.closeRejectModal = function() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejection_reason').value = '';
    };

    // Return Modal Functions  
    window.openReturnModal = function(requestId) {
        document.getElementById('returnForm').action = `/admin/equipment/requests/${requestId}/return`;
        document.getElementById('returnModal').classList.remove('hidden');
    };

    window.openOnsiteBorrowModal = function() {
        document.getElementById('onsiteBorrowModal').classList.remove('hidden');
    };

    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
        const conflictModal = document.getElementById('conflictModal');
        const rejectModal = document.getElementById('rejectModal');
        const returnModal = document.getElementById('returnModal');
        const onsiteBorrowModal = document.getElementById('onsiteBorrowModal');
        
        if (conflictModal && event.target === conflictModal) {
            closeConflictModal();
        }
        if (rejectModal && event.target === rejectModal) {
            closeRejectModal();
        }
        if (returnModal && event.target === returnModal) {
            document.querySelector('[data-action="close-modal"][data-target="returnModal"]').click();
        }
        if (onsiteBorrowModal && event.target === onsiteBorrowModal) {
            document.querySelector('[data-action="close-modal"][data-target="onsiteBorrowModal"]').click();
        }
    });

    // Modal close button handlers
    document.addEventListener('click', function(event) {
        if (event.target.dataset.action === 'close-modal') {
            const targetModal = event.target.dataset.target;
            if (targetModal) {
                document.getElementById(targetModal).classList.add('hidden');
            }
        }
    });
});
</script>

<!-- Reject Request Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Reject Equipment Request</h3>
            <form id="rejectForm" method="POST" class="mt-4">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Rejection Reason</label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="4" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                  placeholder="Please provide a reason for rejecting this request..."></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeRejectModal()"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Reject Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
@endsection