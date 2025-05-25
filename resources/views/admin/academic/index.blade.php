@extends('layouts.admin')

@section('title', 'Academic Calendar')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<!-- Styles moved to app.css -->
@endpush

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Academic Calendar</h1>
        <a href="{{ route('admin.academic.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition">
            <i class="fas fa-plus mr-2"></i> Add New Academic Year
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
            <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <span class="sr-only">Close</span>
                <svg class="h-4 w-4 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Academic Years List -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow">
                <div class="px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-list text-gray-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-900">Academic Years</h3>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($academicYears as $year)
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            <div class="flex justify-between items-center">
                                <h4 class="font-medium text-gray-900">{{ $year->name }}</h4>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.academic.terms.create', ['academicYear' => $year->id]) }}" 
                                       class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 focus:outline-none">
                                        <i class="fas fa-plus-circle mr-1"></i> Add Term
                                    </a>
                                    @if($year->terms->contains('is_current', true))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Current
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $year->start_date->format('M d, Y') }} - {{ $year->end_date->format('M d, Y') }}
                            </p>
                            <div class="mt-2 space-y-1">
                                @foreach($year->terms as $term)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">{{ $term->name }}</span>
                                        @if($term->is_current)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                Active
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Calendar -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow">
                <div class="px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-alt text-gray-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-900">Calendar View</h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex gap-4 mb-4 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <div class="w-1 h-5 bg-blue-500 rounded"></div>
                            <span class="text-sm text-gray-600">Academic Terms</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-1 h-5 bg-green-500 rounded"></div>
                            <span class="text-sm text-gray-600">Current Term</span>
                        </div>
                    </div>
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        buttonText: {
            today: 'Today',
            month: 'Month',
            week: 'Week'
        },
        dayMaxEvents: true,
        events: {!! json_encode($calendarEvents) !!},
        eventClick: function(info) {
            const event = info.event;
            const props = event.extendedProps;
            
            // You can implement a modal or other UI for event details here
        }
    });
    calendar.render();
});
</script>
@endpush 