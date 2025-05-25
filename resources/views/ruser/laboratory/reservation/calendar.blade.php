@extends('layouts.ruser')

@section('title', 'Laboratory Reservation Calendar')
@section('header', 'Laboratory Reservation Calendar')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<!-- Styles moved to app.css -->
@endsection

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
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const modal = document.getElementById('eventModal');
        const closeModal = document.getElementById('closeModal');
        const modalTitle = document.getElementById('modal-title');
        const modalContent = document.getElementById('modal-content');
        const viewDetailsBtn = document.getElementById('viewDetailsBtn');
        
        let currentEventUrl = '';
        
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: '{{ $view }}',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            slotMinTime: '07:00:00',
            slotMaxTime: '19:00:00',
            allDaySlot: false,
            height: 'auto',
            events: @json($events),
            eventClick: function(info) {
                const event = info.event;
                const eventData = event.extendedProps;
                
                if (eventData.type === 'schedule') {
                    modalTitle.textContent = 'Regular Class Schedule';
                    modalContent.innerHTML = `
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Subject</h4>
                                <p class="mt-1 text-base text-gray-900">${eventData.subject}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Time</h4>
                                <p class="mt-1 text-base text-gray-900">${eventData.time}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Section</h4>
                                <p class="mt-1 text-base text-gray-900">${eventData.section}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Instructor</h4>
                                <p class="mt-1 text-base text-gray-900">${eventData.instructor}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Laboratory</h4>
                                <p class="mt-1 text-base text-gray-900">${eventData.laboratory}</p>
                            </div>
                        </div>
                    `;
                    viewDetailsBtn.style.display = 'none';
                } else {
                    modalTitle.textContent = 'Reservation Details';
                    modalContent.innerHTML = `
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Laboratory</h4>
                                <p class="mt-1 text-base text-gray-900">${eventData.laboratory}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Time</h4>
                                <p class="mt-1 text-base text-gray-900">${event.title}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Purpose</h4>
                                <p class="mt-1 text-base text-gray-900">${eventData.purpose}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Status</h4>
                                <p class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    ${eventData.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ''}
                                    ${eventData.status === 'approved' ? 'bg-green-100 text-green-800' : ''}
                                ">
                                    ${eventData.status.charAt(0).toUpperCase() + eventData.status.slice(1)}
                                </p>
                            </div>
                        </div>
                    `;
                    
                    // Set up view details button
                    currentEventUrl = eventData.url;
                    viewDetailsBtn.style.display = 'block';
                }
                
                modal.classList.remove('hidden');
            }
        });
        
        calendar.render();
        
        closeModal.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
        
        viewDetailsBtn.addEventListener('click', function() {
            if (currentEventUrl) {
                window.location.href = currentEventUrl;
            }
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });
</script>
@endsection
