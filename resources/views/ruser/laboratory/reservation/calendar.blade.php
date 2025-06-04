@extends('layouts.ruser')

@section('title', 'Laboratory Reservation Calendar')
@section('header', 'Laboratory Reservation Calendar')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<!-- Styles moved to app.css -->
@endsection

@section('content')
<div class="space-y-6">
    <x-flash-messages />
    
    <!-- Filter Options -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Filter Options</h2>
        </div>
        <div class="p-6">
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="laboratory" class="block text-sm font-medium text-gray-700 mb-1">Laboratory</label>
                    <select id="laboratory" name="laboratory" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Laboratories</option>
                        @foreach($laboratories as $lab)
                            <option value="{{ $lab->id }}" {{ $selectedLab == $lab->id ? 'selected' : '' }}>
                                {{ $lab->name }} ({{ $lab->building }}, Room {{ $lab->room_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="view" class="block text-sm font-medium text-gray-700 mb-1">View Type</label>
                    <select id="view" name="view" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="dayGridMonth" {{ $view == 'dayGridMonth' ? 'selected' : '' }}>Month</option>
                        <option value="timeGridWeek" {{ $view == 'timeGridWeek' ? 'selected' : '' }}>Week</option>
                        <option value="timeGridDay" {{ $view == 'timeGridDay' ? 'selected' : '' }}>Day</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Calendar Legend -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <div class="flex flex-wrap">
            <div class="legend-item">
                <div class="legend-color legend-color-schedule"></div>
                <span>Regular Classes</span>
            </div>
            <div class="legend-item">
                <div class="legend-color legend-color-approved"></div>
                <span>Approved Reservations</span>
            </div>
            <div class="legend-item">
                <div class="legend-color legend-color-pending"></div>
                <span>My Pending Reservations</span>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Reservation Calendar</h2>
        </div>
        <div class="p-6">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Event Modal -->
<div id="eventModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900" id="modal-title">Reservation Details</h3>
            <button id="closeModal" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6" id="modal-content">
            <!-- Event details will be populated here -->
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
            <button id="viewDetailsBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                View Full Details
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
    // Expose calendar configuration for calendar manager
    window.calendarConfig = {
        view: '{{ $view }}',
        events: @json($events),
        laboratory: @json($laboratory ?? null),
        isReservationCalendar: true
    };
</script>
@endsection
