@extends('layouts.ruser')

@section('title', 'Laboratory Schedule')
@section('header', 'Laboratory Schedule')

@push('styles')
<style>
    .sr-only {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }
    
    /* Ensure buttons are properly styled */
    .view-toggle-btn {
        transition: all 0.2s ease-in-out;
    }
    
    .view-toggle-btn:focus {
        outline: 2px solid #3B82F6;
        outline-offset: 2px;
    }
    
    /* Calendar table specific styles */
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
        width: 80px;
        min-width: 80px;
    }
    
    .calendar-day-slot {
        width: calc((100% - 80px) / 7);
        min-width: 120px;
    }
    
    .schedule-block {
        font-size: 0.6875rem; /* 11px */
        line-height: 1.2;
        overflow: hidden;
        word-break: break-word;
        hyphens: auto;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Laboratory Details -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $laboratory->name }}</h2>
                    <p class="text-gray-600">{{ $laboratory->building }}, Room {{ $laboratory->room_number }}</p>
                </div>                <div class="flex items-center space-x-4">
                    <x-status-badge :status="$laboratory->status" type="laboratory" class="px-3 py-1 text-sm" />
                    @if($currentTerm)
                        <span class="text-sm text-gray-500">Current Term: {{ $currentTerm->name }}</span>
                    @endif
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-users text-blue-500 mr-2"></i>
                        <div>
                            <p class="text-sm text-gray-600">Capacity</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $laboratory->capacity }} seats</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-desktop text-green-500 mr-2"></i>
                        <div>
                            <p class="text-sm text-gray-600">Computers</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $laboratory->number_of_computers }} units</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-calendar text-purple-500 mr-2"></i>
                        <div>
                            <p class="text-sm text-gray-600">Weekly Classes</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $schedules->count() }} schedules</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-bookmark text-orange-500 mr-2"></i>
                        <div>
                            <p class="text-sm text-gray-600">
                                @if($weekData['is_current_week'])
                                    This Week's Reservations
                                @else
                                    Week of {{ $weekData['selected_week_start']->format('M d') }}
                                @endif
                            </p>
                            <p class="text-lg font-semibold text-gray-900">{{ isset($reservations) ? $reservations->count() : 0 }} bookings</p>
                        </div>
                    </div>
                </div>
            </div>            @if(!$currentTerm)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle mr-2 mt-0.5"></i>
                        <p>There is no active academic term. Laboratory reservations are not available at this time.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Schedule Header -->
    <div class="space-y-4">
        <!-- Title and Week Info -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Weekly Schedule</h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $weekData['selected_week_start']->format('M d') }} - {{ $weekData['selected_week_end']->format('M d, Y') }}
                    @if($weekData['is_current_week'])
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                            Current Week
                        </span>
                    @endif
                </p>
            </div>
            
            <!-- View Toggle (Desktop) -->
            <div class="hidden sm:flex items-center space-x-2 mt-4 sm:mt-0">
                <span class="text-sm font-medium text-gray-700" id="view-label">View:</span>
                <div class="flex rounded-md shadow-sm" role="group" aria-labelledby="view-label" aria-describedby="view-description">
                    <button id="calendar-view" 
                            type="button"
                            role="tab"
                            aria-pressed="true"
                            aria-selected="true"
                            aria-controls="calendar-content"
                            aria-label="Calendar view - shows schedules in a weekly grid format"
                            tabindex="0"
                            class="view-toggle-btn px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-500 rounded-l-md hover:bg-blue-100 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors duration-200">
                        <i class="fas fa-calendar mr-2" aria-hidden="true"></i>Calendar
                    </button>
                    <button id="table-view" 
                            type="button"
                            role="tab"
                            aria-pressed="false"
                            aria-selected="false"
                            aria-controls="table-content"
                            aria-label="Table view - shows schedules in a detailed table format"
                            tabindex="-1"
                            class="view-toggle-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none border-l-0 transition-colors duration-200">
                        <i class="fas fa-table mr-2" aria-hidden="true"></i>Table
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation and Controls -->
        <div class="flex flex-col sm:flex-row gap-4 sm:justify-between sm:items-center">
            <!-- Week Navigation -->
            <div class="flex items-center justify-center sm:justify-start space-x-2">
                <a href="{{ route('ruser.laboratory.show', array_merge(['laboratory' => $laboratory->id], ['week' => $weekData['current_offset'] - 1])) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-chevron-left mr-2"></i>
                    Previous Week
                </a>
                @if(!$weekData['is_current_week'])
                    <a href="{{ route('ruser.laboratory.show', $laboratory) }}" 
                       class="inline-flex items-center px-4 py-2 border border-blue-600 text-sm font-medium rounded-md text-blue-600 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-calendar-day mr-2"></i>
                        Current Week
                    </a>
                @endif
                <a href="{{ route('ruser.laboratory.show', array_merge(['laboratory' => $laboratory->id], ['week' => $weekData['current_offset'] + 1])) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Next Week
                    <i class="fas fa-chevron-right ml-2"></i>
                </a>
            </div>
            
            <!-- Legend -->
            <div class="flex items-center justify-center sm:justify-end space-x-4 text-xs">
                <div class="flex items-center space-x-1">
                    <div class="w-3 h-3 bg-blue-100 border border-blue-200 rounded"></div>
                    <span class="text-gray-700">Regular Class</span>
                </div>
                <div class="flex items-center space-x-1">
                    <div class="w-3 h-3 bg-yellow-100 border border-yellow-200 rounded"></div>
                    <span class="text-gray-700">Special Class</span>
                </div>
                <div class="flex items-center space-x-1">
                    <div class="w-3 h-3 bg-green-100 border border-green-200 rounded"></div>
                    <span class="text-gray-700">Approved Reservation</span>
                </div>
            </div>
        </div>

        <!-- Mobile View Toggle -->
        <div class="flex sm:hidden items-center justify-center">
            <div class="flex items-center space-x-2">
                <span class="text-sm font-medium text-gray-700">View:</span>
                <div class="flex rounded-md shadow-sm">
                    <button id="calendar-view-mobile" 
                            type="button"
                            class="view-toggle-btn px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-500 rounded-l-md hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-calendar mr-2"></i>Calendar
                    </button>
                    <button id="table-view-mobile" 
                            type="button"
                            class="view-toggle-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 border-l-0 transition-colors duration-200">
                        <i class="fas fa-table mr-2"></i>Table
                    </button>
                </div>
            </div>
        </div>
        
        <div id="view-description" class="sr-only">Use arrow keys to navigate between view options. Press Enter or Space to select a view.</div>
    </div>    <!-- Table View -->
    <div id="table-content" 
         role="tabpanel" 
         aria-labelledby="table-view"
         aria-hidden="true"
         class="hidden bg-white rounded-lg shadow-sm overflow-hidden">
        <h3 class="sr-only">Schedule Table View</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" role="table" aria-label="Laboratory schedule table">
                <thead class="bg-gray-50">
                    <tr role="row">
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject/Purpose</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor/User</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section/Course</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day/Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $hasData = ($currentTerm && $schedules && $schedules->count() > 0) || (isset($reservations) && $reservations->count() > 0);
                    @endphp
                    
                    @if($hasData)
                        {{-- Show recurring class schedules --}}
                        @if($currentTerm && $schedules && $schedules->count() > 0)
                            @foreach($schedules as $schedule)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $schedule->subject_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $schedule->instructor_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $schedule->section }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                        @endphp
                                        {{ $days[$schedule->day_of_week] ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ date('h:i A', strtotime($schedule->start_time)) }} - 
                                        {{ date('h:i A', strtotime($schedule->end_time)) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $schedule->type === 'regular' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            <div class="w-2 h-2 {{ $schedule->type === 'regular' ? 'bg-blue-400' : 'bg-yellow-400' }} rounded-full mr-1"></div>
                                            {{ ucfirst($schedule->type) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        {{-- Show individual reservations --}}
                        @if(isset($reservations) && $reservations->count() > 0)
                            @foreach($reservations as $reservation)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $reservation->purpose }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $reservation->user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $reservation->course_code ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('M d, Y (l)') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ date('h:i A', strtotime($reservation->start_time)) }} - 
                                        {{ date('h:i A', strtotime($reservation->end_time)) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <div class="w-2 h-2 bg-green-400 rounded-full mr-1"></div>
                                            Reservation
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @else
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                @if(!$currentTerm)
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                                        <p class="font-medium">No Active Academic Term</p>
                                        <p>Schedule information is not available without an active term.</p>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-calendar-check text-4xl text-gray-300 mb-4"></i>
                                        <p class="font-medium">No Scheduled Activities</p>
                                        <p>This laboratory has no scheduled classes or approved reservations.</p>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endif                </tbody>
            </table>
        </div>
    </div>

    <!-- Calendar View (Default) -->
    <div id="calendar-content" 
         role="tabpanel" 
         aria-labelledby="calendar-view"
         aria-hidden="false"
         class="bg-white rounded-lg shadow-sm overflow-hidden">
        <h3 class="sr-only">Schedule Calendar View</h3>        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="calendar-table min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="calendar-time-slot px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Time</th>
                            @foreach($weekData['week_dates'] as $date)
                                <th class="calendar-day-slot px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex flex-col">
                                        <span>{{ $date->format('D') }}</span>
                                        <span class="text-lg font-semibold text-gray-800 mt-1">{{ $date->format('j') }}</span>
                                        @if($date->isToday())
                                            <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mx-auto mt-1"></span>
                                        @endif
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead><tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $timeSlots = [];
                            $startTime = strtotime('07:00');
                            $endTime = strtotime('21:00');
                            while($startTime <= $endTime) {
                                $timeSlots[] = date('H:i', $startTime);
                                $startTime = strtotime('+1 hour', $startTime);
                            }
                        @endphp                        @foreach($timeSlots as $time)
                            <tr>
                                <td class="calendar-time-slot text-center align-middle border-r px-3 py-3 bg-gray-50">
                                    <div class="text-xs font-medium text-gray-700">
                                        {{ date('h:i A', strtotime($time))}}
                                    </div>
                                </td>
                                @foreach($weekData['week_dates'] as $dayIndex => $currentDate)
                                    <td class="calendar-day-slot border-r border-gray-200 p-1 h-16 overflow-hidden relative">
                                        @php
                                            $day = $dayIndex; // 0 = Sunday, 1 = Monday, etc.
                                            $hasSchedule = false;
                                            $hasReservation = false;
                                        @endphp
                                        
                                        {{-- Show recurring class schedules --}}
                                        @if($currentTerm && $schedules)
                                            @foreach($schedules as $schedule)
                                                @if($schedule->day_of_week === $day && 
                                                    strtotime($schedule->start_time) <= strtotime($time) && 
                                                    strtotime($schedule->end_time) > strtotime($time))
                                                    @php $hasSchedule = true; @endphp
                                                    <div class="schedule-block w-full h-full {{ $schedule->type === 'regular' ? 'bg-blue-100' : 'bg-yellow-100' }} border-l-4 {{ $schedule->type === 'regular' ? 'border-blue-500' : 'border-yellow-500' }} rounded-r p-1 overflow-hidden">
                                                        <div class="font-semibold text-gray-900 truncate leading-tight mb-1">{{ $schedule->subject_name }}</div>
                                                        <div class="text-gray-700 truncate leading-tight">{{ $schedule->instructor_name }}</div>
                                                        <div class="text-gray-600 truncate leading-tight">{{ $schedule->section }}</div>
                                                        <div class="text-gray-500 leading-tight mt-1">
                                                            {{ date('h:i A', strtotime($schedule->start_time)) }}-{{ date('h:i A', strtotime($schedule->end_time)) }}
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif

                                        {{-- Show individual reservations if no schedule conflict --}}
                                        @if(!$hasSchedule && isset($reservations) && $reservations->count() > 0)
                                            @foreach($reservations as $reservation)
                                                @php
                                                    $reservationDate = \Carbon\Carbon::parse($reservation->reservation_date);
                                                @endphp
                                                @if($reservationDate->isSameDay($currentDate) && 
                                                    strtotime($reservation->start_time) <= strtotime($time) && 
                                                    strtotime($reservation->end_time) > strtotime($time))
                                                    @php $hasReservation = true; @endphp
                                                    <div class="schedule-block w-full h-full bg-green-100 border-l-4 border-green-500 rounded-r p-1 overflow-hidden">
                                                        <div class="font-semibold text-gray-900 truncate leading-tight mb-1">{{ $reservation->purpose }}</div>
                                                        <div class="text-gray-700 truncate leading-tight">{{ $reservation->user->name }}</div>
                                                        @if($reservation->course_code)
                                                            <div class="text-gray-600 truncate leading-tight">{{ $reservation->course_code }}</div>
                                                        @endif
                                                        <div class="text-gray-500 leading-tight mt-1">
                                                            {{ $reservationDate->format('M d') }} • {{ date('h:i A', strtotime($reservation->start_time)) }}-{{ date('h:i A', strtotime($reservation->end_time)) }}
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

    <!-- Reservation Section -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Make a Reservation</h2>
        </div>
        <div class="p-6">
            @if(!$currentTerm)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle mr-2 mt-0.5"></i>
                        <p>Reservations are not available without an active academic term.</p>
                    </div>
                </div>
            @else
                <!-- Reservation Guidelines -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-medium text-blue-800 mb-2">Reservation Guidelines</h3>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Please check the schedule above to avoid conflicts with regular classes</li>
                        <li>• Use the Previous/Next buttons to navigate between weeks for advance planning</li>
                        <li>• Reservations must be made at least 24 hours in advance</li>
                        <li>• All reservations are subject to approval by laboratory administrators</li>
                        <li>• Keyboard shortcuts: ← → arrow keys to navigate weeks, Home key to return to current week</li>
                    </ul>
                </div>

                <div class="text-center space-y-4">
                    <a href="{{ route('ruser.laboratory.reservations.create', $laboratory) }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Create New Reservation
                    </a>
                    
                    <div class="text-sm text-gray-600">
                        or
                    </div>
                    
                    <a href="{{ route('ruser.laboratory.reservations.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-list mr-2"></i>
                        View My Reservations
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sync mobile and desktop view toggles
    const calendarViewDesktop = document.getElementById('calendar-view');
    const tableViewDesktop = document.getElementById('table-view');
    const calendarViewMobile = document.getElementById('calendar-view-mobile');
    const tableViewMobile = document.getElementById('table-view-mobile');
    
    // Sync clicks between desktop and mobile toggles
    if (calendarViewMobile) {
        calendarViewMobile.addEventListener('click', function() {
            if (calendarViewDesktop) calendarViewDesktop.click();
        });
    }
    if (tableViewMobile) {
        tableViewMobile.addEventListener('click', function() {
            if (tableViewDesktop) tableViewDesktop.click();
        });
    }
    
    // Week navigation keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Only activate shortcuts when not in input fields
        if (e.target.tagName.toLowerCase() === 'input' || 
            e.target.tagName.toLowerCase() === 'textarea' ||
            e.target.isContentEditable) {
            return;
        }
        
        switch(e.key) {
            case 'ArrowLeft':
                e.preventDefault();
                navigateWeek(-1);
                break;
            case 'ArrowRight':
                e.preventDefault();
                navigateWeek(1);
                break;
            case 'Home':
                e.preventDefault();
                navigateToCurrentWeek();
                break;
        }
    });
    
    function navigateWeek(direction) {
        const currentOffset = {!! $weekData['current_offset'] !!};
        const newOffset = currentOffset + direction;
        const baseUrl = "{!! route('ruser.laboratory.show', $laboratory) !!}";
        const url = baseUrl + (newOffset !== 0 ? "?week=" + newOffset : "");
        window.location.href = url;
    }
    
    function navigateToCurrentWeek() {
        const url = "{!! route('ruser.laboratory.show', $laboratory) !!}";
        window.location.href = url;
    }
    
    // Add loading states to navigation buttons
    const navButtons = document.querySelectorAll('a[href*="week="], a[href*="laboratory"]');
    navButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.style.opacity = '0.6';
            this.style.pointerEvents = 'none';
        });
    });
    
    // Highlight today's date
    const today = new Date();
    const todayString = today.getFullYear() + '-' + 
                       String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                       String(today.getDate()).padStart(2, '0');
    
    // Add subtle animation to schedule blocks
    const scheduleBlocks = document.querySelectorAll('.schedule-block');
    scheduleBlocks.forEach((block, index) => {
        block.style.animationDelay = (index * 0.1) + 's';
        block.classList.add('animate-fade-in');
    });
});
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}

/* Today highlight */
.today-highlight {
    background-color: rgba(59, 130, 246, 0.1);
    border-top: 2px solid #3b82f6;
}

/* Navigation button hover effects */
.nav-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
}

/* Schedule header improvements */
.schedule-header {
    background: linear-gradient(to right, #f8fafc, #f1f5f9);
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

/* Legend improvements */
.legend-item {
    background: rgba(255, 255, 255, 0.8);
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    border: 1px solid rgba(0, 0, 0, 0.1);
}

/* Mobile responsiveness for buttons */
@media (max-width: 640px) {
    .mobile-nav-button {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
}
</style>
@endpush
