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
                        <li>• Please check the schedule below to avoid conflicts with regular classes & reservations</li>
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
        
        <!-- Table Controls and Summary -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-4">
                    <h4 class="text-sm font-medium text-gray-900">Schedule Details</h4>
                    @php
                        $totalSchedules = ($currentTerm && $schedules) ? $schedules->count() : 0;
                        $totalReservations = isset($reservations) ? $reservations->count() : 0;
                    @endphp
                    <span class="text-xs text-gray-500">
                        {{ $totalSchedules }} regular classes • {{ $totalReservations }} reservations
                    </span>
                </div>
                <div class="mt-2 sm:mt-0 text-xs text-gray-500">
                    Week of {{ $weekData['selected_week_start']->format('M d') }} - {{ $weekData['selected_week_end']->format('M d, Y') }}
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 md:table mobile-table-stack" role="table" aria-label="Laboratory schedule table">
                <thead class="bg-gray-50">
                    <tr role="row">
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject/Purpose</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor/User</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hide-on-mobile">Course/Section</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day/Date</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hide-on-mobile">Type</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hide-on-mobile">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $allEntries = collect();
                        $weekStart = $weekData['selected_week_start'];
                        $weekEnd = $weekData['selected_week_end'];
                        
                        // Add regular schedules for this week
                        if($currentTerm && $schedules && $schedules->count() > 0) {
                            foreach($schedules as $schedule) {
                                // Calculate actual dates for this week
                                $dayOfWeek = $schedule->day_of_week;
                                $scheduleDate = $weekStart->copy()->addDays($dayOfWeek);
                                
                                $allEntries->push([
                                    'type' => 'schedule',
                                    'subject' => $schedule->subject_name,
                                    'instructor' => $schedule->instructor_name,
                                    'course' => $schedule->section,
                                    'date' => $scheduleDate,
                                    'start_time' => $schedule->start_time,
                                    'end_time' => $schedule->end_time,
                                    'schedule_type' => $schedule->type,
                                    'sort_key' => $scheduleDate->format('N') . '-' . $schedule->start_time
                                ]);
                            }
                        }
                        
                        // Add reservations for this week
                        if(isset($reservations) && $reservations->count() > 0) {
                            foreach($reservations as $reservation) {
                                $reservationDate = \Carbon\Carbon::parse($reservation->reservation_date);
                                if($reservationDate->between($weekStart, $weekEnd)) {
                                    $allEntries->push([
                                        'type' => 'reservation',
                                        'subject' => $reservation->purpose,
                                        'instructor' => $reservation->user->name,
                                        'course' => $reservation->course_code ?? 'N/A',
                                        'date' => $reservationDate,
                                        'start_time' => $reservation->start_time,
                                        'end_time' => $reservation->end_time,
                                        'schedule_type' => 'reservation',
                                        'status' => $reservation->status,
                                        'sort_key' => $reservationDate->format('N') . '-' . $reservation->start_time
                                    ]);
                                }
                            }
                        }
                        
                        // Sort entries by day and time
                        $sortedEntries = $allEntries->sortBy('sort_key');
                    @endphp
                    
                    @if($sortedEntries->count() > 0)
                        @foreach($sortedEntries as $entry)
                            <tr class="hover:bg-gray-50 transition-colors duration-150 table-view-row">
                                <td class="px-4 py-4 whitespace-nowrap" data-label="Subject">
                                    <div class="text-sm font-medium text-gray-900">{{ $entry['subject'] }}</div>
                                    @if($entry['type'] === 'reservation' && isset($entry['status']))
                                        <div class="text-xs text-gray-500 mt-1 md:hidden">
                                            <x-status-badge :status="$entry['status']" type="reservation" />
                                        </div>
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
                                <td class="px-4 py-4 whitespace-nowrap hide-on-mobile" data-label="Status">
                                    @if($entry['type'] === 'reservation' && isset($entry['status']))
                                        <x-status-badge :status="$entry['status']" type="reservation" />
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <div class="w-2 h-2 bg-green-400 rounded-full mr-1.5"></div>
                                            Active
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center empty-state">
                                @if(!$currentTerm)
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                                        <h3 class="text-sm font-medium text-gray-900 mb-2">No Active Academic Term</h3>
                                        <p class="text-sm text-gray-500">Schedule information is not available without an active term.</p>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-calendar-check text-4xl text-gray-300 mb-4"></i>
                                        <h3 class="text-sm font-medium text-gray-900 mb-2">No Scheduled Activities</h3>
                                        <p class="text-sm text-gray-500">This laboratory has no scheduled classes or approved reservations for this week.</p>
                                        <div class="mt-4">
                                            <a href="{{ route('ruser.laboratory.reservations.create', $laboratory) }}" 
                                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-plus mr-2"></i>
                                                Create First Reservation
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Table View Footer -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-xs text-gray-500">
                <div class="flex items-center space-x-4">
                    <span>Total entries: {{ $sortedEntries->count() }}</span>
                    <span>•</span>
                    <span>Showing week of {{ $weekData['selected_week_start']->format('M d') }}</span>
                </div>
                <div class="mt-2 sm:mt-0">
                    <span>Use Previous/Next buttons to navigate weeks</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar View (Default) -->
    <div id="calendar-content" 
         role="tabpanel" 
         aria-labelledby="calendar-view"
         aria-hidden="false"
         class="bg-white rounded-lg shadow-sm overflow-hidden">
        <h3 class="sr-only">Schedule Calendar View</h3>        
        <div class="p-3 md:p-6">
            <!-- Mobile Calendar Notice -->
            <div class="md:hidden bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-2"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Mobile Calendar View</p>
                        <p class="text-xs">Scroll horizontally to view all days • Tap and hold for details • Switch to Table view for better mobile experience</p>
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
                                        @if($currentTerm && $schedules)
                                            @foreach($schedules as $schedule)
                                                @if($schedule->day_of_week === $day && 
                                                    strtotime($schedule->start_time) <= strtotime($time) && 
                                                    strtotime($schedule->end_time) > strtotime($time))
                                                    @php $hasSchedule = true; @endphp
                                                    <div class="schedule-block w-full h-full {{ $schedule->type === 'regular' ? 'bg-blue-100' : 'bg-yellow-100' }} border-l-4 {{ $schedule->type === 'regular' ? 'border-blue-500' : 'border-yellow-500' }} rounded-r overflow-hidden" 
                                                         style="padding: 2px; font-size: 0.6rem; line-height: 1.1;"
                                                         title="{{ $schedule->subject_name }} - {{ $schedule->instructor_name }} ({{ $schedule->section }}) {{ date('h:i A', strtotime($schedule->start_time)) }}-{{ date('h:i A', strtotime($schedule->end_time)) }}">
                                                        <div class="font-semibold text-gray-900 truncate">{{ $schedule->subject_name }}</div>
                                                        <div class="text-gray-700 truncate">{{ $schedule->instructor_name }}</div>
                                                        <div class="text-gray-600 truncate">{{ $schedule->section }}</div>
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
                                                    <div class="schedule-block w-full h-full bg-green-100 border-l-4 border-green-500 rounded-r overflow-hidden" 
                                                         style="padding: 2px; font-size: 0.6rem; line-height: 1.1;"
                                                         title="{{ $reservation->purpose }} - {{ $reservation->user->name }} {{ $reservationDate->format('M d') }} {{ date('h:i A', strtotime($reservation->start_time)) }}-{{ date('h:i A', strtotime($reservation->end_time)) }}">
                                                        <div class="font-semibold text-gray-900 truncate">{{ $reservation->purpose }}</div>
                                                        <div class="text-gray-700 truncate">{{ $reservation->user->name }}</div>
                                                        @if($reservation->course_code)
                                                            <div class="text-gray-600 truncate">{{ $reservation->course_code }}</div>
                                                        @endif
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
            </div> <!-- Close calendar-scroll-container -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // View toggle functionality
    const calendarViewDesktop = document.getElementById('calendar-view');
    const tableViewDesktop = document.getElementById('table-view');
    const calendarViewMobile = document.getElementById('calendar-view-mobile');
    const tableViewMobile = document.getElementById('table-view-mobile');
    
    const calendarContent = document.getElementById('calendar-content');
    const tableContent = document.getElementById('table-content');
    
    // Function to switch to calendar view
    function switchToCalendarView() {
        // Update button states - Desktop
        if (calendarViewDesktop && tableViewDesktop) {
            calendarViewDesktop.classList.remove('text-gray-700', 'bg-white', 'border-gray-300');
            calendarViewDesktop.classList.add('text-blue-700', 'bg-blue-50', 'border-blue-500');
            calendarViewDesktop.setAttribute('aria-pressed', 'true');
            calendarViewDesktop.setAttribute('aria-selected', 'true');
            calendarViewDesktop.setAttribute('tabindex', '0');
            
            tableViewDesktop.classList.remove('text-blue-700', 'bg-blue-50', 'border-blue-500');
            tableViewDesktop.classList.add('text-gray-700', 'bg-white', 'border-gray-300');
            tableViewDesktop.setAttribute('aria-pressed', 'false');
            tableViewDesktop.setAttribute('aria-selected', 'false');
            tableViewDesktop.setAttribute('tabindex', '-1');
        }
        
        // Update button states - Mobile
        if (calendarViewMobile && tableViewMobile) {
            calendarViewMobile.classList.remove('text-gray-700', 'bg-white', 'border-gray-300');
            calendarViewMobile.classList.add('text-blue-700', 'bg-blue-50', 'border-blue-500');
            
            tableViewMobile.classList.remove('text-blue-700', 'bg-blue-50', 'border-blue-500');
            tableViewMobile.classList.add('text-gray-700', 'bg-white', 'border-gray-300');
        }
        
        // Show/hide content
        if (calendarContent && tableContent) {
            calendarContent.classList.remove('hidden');
            calendarContent.setAttribute('aria-hidden', 'false');
            
            tableContent.classList.add('hidden');
            tableContent.setAttribute('aria-hidden', 'true');
        }
    }
    
    // Function to switch to table view
    function switchToTableView() {
        // Update button states - Desktop
        if (calendarViewDesktop && tableViewDesktop) {
            tableViewDesktop.classList.remove('text-gray-700', 'bg-white', 'border-gray-300');
            tableViewDesktop.classList.add('text-blue-700', 'bg-blue-50', 'border-blue-500');
            tableViewDesktop.setAttribute('aria-pressed', 'true');
            tableViewDesktop.setAttribute('aria-selected', 'true');
            tableViewDesktop.setAttribute('tabindex', '0');
            
            calendarViewDesktop.classList.remove('text-blue-700', 'bg-blue-50', 'border-blue-500');
            calendarViewDesktop.classList.add('text-gray-700', 'bg-white', 'border-gray-300');
            calendarViewDesktop.setAttribute('aria-pressed', 'false');
            calendarViewDesktop.setAttribute('aria-selected', 'false');
            calendarViewDesktop.setAttribute('tabindex', '-1');
        }
        
        // Update button states - Mobile
        if (calendarViewMobile && tableViewMobile) {
            tableViewMobile.classList.remove('text-gray-700', 'bg-white', 'border-gray-300');
            tableViewMobile.classList.add('text-blue-700', 'bg-blue-50', 'border-blue-500');
            
            calendarViewMobile.classList.remove('text-blue-700', 'bg-blue-50', 'border-blue-500');
            calendarViewMobile.classList.add('text-gray-700', 'bg-white', 'border-gray-300');
        }
        
        // Show/hide content
        if (calendarContent && tableContent) {
            tableContent.classList.remove('hidden');
            tableContent.setAttribute('aria-hidden', 'false');
            
            calendarContent.classList.add('hidden');
            calendarContent.setAttribute('aria-hidden', 'true');
        }
    }
    
    // Add event listeners
    if (calendarViewDesktop) {
        calendarViewDesktop.addEventListener('click', switchToCalendarView);
    }
    if (tableViewDesktop) {
        tableViewDesktop.addEventListener('click', switchToTableView);
    }
    if (calendarViewMobile) {
        calendarViewMobile.addEventListener('click', switchToCalendarView);
    }
    if (tableViewMobile) {
        tableViewMobile.addEventListener('click', switchToTableView);
    }
    
    // Keyboard navigation for desktop buttons
    if (calendarViewDesktop && tableViewDesktop) {
        [calendarViewDesktop, tableViewDesktop].forEach(button => {
            button.addEventListener('keydown', function(e) {
                switch(e.key) {
                    case 'ArrowRight':
                    case 'ArrowLeft':
                        e.preventDefault();
                        const nextButton = button === calendarViewDesktop ? tableViewDesktop : calendarViewDesktop;
                        nextButton.focus();
                        nextButton.click();
                        break;
                    case 'Enter':
                    case ' ':
                        e.preventDefault();
                        button.click();
                        break;
                }
            });
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
                // Only navigate weeks if not in view toggle buttons
                if (!e.target.classList.contains('view-toggle-btn')) {
                    e.preventDefault();
                    navigateWeek(-1);
                }
                break;
            case 'ArrowRight':
                // Only navigate weeks if not in view toggle buttons
                if (!e.target.classList.contains('view-toggle-btn')) {
                    e.preventDefault();
                    navigateWeek(1);
                }
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

/* View toggle enhancements */
.view-toggle-btn {
    transition: all 0.3s ease-in-out;
    position: relative;
    overflow: hidden;
}

.view-toggle-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.5s;
}

.view-toggle-btn:hover::before {
    left: 100%;
}

.view-toggle-btn:focus {
    outline: 2px solid #3B82F6;
    outline-offset: 2px;
    transform: scale(1.02);
}

/* Calendar specific styles */
.calendar-scroll-container {
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.calendar-scroll-container::-webkit-scrollbar {
    height: 8px;
}

.calendar-scroll-container::-webkit-scrollbar-track {
    background: #f7fafc;
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
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 10;
}

/* Table view enhancements */
.table-view-row {
    transition: background-color 0.2s ease, transform 0.1s ease;
}

.table-view-row:hover {
    background-color: rgba(59, 130, 246, 0.05);
    transform: translateX(2px);
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
    transition: background-color 0.2s ease;
}

.legend-item:hover {
    background: rgba(255, 255, 255, 0.95);
}

/* Responsive enhancements */
@media (max-width: 768px) {
    /* Mobile Calendar Optimizations */
    .calendar-table {
        font-size: 0.65rem;
        min-width: 100%;
    }
    
    .calendar-time-slot {
        width: 50px;
        min-width: 50px;
        padding: 0.25rem;
        font-size: 0.6rem;
    }
    
    .calendar-day-slot {
        width: calc((100% - 50px) / 7);
        min-width: 45px;
        padding: 0.1rem;
        font-size: 0.6rem;
    }
    
    .schedule-block {
        font-size: 0.5rem;
        padding: 0.1rem;
        line-height: 1.1;
        min-height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .schedule-block div {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /* Mobile-specific calendar header */
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
    
    /* Reduce calendar row height for mobile */
    .calendar-table td {
        height: 40px;
        min-height: 40px;
    }
    
    /* Mobile navigation improvements */
    .mobile-nav-button {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
    
    /* Add mobile calendar alternative view */
    .mobile-calendar-alternative {
        display: block;
    }
    
    .desktop-calendar {
        display: none;
    }
    
    /* Stack table cells on mobile */
    .mobile-table-stack {
        display: block;
        width: 100%;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 1rem;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .mobile-table-stack thead {
        display: none;
    }
    
    .mobile-table-stack tr {
        display: block;
        border-bottom: none;
        padding: 1rem;
        background: white;
        margin-bottom: 0.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .mobile-table-stack td {
        display: block;
        border: none;
        padding: 0.25rem 0;
        text-align: left !important;
    }
    
    .mobile-table-stack td:before {
        content: attr(data-label) ": ";
        font-weight: 600;
        color: #374151;
        display: inline-block;
        width: 120px;
        flex-shrink: 0;
    }
    
    /* Mobile view toggle improvements */
    .view-toggle-btn {
        padding: 0.4rem 0.6rem;
        font-size: 0.8rem;
    }
    
    /* Mobile calendar tooltip alternative */
    .mobile-schedule-tooltip {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.7rem;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s;
        z-index: 1000;
    }
    
    .schedule-block:active .mobile-schedule-tooltip {
        opacity: 1;
    }
}

@media (max-width: 640px) {
    /* Extra small screens - further optimize */
    .hide-on-mobile {
        display: none;
    }
    
    .view-toggle-btn {
        padding: 0.4rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .calendar-time-slot {
        width: 40px;
        min-width: 40px;
        font-size: 0.55rem;
    }
    
    .calendar-day-slot {
        width: calc((100% - 40px) / 7);
        min-width: 35px;
    }
    
    .schedule-block {
        font-size: 0.45rem;
        padding: 0.05rem;
    }
    
    /* Show only essential info in schedule blocks */
    .schedule-block .text-gray-700,
    .schedule-block .text-gray-600,
    .schedule-block .text-gray-500 {
        display: none;
    }
    
    .schedule-block .font-semibold {
        font-size: 0.5rem;
        line-height: 1;
    }
    
    /* Mobile calendar scroll optimization */
    .calendar-container {
        overflow-x: auto;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }
    
    .calendar-container::-webkit-scrollbar {
        height: 4px;
    }
    
    .calendar-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 2px;
    }
    
    .calendar-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 2px;
    }
    
    .calendar-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
}

/* Add desktop-specific calendar class */
@media (min-width: 769px) {
    .mobile-calendar-alternative {
        display: none;
    }
    
    .desktop-calendar {
        display: block;
    }
}

/* Loading animation for navigation */
.loading {
    opacity: 0.6;
    pointer-events: none;
    position: relative;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 16px;
    height: 16px;
    margin: -8px 0 0 -8px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Focus improvements for accessibility */
.focus-visible:focus {
    outline: 2px solid #3B82F6;
    outline-offset: 2px;
    border-radius: 0.25rem;
}

/* Status badge improvements */
.status-badge {
    display: inline-flex;
    align-items: center;
    font-weight: 500;
    transition: transform 0.1s ease;
}

.status-badge:hover {
    transform: scale(1.05);
}

/* Smooth transitions for content switching */
#calendar-content,
#table-content {
    transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
}

#calendar-content.hidden,
#table-content.hidden {
    opacity: 0;
    transform: translateY(-10px);
}

/* Improved empty state */
.empty-state {
    padding: 3rem 1rem;
    text-align: center;
}

.empty-state i {
    color: #9CA3AF;
    margin-bottom: 1rem;
}

.empty-state h3 {
    color: #374151;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #6B7280;
    margin-bottom: 1rem;
}
</style>
@endpush
