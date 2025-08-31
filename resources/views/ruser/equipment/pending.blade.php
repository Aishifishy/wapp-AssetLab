@extends('layouts.ruser')

@section('title', 'Pending Equipment Requests')
@section('header', 'Pending Equipment Requests')

@section('content')
<div class="space-y-4">
    <!-- Header with action buttons -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="px-4 py-3 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                <h2 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    My Pending Requests
                </h2>
                <div class="flex space-x-3">
                    <a href="{{ route('ruser.equipment.borrowed') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2" />
                        </svg>
                        Current Borrowed
                    </a>
                    <a href="{{ route('ruser.equipment.borrow') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Borrow Equipment
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($pendingRequests->count() > 0)
        <!-- Info banner about cancellation -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Need to remove a duplicate request?</strong> You can cancel any pending request that hasn't been approved yet. 
                        Once a request is approved by an admin, it can no longer be cancelled from here.
                    </p>
                </div>
            </div>
        </div>

        <!-- Pending Requests Table -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Equipment
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Purpose
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Requested Period
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Submitted
                            </th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingRequests as $request)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                                <svg class="h-5 w-5 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $request->equipment->name }}</div>
                                            @if($request->equipment->description)
                                                <div class="text-sm text-gray-500">{{ Str::limit($request->equipment->description, 50) }}</div>
                                            @endif
                                            @if($request->equipment->category)
                                                <div class="text-xs text-gray-400 mt-1">{{ $request->equipment->category->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs" title="{{ $request->purpose }}">
                                        {{ Str::limit($request->purpose, 80) }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="space-y-1">
                                        <div>
                                            <span class="font-medium text-gray-700">From:</span>
                                            <div class="text-gray-900">{{ $request->requested_from->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $request->requested_from->format('g:i A') }}</div>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">To:</span>
                                            <div class="text-gray-900">{{ $request->requested_until->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $request->requested_until->format('g:i A') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $request->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs">{{ $request->created_at->format('g:i A') }}</div>
                                    <div class="text-xs text-gray-400">{{ $request->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <x-status-badge 
                                        type="equipment_request" 
                                        :status="$request->status" />
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <form action="{{ route('ruser.equipment.cancel-request', $request) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to cancel this equipment request? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                                            <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Cancel
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($pendingRequests->hasPages())
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-4">
                    {{ $pendingRequests->links() }}
                </div>
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-8 text-center">
                <div class="mx-auto h-24 w-24 text-gray-300 mb-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Pending Requests</h3>
                <p class="text-gray-600 mb-6">You don't have any pending equipment requests at the moment.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('ruser.equipment.borrow') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Browse Equipment
                    </a>
                    <a href="{{ route('ruser.equipment.history') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        View History
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
// Add any additional JavaScript if needed for enhanced UX
document.addEventListener('DOMContentLoaded', function() {
    // Optional: Add confirmation with more details
    const cancelForms = document.querySelectorAll('form[action*="cancel-request"]');
    
    cancelForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const equipmentName = this.closest('tr').querySelector('td .text-sm.font-medium').textContent;
            const confirmed = confirm(`Are you sure you want to cancel your request for "${equipmentName}"?\n\nThis action cannot be undone and you'll need to submit a new request if you change your mind.`);
            
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush
@endsection
