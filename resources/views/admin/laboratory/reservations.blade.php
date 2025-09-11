@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Laboratory Management</h1>
        <a href="{{ route('admin.laboratory.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            <i class="fas fa-arrow-left mr-2"></i> Back to Laboratories
        </a>
    </div>

    <x-flash-messages />

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <span class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                <i class="fas fa-calendar-check mr-2"></i>
                Reservations
            </span>
            <a href="{{ route('admin.laboratory.schedule-overrides') }}" 
               class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Schedule Overrides
            </a>
        </nav>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending Requests</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p>
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
                    <p class="text-2xl font-bold text-gray-900">{{ $approvedTodayCount }}</p>
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
                    <p class="text-2xl font-bold text-gray-900">{{ $rejectedTodayCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <h3 class="text-lg font-medium text-gray-900">Laboratory Reservation Requests</h3>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Per Page Selector -->
                    <div class="flex items-center space-x-2">
                        <label for="perPageSelect" class="text-sm text-gray-600">Show:</label>
                        <select id="perPageSelect" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 w-20">
                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-sm text-gray-500">per page</span>
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
                        @forelse($reservations as $request)
                        <tr class="request-row" 
                            data-user="{{ strtolower($request->user->name) }}" 
                            data-laboratory="{{ strtolower($request->laboratory->name) }}" 
                            data-status="{{ $request->status }}"
                            data-date="{{ $request->reservation_date ? $request->reservation_date->format('Y-m-d') : '' }}"
                            data-search="{{ strtolower($request->user->name . ' ' . $request->user->email . ' ' . $request->laboratory->name . ' ' . ($request->purpose ?? '')) }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $request->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $request->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $request->laboratory->name }}</div>
                                <div class="text-sm text-gray-500">{{ $request->laboratory->building }} - {{ $request->laboratory->room_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($request->purpose)
                                        {{ Str::limit($request->purpose, 50) }}
                                    @else
                                        <span class="text-gray-400">No purpose provided</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($request->reservation_date && $request->formatted_start_time && $request->formatted_end_time)
                                        {{ $request->reservation_date->format('M d, Y') }}
                                        <br>
                                        {{ $request->formatted_start_time }} - {{ $request->formatted_end_time }}
                                    @else
                                        <span class="text-gray-400">Date/Time not available</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">{{ $request->duration }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium
                                        @if($request->status === 'approved') bg-green-100 text-green-800
                                        @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($request->status) }}
                                    </span>
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
                                        @if($request->rejection_reason)
                                            <div class="text-gray-600 mt-1">{{ Str::limit($request->rejection_reason, 30) }}</div>
                                        @endif
                                    </div>
                                @elseif($request->status === 'pending')
                                    <div class="text-xs text-gray-400">
                                        <div>Awaiting admin action</div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($request->status === 'pending')
                                    <div class="flex flex-col space-y-1">
                                        <div class="flex space-x-2">
                                            <form action="{{ route('admin.laboratory.approve-request', $request) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="text-green-600 hover:text-green-900"
                                                        onclick="return confirm('Are you sure you want to approve this request?')">
                                                    Approve
                                                </button>
                                            </form>
                                            <button type="button" 
                                                    class="text-red-600 hover:text-red-900"
                                                    data-modal-target="rejectModal{{ $request->id }}">
                                                Reject
                                            </button>
                                        </div>
                                        @if($request->reservation_date && $request->laboratory_id)
                                            <button type="button" 
                                                    class="text-blue-600 hover:text-blue-900 text-xs"
                                                    onclick="viewReservationDetails({{ $request->id }})">
                                                View
                                            </button>
                                        @endif
                                    </div>
                                @elseif($request->status === 'approved' && $request->reservation_date && $request->laboratory_id)
                                    <div class="flex flex-col space-y-1">
                                        <span class="text-gray-400 text-sm">Processed {{ $request->updated_at->diffForHumans() }}</span>
                                        <button type="button" 
                                                class="text-blue-600 hover:text-blue-900 text-xs"
                                                onclick="viewReservationDetails({{ $request->id }})">
                                            View
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400">Processed {{ $request->updated_at->diffForHumans() }}</span>
                                @endif
                            </td>
                        </tr>

                        @if($request->status === 'pending')
                        <!-- Reject Modal -->
                        <div id="rejectModal{{ $request->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                    <form action="{{ route('admin.laboratory.reject-request', $request) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
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
                        @endif
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No pending reservation requests found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($reservations->hasPages())
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing {{ $reservations->firstItem() }} to {{ $reservations->lastItem() }} of {{ $reservations->total() }} reservation requests
                </div>
                <div class="flex space-x-1">
                    {{ $reservations->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>


</div>

<!-- View Details Modal -->
<div id="viewDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Reservation Details</h3>
                <button type="button" onclick="closeViewDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="modalContent" class="space-y-4">
                <!-- Content will be loaded here via JavaScript -->
                <div class="text-center py-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="mt-2 text-gray-500">Loading details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// View reservation details modal functionality
function viewReservationDetails(reservationId) {
    const modal = document.getElementById('viewDetailsModal');
    const modalContent = document.getElementById('modalContent');
    
    // Show modal with loading state
    modal.classList.remove('hidden');
    modalContent.innerHTML = `
        <div class="text-center py-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-2 text-gray-500">Loading details...</p>
        </div>
    `;
    
    // Fetch reservation details
    fetch(`{{ url('admin/laboratory/reservation') }}/${reservationId}/details`)
        .then(response => response.json())
        .then(data => {
            modalContent.innerHTML = `
                <div class="max-h-96 overflow-y-auto">
                    <!-- Header with Status Badge -->
                    <div class="flex items-center justify-between mb-6 p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h5 class="text-lg font-medium text-gray-900">Reservation #${data.id}</h5>
                            <p class="text-sm text-gray-600">Submitted ${data.created_at}</p>
                        </div>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                            ${data.status === 'approved' ? 'bg-green-100 text-green-800' : 
                              data.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                              data.status === 'rejected' ? 'bg-red-100 text-red-800' : 
                              'bg-gray-100 text-gray-800'}">
                            ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}
                        </span>
                    </div>

                    <!-- User & Laboratory Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h6 class="font-medium text-blue-900 mb-3 flex items-center">
                                <i class="fas fa-user mr-2"></i>Requester Information
                            </h6>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Name:</span>
                                    <span class="font-medium text-gray-900">${data.user.name}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-medium text-gray-900">${data.user.email}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h6 class="font-medium text-green-900 mb-3 flex items-center">
                                <i class="fas fa-flask mr-2"></i>Laboratory Details
                            </h6>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Name:</span>
                                    <span class="font-medium text-gray-900">${data.laboratory.name}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Location:</span>
                                    <span class="font-medium text-gray-900">${data.laboratory.building}, Room ${data.laboratory.room_number}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Capacity:</span>
                                    <span class="font-medium text-gray-900">${data.laboratory.capacity} people</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reservation Details -->
                    <div class="bg-purple-50 p-4 rounded-lg mb-6">
                        <h6 class="font-medium text-purple-900 mb-3 flex items-center">
                            <i class="fas fa-calendar-alt mr-2"></i>Reservation Details
                        </h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date:</span>
                                <span class="font-medium text-gray-900">${data.reservation_date}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Time:</span>
                                <span class="font-medium text-gray-900">${data.start_time} - ${data.end_time}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Students:</span>
                                <span class="font-medium text-gray-900">${data.num_students}</span>
                            </div>
                            ${data.course_code ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Course:</span>
                                <span class="font-medium text-gray-900">${data.course_code}</span>
                            </div>
                            ` : ''}
                            ${data.subject ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subject:</span>
                                <span class="font-medium text-gray-900">${data.subject}</span>
                            </div>
                            ` : ''}
                            ${data.section ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Section:</span>
                                <span class="font-medium text-gray-900">${data.section}</span>
                            </div>
                            ` : ''}
                        </div>
                    </div>

                    <!-- Purpose -->
                    <div class="mb-6">
                        <h6 class="font-medium text-gray-900 mb-2 flex items-center">
                            <i class="fas fa-align-left mr-2"></i>Purpose
                        </h6>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-700">${data.purpose || 'No purpose provided'}</p>
                        </div>
                    </div>

                    <!-- Recurring Information -->
                    ${data.is_recurring ? `
                    <div class="bg-indigo-50 p-4 rounded-lg mb-6">
                        <h6 class="font-medium text-indigo-900 mb-3 flex items-center">
                            <i class="fas fa-redo mr-2"></i>Recurring Information
                        </h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pattern:</span>
                                <span class="font-medium text-gray-900 capitalize">${data.recurrence_pattern}</span>
                            </div>
                            ${data.recurrence_end_date ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">End Date:</span>
                                <span class="font-medium text-gray-900">${data.recurrence_end_date}</span>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                    ` : ''}

                    <!-- Submitted Form Image -->
                    ${data.has_form_image ? `
                    <div class="bg-amber-50 p-4 rounded-lg mb-6">
                        <h6 class="font-medium text-amber-900 mb-3 flex items-center">
                            <i class="fas fa-file-image mr-2"></i>Submitted Form Document
                        </h6>
                        <div class="text-center">
                            <div class="relative inline-block">
                                <img src="${data.form_image_url}" 
                                     alt="Submitted Form" 
                                     class="max-w-full max-h-64 mx-auto rounded-lg border-2 border-amber-200 shadow-md cursor-pointer hover:shadow-lg transition-shadow duration-200" 
                                     onclick="window.open('${data.form_image_url}', '_blank')"
                                     onerror="this.parentElement.innerHTML='<div class=\\'text-center p-8 bg-red-50 rounded-lg\\'><i class=\\'fas fa-exclamation-triangle text-red-500 text-2xl mb-2\\'></i><p class=\\'text-red-700\\'>Image could not be loaded</p></div>'">
                                <div class="absolute top-2 right-2 bg-white bg-opacity-90 rounded-full p-2 cursor-pointer hover:bg-opacity-100 transition-all duration-200" onclick="window.open('${data.form_image_url}', '_blank')">
                                    <i class="fas fa-expand-alt text-gray-600 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-sm text-amber-700 mt-3">
                                <i class="fas fa-info-circle mr-1"></i>
                                Click image to view in full size
                            </p>
                        </div>
                    </div>
                    ` : ''}

                    <!-- Rejection Reason -->
                    ${data.rejection_reason ? `
                    <div class="bg-red-50 border border-red-200 p-4 rounded-lg mb-6">
                        <h6 class="font-medium text-red-900 mb-2 flex items-center">
                            <i class="fas fa-times-circle mr-2"></i>Rejection Reason
                        </h6>
                        <p class="text-sm text-red-700">${data.rejection_reason}</p>
                    </div>
                    ` : ''}

                    <!-- Timeline -->
                    <div class="border-t pt-4">
                        <h6 class="font-medium text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-history mr-2"></i>Timeline
                        </h6>
                        <div class="space-y-3">
                            <div class="flex items-center text-sm">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-gray-600">Created:</span>
                                <span class="font-medium text-gray-900 ml-2">${data.created_at}</span>
                            </div>
                            ${data.approved_at ? `
                            <div class="flex items-center text-sm">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-gray-600">Approved:</span>
                                <span class="font-medium text-gray-900 ml-2">${data.approved_at}</span>
                                ${data.approved_by_name ? `<span class="text-gray-500 ml-1">by ${data.approved_by_name}</span>` : ''}
                            </div>
                            ` : ''}
                            ${data.rejected_at ? `
                            <div class="flex items-center text-sm">
                                <div class="w-2 h-2 bg-red-500 rounded-full mr-3"></div>
                                <span class="text-gray-600">Rejected:</span>
                                <span class="font-medium text-gray-900 ml-2">${data.rejected_at}</span>
                                ${data.rejected_by_name ? `<span class="text-gray-500 ml-1">by ${data.rejected_by_name}</span>` : ''}
                            </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error fetching reservation details:', error);
            modalContent.innerHTML = `
                <div class="text-center py-4">
                    <div class="text-red-500 mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500">Error loading reservation details. Please try again.</p>
                </div>
            `;
        });
}

function closeViewDetailsModal() {
    document.getElementById('viewDetailsModal').classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('viewDetailsModal');
    if (e.target === modal) {
        closeViewDetailsModal();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('requestsTable');
    const rows = table.querySelectorAll('.request-row');
    
    let sortDirection = {};
    let currentSort = null;

    // Per-page selector
    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('per_page', this.value);
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        });
    }

    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterTable(rows, this.value.toLowerCase());
        });
    }

    // Sorting functionality
    if (table) {
        table.querySelectorAll('th[data-sort]').forEach(header => {
            header.addEventListener('click', function() {
                const sortKey = this.dataset.sort;
                sortTable(rows, table, sortKey);
            });
        });
    }

    function filterTable(tableRows, searchTerm) {
        tableRows.forEach(row => {
            const searchData = row.dataset.search;
            
            const matchesSearch = searchData.includes(searchTerm);
            
            if (matchesSearch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function sortTable(tableRows, tableElement, sortKey) {
        // Toggle sort direction
        if (currentSort === sortKey) {
            sortDirection[sortKey] = sortDirection[sortKey] === 'asc' ? 'desc' : 'asc';
        } else {
            sortDirection[sortKey] = 'asc';
            currentSort = sortKey;
        }

        // Update sort icons
        tableElement.querySelectorAll('th[data-sort] i').forEach(icon => {
            icon.className = 'fas fa-sort ml-2 text-gray-400';
        });
        
        const currentHeader = tableElement.querySelector(`th[data-sort="${sortKey}"] i`);
        if (sortDirection[sortKey] === 'asc') {
            currentHeader.className = 'fas fa-sort-up ml-2 text-gray-600';
        } else {
            currentHeader.className = 'fas fa-sort-down ml-2 text-gray-600';
        }

        // Convert NodeList to Array and sort
        const rowsArray = Array.from(tableRows);
        const tbody = tableElement.querySelector('tbody');
        
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
</script>

@endsection
