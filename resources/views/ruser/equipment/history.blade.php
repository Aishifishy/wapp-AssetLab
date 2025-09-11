@extends('layouts.ruser')

@section('title', 'Equipment Borrowing History')
@section('header', 'Equipment Borrowing History')

@section('content')
<div class="space-y-4">
    <!-- Header with action button -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="px-4 py-3 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                <h2 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    My Borrowing History
                </h2>
                <div class="flex space-x-3">
                    <div class="flex items-center space-x-2">
                        <label for="per_page" class="text-sm font-medium text-gray-700">Show:</label>
                        <select id="per_page" name="per_page" class="form-select text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 w-20">
                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-sm text-gray-500">per page</span>
                    </div>
                    <a href="{{ route('ruser.equipment.borrowed') }}" class="btn-outline">
                        <svg class="icon-sm mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2" />
                        </svg>
                        Current Borrowed
                    </a>
                    <a href="{{ route('ruser.equipment.borrow') }}" class="btn-primary">
                        <svg class="icon-sm mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Borrow Equipment
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($historyRequests->count() > 0)
        <!-- History Table -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table-standard">
                    <thead class="table-head">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Equipment
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Purpose
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Borrowed Period
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Returned
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($historyRequests as $request)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $request->equipment->name }}</div>
                                            @if($request->equipment->description)
                                                <div class="text-sm text-gray-500">{{ Str::limit($request->equipment->description, 50) }}</div>
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
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($request->status === 'returned')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Returned
                                        </span>
                                    @elseif($request->status === 'rejected')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Rejected
                                        </span>
                                    @elseif($request->status === 'approved' && $request->returned_at)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Completed
                                        </span>
                                    @elseif($request->status === 'pending')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($request->returned_at)
                                        <div class="text-gray-900">{{ $request->returned_at->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $request->returned_at->format('g:i A') }}</div>
                                    @elseif($request->status === 'rejected')
                                        <span class="text-gray-400">N/A</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($historyRequests->hasPages())
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-4">
                    {{ $historyRequests->links() }}
                </div>
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-8 text-center">
                <div class="mx-auto h-24 w-24 text-gray-300 mb-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No History Found</h3>
                <p class="text-gray-600 mb-6">You haven't borrowed any equipment yet.</p>
                <div class="flex-responsive-center space-responsive justify-center">
                    <a href="{{ route('ruser.equipment.borrow') }}" class="btn-primary">
                        <svg class="icon-sm mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Browse Equipment
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn-outline">
                        <svg class="icon-sm mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        </svg>
                        Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const perPageSelect = document.getElementById('per_page');
    
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('per_page', this.value);
            url.searchParams.set('page', '1'); // Reset to first page when changing per_page
            window.location.href = url.toString();
        });
    }
});
</script>
@endpush
