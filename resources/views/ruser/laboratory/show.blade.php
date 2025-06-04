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
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
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

    <!-- Schedule View Type Toggle -->
    <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Weekly Schedule</h2>
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2 text-xs">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-100 border border-blue-200 rounded mr-1"></div>
                    <span>Regular Class</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-yellow-100 border border-yellow-200 rounded mr-1"></div>
                    <span>Special Class</span>
                </div>
            </div>            <div class="flex items-center space-x-2">
                <span class="text-sm font-medium text-gray-700" id="view-label">View:</span>
                <div class="flex rounded-md shadow-sm" role="group" aria-labelledby="view-label" aria-describedby="view-description">                    <button id="calendar-view" 
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
                <div id="view-description" class="sr-only">Use arrow keys to navigate between view options. Press Enter or Space to select a view.                </div>
            </div>
        </div>
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
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
                                        <p class="font-medium">No Scheduled Classes</p>
                                        <p>This laboratory has no scheduled classes for the current term.</p>
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
                            @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                <th class="calendar-day-slot px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $day }}</th>
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
                                @for($day = 0; $day <= 6; $day++)
                                    <td class="calendar-day-slot border-r border-gray-200 p-1 h-16 overflow-hidden relative">
                                        @if($currentTerm && $schedules)
                                            @foreach($schedules as $schedule)
                                                @if($schedule->day_of_week === $day && 
                                                    strtotime($schedule->start_time) <= strtotime($time) && 
                                                    strtotime($schedule->end_time) > strtotime($time))
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
                                    </td>
                                @endfor
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
                        <li>• Reservations must be made at least 24 hours in advance</li>
                        <li>• All reservations are subject to approval by laboratory administrators</li>
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
