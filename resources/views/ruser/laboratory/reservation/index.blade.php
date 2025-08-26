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
});
</script>
@endsection
