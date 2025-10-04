@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            Laboratory Schedule Calendar
            @if($selectedLaboratory)
                - {{ $selectedLaboratory->name }}
            @endif
        </h1>
        <div class="flex items-center space-x-4">
            <span class="text-gray-500">Current Term: {{ $currentTerm->name }}</span>
            @if($selectedLaboratory)
                <a href="{{ route('admin.comlab.schedule.create', $selectedLaboratory) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>
                    Add Schedule
                </a>
            @endif
        </div>
    </div>    <!-- Laboratory Information (when specific laboratory is selected) -->
    @if($selectedLaboratory)
        <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <x-status-badge :status="$selectedLaboratory->status" type="laboratory" class="px-2.5 py-0.5" />
                        <span>Capacity: {{ $selectedLaboratory->capacity }} students</span>
                    </div>
                </div>
                <a href="{{ route('admin.comlab.calendar') }}" 
                   class="text-sm text-blue-600 hover:text-blue-800">
                    <i class="fas fa-times mr-1"></i>Back to All Laboratories
                </a>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    <x-flash-messages />

    <!-- Laboratory Summary Cards (only show when no specific laboratory is selected) -->
    @if(!$selectedLaboratory)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
            @foreach($laboratories as $laboratory)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-900 truncate">{{ $laboratory->name }}</h3>
                    <x-status-badge :status="$laboratory->status" type="laboratory" />
                </div>
                <p class="text-xs text-gray-500 mb-3">{{ $laboratory->building }} - Room {{ $laboratory->room_number }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-600">
                        @if(isset($schedules[$laboratory->id]))
                            {{ $schedules[$laboratory->id]->count() }} schedule(s)
                        @else
                            No schedules
                        @endif                    </span>
                    <a href="{{ route('admin.comlab.calendar', ['laboratory_id' => $laboratory->id]) }}" 
                       class="text-xs text-blue-600 hover:text-blue-800">
                        View Schedule
                    </a>
                </div>
            </div>
        @endforeach
        </div>
    @endif

    <!-- Week Navigation (shown for both views) -->
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-2">
            <button id="prev-week-btn" 
                    class="inline-flex items-center px-3 py-1 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-chevron-left mr-1"></i>Previous
            </button>
            <div class="text-sm font-medium text-gray-900" id="week-display">
                {{ $weekData['selected_week_start']->format('M d') }} - {{ $weekData['selected_week_end']->format('M d, Y') }}
                @if($weekData['is_current_week'])
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 ml-2" id="current-week-badge">
                        Current Week
                    </span>
                @endif
            </div>
            <button id="next-week-btn" 
                    class="inline-flex items-center px-3 py-1 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Next<i class="fas fa-chevron-right ml-1"></i>
            </button>
            <button id="current-week-btn" 
                    class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $weekData['is_current_week'] ? 'hidden' : '' }}">
                <i class="fas fa-home mr-1"></i>Current Week
            </button>
        </div>
        
        <!-- Legend -->
        <div class="flex items-center space-x-2 text-xs">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-blue-100 border border-blue-200 rounded mr-1"></div>
                <span>Regular Class</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-yellow-100 border border-yellow-200 rounded mr-1"></div>
                <span>Special Class</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-green-100 border border-green-200 rounded mr-1"></div>
                <span>Reservation</span>
            </div>
        </div>
    </div>

    <!-- Schedule View Type Toggle -->
    <div class="mb-4 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <span class="text-sm font-medium text-gray-700">View:</span>
            <div class="flex rounded-md shadow-sm">
                <button id="tabular-view" 
                        data-view-type="tabular"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <i class="fas fa-table mr-2"></i>Tabular
                </button>
                <button id="calendar-view" 
                        data-view-type="calendar"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-r-md hover:bg-gray-50 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 border-l-0">
                    <i class="fas fa-calendar mr-2"></i>Calendar
                </button>
            </div>
        </div>
    </div>

    <!-- Tabular View -->
    <div id="tabular-content" class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($selectedLaboratory)
            <!-- Single Laboratory View -->
            @php
                $laboratoriesToShow = collect([$selectedLaboratory]);
            @endphp
        @else
            <!-- All Laboratories View -->
            @php
                $laboratoriesToShow = $laboratories;
            @endphp
        @endif

        @foreach($laboratoriesToShow as $laboratory)
            @php
                $labSchedules = isset($schedules[$laboratory->id]) ? $schedules[$laboratory->id] : collect();
                $labReservations = isset($reservations[$laboratory->id]) ? $reservations[$laboratory->id] : collect();
                
                // Prepare entries for this laboratory similar to ruser format
                $allEntries = collect();
                $weekStart = $weekData['selected_week_start'];
                $weekEnd = $weekData['selected_week_end'];
                
                // Add regular schedules for this week
                if($labSchedules->count() > 0) {
                    foreach($labSchedules as $schedule) {
                        $dayOfWeek = $schedule->day_of_week;
                        $scheduleDate = $weekStart->copy()->addDays($dayOfWeek);
                        
                        $allEntries->push([
                            'type' => 'schedule',
                            'laboratory' => $laboratory,
                            'subject' => $schedule->subject_name,
                            'subject_code' => $schedule->subject_code,
                            'instructor' => $schedule->instructor_name,
                            'course' => $schedule->section,
                            'date' => $scheduleDate,
                            'start_time' => $schedule->start_time,
                            'end_time' => $schedule->end_time,
                            'schedule_type' => $schedule->type,
                            'schedule_obj' => $schedule,
                            'sort_key' => $scheduleDate->format('N') . '-' . $schedule->start_time
                        ]);
                    }
                }
                
                // Add reservations for this week
                if($labReservations->count() > 0) {
                    foreach($labReservations as $reservation) {
                        $reservationDate = \Carbon\Carbon::parse($reservation->reservation_date);
                        if($reservationDate->between($weekStart, $weekEnd)) {
                            $allEntries->push([
                                'type' => 'reservation',
                                'laboratory' => $laboratory,
                                'subject' => $reservation->purpose,
                                'subject_code' => null,
                                'instructor' => $reservation->user->name,
                                'course' => $reservation->course_code ?? 'N/A',
                                'date' => $reservationDate,
                                'start_time' => $reservation->start_time,
                                'end_time' => $reservation->end_time,
                                'schedule_type' => 'reservation',
                                'status' => $reservation->status,
                                'reservation_obj' => $reservation,
                                'sort_key' => $reservationDate->format('N') . '-' . $reservation->start_time
                            ]);
                        }
                    }
                }
                
                $sortedEntries = $allEntries->sortBy('sort_key');
            @endphp

            @if($sortedEntries->count() > 0 || !$selectedLaboratory)
            <div class="mb-6 last:mb-0">
                <!-- Laboratory Header (only show if viewing all laboratories) -->
                @if(!$selectedLaboratory)
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-desktop text-gray-500 mr-3"></i>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $laboratory->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $laboratory->building }}, Room {{ $laboratory->room_number }} • Capacity: {{ $laboratory->capacity }} students</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $laboratory->status === 'available' ? 'bg-green-100 text-green-800' : 
                                       ($laboratory->status === 'in_use' ? 'bg-blue-100 text-blue-800' :
                                       ($laboratory->status === 'under_maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ str_replace('_', ' ', ucfirst($laboratory->status)) }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $sortedEntries->where('type', 'schedule')->count() }} classes • {{ $sortedEntries->where('type', 'reservation')->count() }} reservations
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Single Laboratory Controls -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center space-x-4">
                                <h4 class="text-sm font-medium text-gray-900">Schedule Details</h4>
                                <span class="text-xs text-gray-500">
                                    {{ $sortedEntries->where('type', 'schedule')->count() }} regular classes • {{ $sortedEntries->where('type', 'reservation')->count() }} reservations
                                </span>
                            </div>
                            <div class="mt-2 sm:mt-0 text-xs text-gray-500">
                                Week of {{ $weekData['selected_week_start']->format('M d') }} - {{ $weekData['selected_week_end']->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 md:table mobile-table-stack">
                        <thead class="bg-gray-50">
                            <tr>
                                @if(!$selectedLaboratory)
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hide-on-mobile">Laboratory</th>
                                @endif
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject/Purpose</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor/User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hide-on-mobile">Course/Section</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day/Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hide-on-mobile">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if($sortedEntries->count() > 0)
                                @foreach($sortedEntries as $entry)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150 table-view-row">
                                        @if(!$selectedLaboratory)
                                            <td class="px-4 py-4 whitespace-nowrap hide-on-mobile" data-label="Laboratory">
                                                <div class="text-sm font-medium text-gray-900">{{ $entry['laboratory']->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $entry['laboratory']->building }} - {{ $entry['laboratory']->room_number }}</div>
                                            </td>
                                        @endif
                                        <td class="px-4 py-4 whitespace-nowrap" data-label="Subject">
                                            <div class="text-sm font-medium text-gray-900">{{ $entry['subject'] }}</div>
                                            @if($entry['subject_code'])
                                                <div class="text-sm text-gray-500">{{ $entry['subject_code'] }}</div>
                                            @endif
                                            @if($entry['type'] === 'reservation' && isset($entry['status']))
                                                <div class="text-xs text-gray-500 mt-1 md:hidden">
                                                    <x-status-badge :status="$entry['status']" type="reservation" />
                                                </div>
                                            @endif
                                            @if(!$selectedLaboratory)
                                                <div class="text-xs text-gray-500 mt-1 md:hidden">{{ $entry['laboratory']->name }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap" data-label="Instructor">
                                            <div class="text-sm text-gray-900">{{ $entry['instructor'] }}</div>
                                            @if($entry['type'] === 'reservation')
                                                <div class="text-xs text-gray-500">Requestor</div>
                                            @endif
                                            <div class="text-xs text-gray-500 mt-1 md:hidden">
                                                {{ $entry['course'] }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 hide-on-mobile" data-label="Course">
                                            {{ $entry['course'] }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap" data-label="Date">
                                            <div class="text-sm font-medium text-gray-900">{{ $entry['date']->format('l') }}</div>
                                            <div class="text-xs text-gray-500">{{ $entry['date']->format('M d, Y') }}</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap" data-label="Time">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ date('h:i A', strtotime($entry['start_time'])) }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                to {{ date('h:i A', strtotime($entry['end_time'])) }}
                                            </div>
                                            <div class="md:hidden mt-1">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($entry['schedule_type'] === 'regular')
                                                        bg-blue-100 text-blue-800
                                                    @elseif($entry['schedule_type'] === 'special')
                                                        bg-yellow-100 text-yellow-800
                                                    @else
                                                        bg-green-100 text-green-800
                                                    @endif">
                                                    @if($entry['schedule_type'] === 'reservation')
                                                        Reservation
                                                    @else
                                                        {{ ucfirst($entry['schedule_type']) }} Class
                                                    @endif
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap hide-on-mobile" data-label="Type">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($entry['schedule_type'] === 'regular')
                                                    bg-blue-100 text-blue-800
                                                @elseif($entry['schedule_type'] === 'special')
                                                    bg-yellow-100 text-yellow-800
                                                @else
                                                    bg-green-100 text-green-800
                                                @endif">
                                                <div class="w-2 h-2 
                                                    @if($entry['schedule_type'] === 'regular')
                                                        bg-blue-400
                                                    @elseif($entry['schedule_type'] === 'special')
                                                        bg-yellow-400
                                                    @else
                                                        bg-green-400
                                                    @endif
                                                    rounded-full mr-1.5"></div>
                                                @if($entry['schedule_type'] === 'reservation')
                                                    Reservation
                                                @else
                                                    {{ ucfirst($entry['schedule_type']) }} Class
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2" data-label="Actions">
                                            @if($entry['type'] === 'schedule')
                                                <a href="{{ route('admin.comlab.schedule.edit', [$entry['laboratory'], $entry['schedule_obj']]) }}" 
                                                   class="text-blue-600 hover:text-blue-800 inline-flex items-center">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="text-red-600 hover:text-red-800 delete-schedule-btn inline-flex items-center"
                                                        data-form-id="deleteForm{{ $entry['schedule_obj']->id }}"
                                                        data-confirm-message="Are you sure you want to delete this schedule?">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="deleteForm{{ $entry['schedule_obj']->id }}" 
                                                      action="{{ route('admin.comlab.schedule.destroy', [$entry['laboratory'], $entry['schedule_obj']]) }}" 
                                                      method="POST" 
                                                      class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @else
                                                <span class="text-green-600 inline-flex items-center" title="Reservation">
                                                    <i class="fas fa-calendar-check"></i>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="{{ !$selectedLaboratory ? '8' : '7' }}" class="px-6 py-12 text-center empty-state">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-calendar-check text-4xl text-gray-300 mb-4"></i>
                                            <h3 class="text-sm font-medium text-gray-900 mb-2">No Scheduled Activities</h3>
                                            <p class="text-sm text-gray-500">{{ $laboratory->name }} has no scheduled classes or approved reservations for this week.</p>
                                            <div class="mt-4">
                                                <a href="{{ route('admin.comlab.schedule.create', $laboratory) }}" 
                                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-50 hover:bg-blue-100">
                                                    <i class="fas fa-plus mr-2"></i>Add Schedule
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        @endforeach

        @if($laboratories->sum(function($lab) use ($schedules, $reservations) {
            $labSchedules = isset($schedules[$lab->id]) ? $schedules[$lab->id]->count() : 0;
            $labReservations = isset($reservations[$lab->id]) ? $reservations[$lab->id]->count() : 0;
            return $labSchedules + $labReservations;
        }) === 0)
            <div class="px-6 py-12 text-center empty-state">
                <div class="flex flex-col items-center">
                    <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-sm font-medium text-gray-900 mb-2">No Scheduled Activities</h3>
                    <p class="text-sm text-gray-500">No laboratories have scheduled classes or approved reservations for this week.</p>
                </div>
            </div>
        @endif
    </div>    <!-- Calendar View (Hidden by default) -->
    <div id="calendar-content" class="hidden space-y-6">
        @php
            $laboratoriesToShow = $selectedLaboratory ? collect([$selectedLaboratory]) : $laboratories;
            // Generate current week dates for the modern weekly calendar view
            $now = \Carbon\Carbon::now();
            $startOfWeek = $now->copy()->startOfWeek();
            $weekDates = [];
            for($i = 0; $i < 7; $i++) {
                $weekDates[] = $startOfWeek->copy()->addDays($i);
            }
        @endphp
        
        @foreach($laboratoriesToShow as $laboratory)
            @if(isset($schedules[$laboratory->id]) || !$selectedLaboratory)
            <div id="lab-{{ $laboratory->id }}" class="bg-white rounded-lg shadow-sm overflow-hidden">
                <!-- Laboratory Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <i class="fas fa-desktop text-gray-500 mr-3"></i>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $laboratory->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $laboratory->building }}, Room {{ $laboratory->room_number }} • Capacity: {{ $laboratory->capacity }} students</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                {{ $laboratory->status === 'available' ? 'bg-green-100 text-green-800' : 
                                   ($laboratory->status === 'in_use' ? 'bg-blue-100 text-blue-800' :
                                   ($laboratory->status === 'under_maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ str_replace('_', ' ', ucfirst($laboratory->status)) }}
                            </span>
                            @if($selectedLaboratory)
                                <a href="{{ route('admin.comlab.schedule.create', $laboratory) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-plus mr-1"></i>Add Schedule
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Calendar Container -->
                <div class="p-3 md:p-6">
                    <!-- Mobile Calendar Notice -->
                    <div class="md:hidden bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-2"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">Mobile Calendar View</p>
                                <p class="text-xs">Scroll horizontally to view all days • Tap schedules for details • Switch to Table view for better mobile experience</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Horizontal Scrollable Calendar Container -->
                    <div class="calendar-scroll-container overflow-x-auto overflow-y-hidden" style="scroll-behavior: smooth; -webkit-overflow-scrolling: touch;">
                        <div class="calendar-wrapper" style="min-width: 800px;">
                            <table class="calendar-table w-full divide-y divide-gray-200" style="table-layout: fixed; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="calendar-time-slot text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 sticky left-0 z-10" 
                                            style="width: 80px; min-width: 80px; padding: 8px;">Time</th>
                                        @foreach($weekData['week_dates'] as $date)
                                            <th class="calendar-day-slot text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50" 
                                                style="width: 120px; min-width: 120px; padding: 8px;">
                                                <div class="flex flex-col items-center">
                                                    <span class="text-xs">{{ $date->format('D') }}</span>
                                                    <span class="text-lg font-semibold text-gray-800 mt-1">{{ $date->format('j') }}</span>
                                                    @if($date->isToday())
                                                        <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mx-auto mt-1"></span>
                                                    @endif
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $timeSlots = [];
                                        $startTime = strtotime('07:00');
                                        $endTime = strtotime('21:00');
                                        while($startTime <= $endTime) {
                                            $timeSlots[] = date('H:i', $startTime);
                                            $startTime = strtotime('+1 hour', $startTime);
                                        }
                                    @endphp
                                    
                                    @foreach($timeSlots as $time)
                                        <tr>
                                            <td class="calendar-time-slot text-center align-middle border-r bg-gray-50 sticky left-0 z-10" 
                                                style="width: 80px; min-width: 80px; padding: 8px; height: 60px;">
                                                <div class="text-xs font-medium text-gray-700">
                                                    {{ date('h:i A', strtotime($time))}}
                                                </div>
                                            </td>
                                            @foreach($weekData['week_dates'] as $dayIndex => $currentDate)
                                                <td class="calendar-day-slot border-r border-gray-200 overflow-hidden relative" 
                                                    style="width: 120px; min-width: 120px; height: 60px; padding: 2px;">
                                                    @php
                                                        $day = $dayIndex; // 0 = Sunday, 1 = Monday, etc.
                                                        $hasSchedule = false;
                                                        $hasReservation = false;
                                                    @endphp
                                                    
                                                    {{-- Show recurring class schedules --}}
                                                    @if(isset($schedules[$laboratory->id]))
                                                        @foreach($schedules[$laboratory->id] as $schedule)
                                                            @if($schedule->day_of_week === $day && 
                                                                strtotime($schedule->start_time) <= strtotime($time) && 
                                                                strtotime($schedule->end_time) > strtotime($time))
                                                                @php $hasSchedule = true; @endphp
                                                                <div class="schedule-block w-full h-full {{ $schedule->type === 'regular' ? 'bg-blue-100' : 'bg-yellow-100' }} border-l-4 {{ $schedule->type === 'regular' ? 'border-blue-500' : 'border-yellow-500' }} rounded-r overflow-hidden cursor-pointer hover:shadow-md transition-all duration-200" 
                                                                     style="padding: 2px; font-size: 0.6rem; line-height: 1.1;"
                                                                     title="{{ $schedule->subject_name }} - {{ $schedule->instructor_name }} ({{ $schedule->section }}) {{ date('h:i A', strtotime($schedule->start_time)) }}-{{ date('h:i A', strtotime($schedule->end_time)) }}">
                                                                    <div class="flex justify-between items-start h-full">
                                                                        <div class="flex-1 min-w-0">
                                                                            <div class="font-semibold text-gray-900 truncate">{{ $schedule->subject_name }}</div>
                                                                            <div class="text-gray-700 truncate">{{ $schedule->instructor_name }}</div>
                                                                            <div class="text-gray-600 truncate">{{ $schedule->section }}</div>
                                                                        </div>
                                                                        <div class="flex flex-col space-y-1 ml-1">
                                                                            <a href="{{ route('admin.comlab.schedule.edit', [$laboratory, $schedule]) }}" 
                                                                               class="text-blue-600 hover:text-blue-800 text-xs">
                                                                                <i class="fas fa-edit"></i>
                                                                            </a>
                                                                            <button type="button" 
                                                                                    class="text-red-600 hover:text-red-800 delete-schedule-btn text-xs"
                                                                                    data-form-id="deleteForm{{ $schedule->id }}Cal"
                                                                                    data-confirm-message="Are you sure you want to delete this schedule?">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                            <form id="deleteForm{{ $schedule->id }}Cal" 
                                                                                  action="{{ route('admin.comlab.schedule.destroy', [$laboratory, $schedule]) }}" 
                                                                                  method="POST" 
                                                                                  class="hidden">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    {{-- Show individual reservations if no schedule conflict --}}
                                                    @if(!$hasSchedule && isset($reservations[$laboratory->id]) && $reservations[$laboratory->id]->count() > 0)
                                                        @foreach($reservations[$laboratory->id] as $reservation)
                                                            @php
                                                                $reservationDate = \Carbon\Carbon::parse($reservation->reservation_date);
                                                            @endphp
                                                            @if($reservationDate->isSameDay($currentDate) && 
                                                                strtotime($reservation->start_time) <= strtotime($time) && 
                                                                strtotime($reservation->end_time) > strtotime($time))
                                                                @php $hasReservation = true; @endphp
                                                                <div class="schedule-block w-full h-full bg-green-100 border-l-4 border-green-500 rounded-r overflow-hidden cursor-pointer hover:shadow-md transition-all duration-200" 
                                                                     style="padding: 2px; font-size: 0.6rem; line-height: 1.1;"
                                                                     title="{{ $reservation->purpose }} - {{ $reservation->user->name }} {{ $reservationDate->format('M d') }} {{ date('h:i A', strtotime($reservation->start_time)) }}-{{ date('h:i A', strtotime($reservation->end_time)) }}">
                                                                    <div class="flex justify-between items-start h-full">
                                                                        <div class="flex-1 min-w-0">
                                                                            <div class="font-semibold text-gray-900 truncate">{{ $reservation->purpose }}</div>
                                                                            <div class="text-gray-700 truncate">{{ $reservation->user->name }}</div>
                                                                            @if($reservation->course_code)
                                                                                <div class="text-gray-600 truncate">{{ $reservation->course_code }}</div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="flex flex-col space-y-1 ml-1">
                                                                            <span class="text-green-600 text-xs">
                                                                                <i class="fas fa-calendar-check"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
        
        <!-- Show message when selected laboratory has no schedules -->
        @if($selectedLaboratory && !isset($schedules[$selectedLaboratory->id]))
            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-calendar-times text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No schedules found</h3>
                <p class="text-gray-500 mb-4">
                    {{ $selectedLaboratory->name }} doesn't have any schedules for the current term.
                </p>
                <a href="{{ route('admin.comlab.schedule.create', $selectedLaboratory) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Add Schedule
                </a>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
/* Modern Calendar Styles */
.calendar-scroll-container {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f9fafb;
}

.calendar-scroll-container::-webkit-scrollbar {
    height: 8px;
}

.calendar-scroll-container::-webkit-scrollbar-track {
    background: #f9fafb;
    border-radius: 4px;
}

.calendar-scroll-container::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}

.calendar-scroll-container::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

.calendar-table {
    table-layout: fixed;
    width: 100%;
}

.calendar-table td {
    vertical-align: top;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.calendar-time-slot {
    background: #f9fafb;
    border-right: 2px solid #e5e7eb;
}

.calendar-day-slot {
    position: relative;
}

.schedule-block {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    transform: translateY(0);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
}

.schedule-block:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    z-index: 10;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .calendar-table td {
        height: 50px;
        padding: 0.1rem;
        font-size: 0.45rem;
    }
    
    .schedule-block {
        font-size: 0.5rem;
        padding: 0.1rem;
        line-height: 1.1;
        min-height: 100%;
    }
    
    .schedule-block div {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .schedule-block .flex {
        flex-direction: column;
    }
    
    .schedule-block .flex > div:last-child {
        display: none; /* Hide action buttons on mobile for cleaner view */
    }
    
    .calendar-day-slot .flex {
        flex-direction: column;
        align-items: center;
    }
    
    .calendar-day-slot .flex span:first-child {
        font-size: 0.55rem;
        line-height: 1;
    }
    
    .calendar-day-slot .flex span:nth-child(2) {
        font-size: 0.7rem;
        font-weight: 600;
        line-height: 1;
    }
    
    /* Mobile week navigation */
    #week-navigation {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }
    
    #week-navigation > div {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }
    
    #current-week-display {
        margin: 0.5rem 0;
        font-size: 0.75rem;
    }
}

/* Desktop specific improvements */
@media (min-width: 769px) {
    .calendar-table td {
        height: 60px;
    }
    
    .schedule-block {
        font-size: 0.65rem;
        line-height: 1.2;
        padding: 4px;
    }
}

/* Week info message styling */
.week-info-message {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Current week button styling */
.current-week-btn {
    animation: slideIn 0.2s ease-out;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(10px); }
    to { opacity: 1; transform: translateX(0); }
}

/* Mobile Table Styles */
@media (max-width: 768px) {
    .mobile-table-stack {
        display: block;
    }
    
    .mobile-table-stack thead {
        display: none;
    }
    
    .mobile-table-stack tbody {
        display: block;
    }
    
    .mobile-table-stack tr {
        display: block;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        background: white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .mobile-table-stack td {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f3f4f6;
        text-align: left;
    }
    
    .mobile-table-stack td:last-child {
        border-bottom: none;
    }
    
    .mobile-table-stack td:before {
        content: attr(data-label) ": ";
        font-weight: 600;
        color: #374151;
        flex-shrink: 0;
        margin-right: 1rem;
        min-width: 100px;
    }
    
    .hide-on-mobile {
        display: none !important;
    }
    
    .table-view-row:hover {
        background-color: #f9fafb;
    }
    
    .empty-state {
        padding: 3rem 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get view toggle buttons
    const tabularView = document.getElementById('tabular-view');
    const calendarView = document.getElementById('calendar-view');
    const tabularContent = document.getElementById('tabular-content');
    const calendarContent = document.getElementById('calendar-content');
    
    // Week navigation elements
    const prevWeekBtn = document.getElementById('prev-week-btn');
    const nextWeekBtn = document.getElementById('next-week-btn');
    const currentWeekBtn = document.getElementById('current-week-btn');
    const weekDisplay = document.getElementById('week-display');
    
    // Track current state
    let currentWeekOffset = {!! json_encode($weekData['current_offset']) !!};
    let currentView = 'tabular'; // default view
    let isLoading = false;
    
    // Function to switch to tabular view
    function switchToTabularView() {
        currentView = 'tabular';
        tabularView.classList.remove('text-gray-700', 'bg-gray-50');
        tabularView.classList.add('text-blue-700', 'bg-blue-50', 'border-blue-500');
        
        calendarView.classList.remove('text-blue-700', 'bg-blue-50', 'border-blue-500');
        calendarView.classList.add('text-gray-700', 'bg-white');
        
        tabularContent.classList.remove('hidden');
        calendarContent.classList.add('hidden');
    }
    
    // Function to switch to calendar view
    function switchToCalendarView() {
        currentView = 'calendar';
        calendarView.classList.remove('text-gray-700', 'bg-white');
        calendarView.classList.add('text-blue-700', 'bg-blue-50', 'border-blue-500');
        
        tabularView.classList.remove('text-blue-700', 'bg-blue-50', 'border-blue-500');
        tabularView.classList.add('text-gray-700', 'bg-white');
        
        calendarContent.classList.remove('hidden');
        tabularContent.classList.add('hidden');
    }
    
    // Function to load week data via AJAX
    function loadWeekData(weekOffset) {
        if (isLoading) return;
        
        isLoading = true;
        setLoadingState(true);
        
        const currentQuery = new URLSearchParams(window.location.search);
        if (weekOffset === 0) {
            currentQuery.delete('week');
        } else {
            currentQuery.set('week', weekOffset);
        }
        
        fetch('{{ route("admin.comlab.calendar") }}?' + currentQuery.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Update the URL without refreshing the page
            const newUrl = window.location.pathname + (currentQuery.toString() ? '?' + currentQuery.toString() : '');
            history.pushState({weekOffset: weekOffset}, '', newUrl);
            
            // Create a temporary container to parse the response
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Update tabular content
            const newTabularContent = tempDiv.querySelector('#tabular-content');
            if (newTabularContent) {
                tabularContent.innerHTML = newTabularContent.innerHTML;
            }
            
            // Update calendar content
            const newCalendarContent = tempDiv.querySelector('#calendar-content');
            if (newCalendarContent) {
                calendarContent.innerHTML = newCalendarContent.innerHTML;
            }
            
            // Update week display and navigation
            updateWeekDisplay(weekOffset);
            currentWeekOffset = weekOffset;
            
            // Maintain current view
            if (currentView === 'calendar') {
                switchToCalendarView();
            } else {
                switchToTabularView();
            }
            
            isLoading = false;
            setLoadingState(false);
        })
        .catch(error => {
            console.error('Error loading week data:', error);
            isLoading = false;
            setLoadingState(false);
        });
    }
    
    // Function to set loading state
    function setLoadingState(loading) {
        const buttons = [prevWeekBtn, nextWeekBtn, currentWeekBtn];
        buttons.forEach(btn => {
            if (btn) {
                btn.disabled = loading;
                if (loading) {
                    btn.style.opacity = '0.6';
                    btn.style.cursor = 'not-allowed';
                } else {
                    btn.style.opacity = '1';
                    btn.style.cursor = 'pointer';
                }
            }
        });
        
        if (loading) {
            weekDisplay.style.opacity = '0.6';
        } else {
            weekDisplay.style.opacity = '1';
        }
    }
    
    // Function to update week display
    function updateWeekDisplay(weekOffset) {
        const now = new Date();
        const currentWeekStart = new Date(now);
        currentWeekStart.setDate(now.getDate() - now.getDay());
        currentWeekStart.setDate(currentWeekStart.getDate() + (weekOffset * 7));
        
        const weekEnd = new Date(currentWeekStart);
        weekEnd.setDate(currentWeekStart.getDate() + 6);
        
        const formatOptions = { month: 'short', day: 'numeric' };
        const startStr = currentWeekStart.toLocaleDateString('en-US', formatOptions);
        const endStr = weekEnd.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric' 
        });
        
        let displayHTML = `${startStr} - ${endStr}`;
        if (weekOffset === 0) {
            displayHTML += '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 ml-2">Current Week</span>';
            currentWeekBtn.classList.add('hidden');
        } else {
            currentWeekBtn.classList.remove('hidden');
        }
        
        weekDisplay.innerHTML = displayHTML;
    }
    
    // Event listeners for view toggle
    tabularView.addEventListener('click', switchToTabularView);
    calendarView.addEventListener('click', switchToCalendarView);
    
    // Event listeners for week navigation
    prevWeekBtn.addEventListener('click', () => loadWeekData(currentWeekOffset - 1));
    nextWeekBtn.addEventListener('click', () => loadWeekData(currentWeekOffset + 1));
    currentWeekBtn.addEventListener('click', () => loadWeekData(0));
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function(e) {
        if (e.state && typeof e.state.weekOffset !== 'undefined') {
            currentWeekOffset = e.state.weekOffset;
            loadWeekData(currentWeekOffset);
        }
    });
    
    // Keyboard navigation for week changes
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft' && !isLoading) {
            e.preventDefault();
            loadWeekData(currentWeekOffset - 1);
        } else if (e.key === 'ArrowRight' && !isLoading) {
            e.preventDefault();
            loadWeekData(currentWeekOffset + 1);
        } else if (e.key === 'Home' && !isLoading) {
            e.preventDefault();
            loadWeekData(0);
        }
    });
    
    // Initialize with current view (tabular by default)
    switchToTabularView();
});
</script>
@endpush
@endsection