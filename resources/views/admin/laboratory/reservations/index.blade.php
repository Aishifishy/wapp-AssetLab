@extends('layouts.admin')

@section('title', 'Laboratory Reservations')
@section('header', 'Laboratory Reservations')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Filters</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.laboratory.reservations.index') }}" method="GET">
                <div class="flex flex-wrap gap-4">
                    <div class="flex-grow min-w-[200px]">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status" name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All ({{ $statusCounts['all'] }})</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending ({{ $statusCounts['pending'] }})</option>
                            <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved ({{ $statusCounts['approved'] }})</option>
                            <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected ({{ $statusCounts['rejected'] }})</option>
                            <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled ({{ $statusCounts['cancelled'] }})</option>
                        </select>
                    </div>
                    <div class="flex-grow min-w-[200px]">
                        <label for="laboratory" class="block text-sm font-medium text-gray-700 mb-1">Laboratory</label>
                        <select id="laboratory" name="laboratory" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">All Laboratories</option>
                            @foreach($laboratories as $lab)
                                <option value="{{ $lab->id }}" {{ $laboratory == $lab->id ? 'selected' : '' }}>
                                    {{ $lab->name }} ({{ $lab->building }}, Room {{ $lab->room_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reservations List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">
                Laboratory Reservations 
                @if($status !== 'all')
                    - {{ ucfirst($status) }}
                @endif
            </h2>
        </div>
        <div class="p-6">
            @if($reservations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reservations as $reservation)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">{{ $reservation->id }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $reservation->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $reservation->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $reservation->laboratory->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $reservation->laboratory->building }}, Room {{ $reservation->laboratory->room_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $reservation->reservation_date->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($reservation->status == 'pending')
                                                bg-yellow-100 text-yellow-800
                                            @elseif($reservation->status == 'approved')
                                                bg-green-100 text-green-800
                                            @elseif($reservation->status == 'rejected')
                                                bg-red-100 text-red-800
                                            @elseif($reservation->status == 'cancelled')
                                                bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($reservation->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $reservation->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.laboratory.reservations.show', $reservation) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        
                                        @if($reservation->status == 'pending')
                                            <a href="#" onclick="document.getElementById('approve-form-{{ $reservation->id }}').submit();" class="text-green-600 hover:text-green-900 mr-3">Approve</a>
                                            <form id="approve-form-{{ $reservation->id }}" action="{{ route('admin.laboratory.reservations.approve', $reservation) }}" method="POST" class="hidden">
                                                @csrf
                                            </form>
                                            
                                            <a href="#" onclick="openRejectModal({{ $reservation->id }})" class="text-red-600 hover:text-red-900">Reject</a>
                                        @endif
                                        
                                        @if(in_array($reservation->status, ['rejected', 'cancelled']))
                                            <a href="#" onclick="if(confirm('Are you sure you want to delete this reservation?')) document.getElementById('delete-form-{{ $reservation->id }}').submit();" class="text-gray-600 hover:text-gray-900">Delete</a>
                                            <form id="delete-form-{{ $reservation->id }}" action="{{ route('admin.laboratory.reservations.destroy', $reservation) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $reservations->appends(request()->query())->links() }}
                </div>
            @else
                <p class="text-gray-500">No reservations found matching the current filters.</p>
            @endif
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejection-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Reject Reservation</h3>
            <button type="button" onclick="closeRejectModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="reject-form" action="" method="POST">
            @csrf
            <div class="mb-4">
                <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason</label>
                <textarea id="rejection_reason" name="rejection_reason" rows="4" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="button" onclick="closeRejectModal()" class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 mr-3">
                    Cancel
                </button>
                <button type="submit" class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700">
                    Reject Reservation
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openRejectModal(reservationId) {
        document.getElementById('reject-form').action = `/admin/laboratory/reservations/${reservationId}/reject`;
        document.getElementById('rejection_reason').value = '';
        document.getElementById('rejection-modal').classList.remove('hidden');
    }
    
    function closeRejectModal() {
        document.getElementById('rejection-modal').classList.add('hidden');
    }
</script>
@endpush
@endsection
