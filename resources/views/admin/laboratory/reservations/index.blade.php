@extends('layouts.admin')

@section('title', 'Laboratory Reservations')
@section('header', 'Laboratory Reservations')

@section('content')
<div class="space-y-6">
    <x-flash-messages />

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
                                    <td class="px-6 py-4 whitespace-nowrap">                                        <x-status-badge :status="$reservation->status" type="reservation" />
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
                                            <button type="button" 
                                                    class="text-gray-600 hover:text-gray-900"
                                                    data-modal-target="deleteModal{{ $reservation->id }}">
                                                Delete
                                            </button>
                                        @endif
                                    </td>                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Delete Confirmation Modals for each deletable reservation -->
                @foreach($reservations as $reservation)
                    @if(in_array($reservation->status, ['rejected', 'cancelled']))
                        <x-delete-confirmation-modal 
                            modal-id="deleteModal{{ $reservation->id }}"
                            title="Delete Reservation"
                            message="Are you sure you want to delete this reservation? This action cannot be undone."
                            item-name="Reservation #{{ $reservation->id }}"
                            delete-route="{{ route('admin.laboratory.reservations.destroy', $reservation) }}" />
                    @endif
                @endforeach
                
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
<x-rejection-modal />

@push('scripts')
<!-- Reservation rejection functionality is now handled by reservation-manager.js module -->
@endpush
@endsection
