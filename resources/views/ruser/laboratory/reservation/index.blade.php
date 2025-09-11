@extends('layouts.app')

@section('title', 'My Laboratory Reservations')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">My Laboratory Reservations</h1>
            <p class="mt-1 text-sm text-gray-600">Manage your laboratory reservations</p>
        </div>
        <div class="flex space-x-4">
            @if(isset($pastReservations) && $pastReservations->count() > 0)
                <div class="flex items-center space-x-2">
                    <label for="per_page" class="text-sm font-medium text-gray-700">Show:</label>
                    <select id="per_page" name="per_page" class="form-select text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 w-20">
                        <option value="5" {{ (isset($perPage) && $perPage == 5) ? 'selected' : '' }}>5</option>
                        <option value="10" {{ (isset($perPage) && $perPage == 10) ? 'selected' : '' }}>10</option>
                        <option value="15" {{ (isset($perPage) && $perPage == 15) ? 'selected' : '' }}>15</option>
                        <option value="25" {{ (isset($perPage) && $perPage == 25) ? 'selected' : '' }}>25</option>
                        <option value="50" {{ (isset($perPage) && $perPage == 50) ? 'selected' : '' }}>50</option>
                        <option value="100" {{ (isset($perPage) && $perPage == 100) ? 'selected' : '' }}>100</option>
                    </select>
                    <span class="text-sm text-gray-500">per page</span>
                </div>
            @endif
            <a href="{{ route('ruser.laboratory.reservations.calendar') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h3z" />
                </svg>
                View Calendar
            </a>
            <a href="{{ route('ruser.laboratory.index') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                New Reservation
            </a>
        </div>
    </div>

    <div class="space-y-8">
        <!-- Pending Reservations -->
        @include('ruser.laboratory.reservation.partials.section', [
            'title' => 'Pending Reservations',
            'reservations' => $pendingReservations,
            'emptyMessage' => 'You don\'t have any pending laboratory reservations.',
            'isPending' => true,
            'showActions' => true
        ])

        <!-- Upcoming Reservations -->
        @include('ruser.laboratory.reservation.partials.section', [
            'title' => 'Upcoming Reservations',
            'reservations' => $upcomingReservations,
            'emptyMessage' => 'You don\'t have any upcoming laboratory reservations.',
            'isPending' => false,
            'showActions' => true
        ])

        <!-- Past/Rejected Reservations -->
        @include('ruser.laboratory.reservation.partials.section', [
            'title' => 'Past/Cancelled/Rejected Reservations',
            'reservations' => $pastReservations,
            'emptyMessage' => 'You don\'t have any past laboratory reservations.',
            'isPending' => false,
            'showActions' => false,
            'showPagination' => true
        ])
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirm action functionality
    document.querySelectorAll('.confirm-action').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const message = this.dataset.confirmMessage || 'Are you sure?';
            if (confirm(message)) {
                this.closest('form').submit();
            }
        });
    });

    // Per-page selector functionality
    const perPageSelect = document.getElementById('per_page');
    
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('per_page', this.value);
            url.searchParams.set('page', '1'); // Reset to first page when changing per_page
            url.searchParams.set('past_page', '1'); // Reset past reservations page too
            window.location.href = url.toString();
        });
    }
});
</script>
@endsection
