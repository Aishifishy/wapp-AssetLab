@extends('layouts.ruser')

@section('title', 'Create Laboratory Reservation')
@section('header', 'Create Laboratory Reservation')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

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

    <!-- Laboratory Information -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Laboratory Information</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Laboratory</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $laboratory->name }}</p>
                    <p class="text-sm text-gray-600">{{ $laboratory->building }}, Room {{ $laboratory->room_number }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Capacity</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $laboratory->capacity }} seats</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Computers</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $laboratory->number_of_computers }} units</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Status</h3>
                    <p class="mt-1">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $laboratory->status === 'available' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $laboratory->status === 'in_use' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $laboratory->status === 'under_maintenance' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $laboratory->status === 'reserved' ? 'bg-blue-100 text-blue-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $laboratory->status)) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation Form -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Reservation Form</h2>
        </div>
        <div class="p-6">
            @if(!$currentTerm)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                    <p>Reservations are not available without an active academic term.</p>
                </div>
            @else
                <form action="{{ route('ruser.laboratory.reservations.store', $laboratory) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose <span class="text-red-500">*</span></label>
                            <textarea name="purpose" id="purpose" rows="3" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="reservation_date" class="block text-sm font-medium text-gray-700">Date <span class="text-red-500">*</span></label>
                            <input type="date" name="reservation_date" id="reservation_date" required
                                min="{{ date('Y-m-d') }}"
                                value="{{ old('reservation_date') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('reservation_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time <span class="text-red-500">*</span></label>
                            <input type="time" name="start_time" id="start_time" required
                                value="{{ old('start_time') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('start_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700">End Time <span class="text-red-500">*</span></label>
                            <input type="time" name="end_time" id="end_time" required
                                value="{{ old('end_time') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('end_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="num_students" class="block text-sm font-medium text-gray-700">Number of Students <span class="text-red-500">*</span></label>
                            <input type="number" name="num_students" id="num_students" required
                                min="1" max="{{ $laboratory->capacity }}"
                                value="{{ old('num_students') }}"
                                placeholder="Max: {{ $laboratory->capacity }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('num_students')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="course_code" class="block text-sm font-medium text-gray-700">Course Code</label>
                            <input type="text" name="course_code" id="course_code"
                                value="{{ old('course_code') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('course_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                            <input type="text" name="subject" id="subject"
                                value="{{ old('subject') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('subject')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="section" class="block text-sm font-medium text-gray-700">Section</label>
                            <input type="text" name="section" id="section"
                                value="{{ old('section') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('section')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                          <div class="col-span-1 md:col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_recurring" id="is_recurring" value="1"
                                    {{ old('is_recurring') ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_recurring" class="ml-2 block text-sm text-gray-700">
                                    Make this a recurring reservation
                                </label>
                            </div>
                        </div>
                        
                        <div class="recurrence-options hidden md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="recurrence_pattern" class="block text-sm font-medium text-gray-700">Recurrence Pattern</label>
                                <select name="recurrence_pattern" id="recurrence_pattern"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="weekly" {{ old('recurrence_pattern') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ old('recurrence_pattern') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                                @error('recurrence_pattern')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="recurrence_end_date" class="block text-sm font-medium text-gray-700">Recurrence End Date</label>
                                <input type="date" name="recurrence_end_date" id="recurrence_end_date"
                                    min="{{ date('Y-m-d', strtotime('+1 week')) }}"
                                    value="{{ old('recurrence_end_date') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                @error('recurrence_end_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h3 class="text-base font-medium text-gray-800 mb-2">Existing Schedule Information</h3>
                        <p class="text-sm text-gray-600 mb-4">This laboratory may have regular classes scheduled. Please check the calendar before submitting your reservation.</p>
                        
                        @if($schedules->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full border">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Day</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Time</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Subject</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Section</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Instructor</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($schedules as $schedule)
                                        <tr>
                                            <td class="px-4 py-2 border">
                                                @php
                                                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                                @endphp
                                                {{ $days[$schedule->day_of_week] }}
                                            </td>
                                            <td class="px-4 py-2 border">
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                            </td>
                                            <td class="px-4 py-2 border">
                                                {{ $schedule->subject_code }} - {{ $schedule->subject_name }}
                                            </td>
                                            <td class="px-4 py-2 border">
                                                {{ $schedule->section }}
                                            </td>
                                            <td class="px-4 py-2 border">
                                                {{ $schedule->instructor_name }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-600">No regular schedules found for this laboratory.</p>
                        @endif
                    </div>
                    
                    <div class="mt-6">
                        <h3 class="text-base font-medium text-gray-800 mb-2">Existing Reservations</h3>
                        @if($existingReservations->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full border">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Time</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($existingReservations as $existingReservation)
                                        <tr>
                                            <td class="px-4 py-2 border">
                                                {{ $existingReservation->reservation_date->format('M d, Y (D)') }}
                                            </td>
                                            <td class="px-4 py-2 border">
                                                {{ \Carbon\Carbon::parse($existingReservation->start_time)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($existingReservation->end_time)->format('H:i') }}
                                            </td>
                                            <td class="px-4 py-2 border">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    bg-blue-100 text-blue-800">
                                                    Reserved
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-600">No existing reservations for the next 14 days.</p>
                        @endif
                    </div>

                    <div class="mt-8 flex justify-between">
                        <a href="{{ route('ruser.laboratory.show', $laboratory) }}" 
                           class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Submit Reservation
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isRecurringCheckbox = document.getElementById('is_recurring');
        const recurrenceOptions = document.querySelector('.recurrence-options');
        const dateInput = document.getElementById('reservation_date');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const recurrencePatternSelect = document.getElementById('recurrence_pattern');
        const recurrenceEndDateInput = document.getElementById('recurrence_end_date');
        const laboratoryId = {{ $laboratory->id }};
        
        // Status indicators
        const conflictStatus = document.createElement('div');
        conflictStatus.id = 'conflict-status';
        conflictStatus.className = 'mt-2';
        endTimeInput.parentNode.appendChild(conflictStatus);
        
        // For recurring reservations
        const recurringConflictStatus = document.createElement('div');
        recurringConflictStatus.id = 'recurring-conflict-status';
        recurringConflictStatus.className = 'mt-2';
        if (recurrenceEndDateInput) {
            recurrenceEndDateInput.parentNode.appendChild(recurringConflictStatus);
        }
        
        function toggleRecurrenceOptions() {
            if (isRecurringCheckbox.checked) {
                recurrenceOptions.classList.remove('hidden');
            } else {
                recurrenceOptions.classList.add('hidden');
            }
        }
        
        function checkConflict() {
            const date = dateInput.value;
            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;
            
            if (!date || !startTime || !endTime) {
                conflictStatus.innerHTML = '';
                return;
            }
            
            conflictStatus.innerHTML = '<span class="text-blue-500">Checking for conflicts...</span>';
            
            fetch('/api/reservation/check-conflict', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    laboratory_id: laboratoryId,
                    date: date,
                    start_time: startTime,
                    end_time: endTime
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.has_conflict) {
                    conflictStatus.innerHTML = `
                        <span class="text-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            ${data.message}
                        </span>
                    `;
                } else {
                    conflictStatus.innerHTML = `
                        <span class="text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            The selected time is available
                        </span>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                conflictStatus.innerHTML = '<span class="text-red-600">Error checking for conflicts</span>';
            });
        }
          function checkRecurringConflicts() {
            const startDate = dateInput.value;
            const endDate = recurrenceEndDateInput.value;
            const pattern = recurrencePatternSelect.value;
            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;
            
            if (!startDate || !endDate || !pattern || !startTime || !endTime || !isRecurringCheckbox.checked) {
                recurringConflictStatus.innerHTML = '';
                return;
            }
            
            recurringConflictStatus.innerHTML = '<span class="text-blue-500">Checking for recurring conflicts...</span>';
            
            fetch('/api/reservation/check-recurring-conflicts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    laboratory_id: laboratoryId,
                    start_date: startDate,
                    end_date: endDate,
                    recurrence_pattern: pattern,
                    start_time: startTime,
                    end_time: endTime
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.has_conflicts) {
                    // Group conflicts by type
                    const conflictsByType = {
                        single_reservation: [],
                        recurring_reservation: [],
                        class_schedule: []
                    };
                    
                    data.conflicts.forEach(conflict => {
                        const date = new Date(conflict.date);
                        const formattedDate = date.toLocaleDateString();
                        
                        if (conflict.conflict_type) {
                            if (!conflictsByType[conflict.conflict_type]) {
                                conflictsByType[conflict.conflict_type] = [];
                            }
                            conflictsByType[conflict.conflict_type].push(formattedDate);
                        }
                    });
                    
                    // Create a more detailed message
                    let conflictMessage = `<div class="text-red-600 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        Found ${data.conflict_count} conflict${data.conflict_count !== 1 ? 's' : ''} in your recurring reservation:
                    </div>`;
                    
                    if (conflictsByType.class_schedule.length > 0) {
                        conflictMessage += `<div class="text-red-600 ml-6 mb-1">• ${conflictsByType.class_schedule.length} conflict(s) with regular class schedules</div>`;
                    }
                    
                    if (conflictsByType.single_reservation.length > 0) {
                        conflictMessage += `<div class="text-red-600 ml-6 mb-1">• ${conflictsByType.single_reservation.length} conflict(s) with existing reservations</div>`;
                    }
                    
                    if (conflictsByType.recurring_reservation.length > 0) {
                        conflictMessage += `<div class="text-red-600 ml-6 mb-1">• ${conflictsByType.recurring_reservation.length} conflict(s) with recurring reservations</div>`;
                    }
                    
                    // Add academic term warning if approaching term end
                    const startDateObj = new Date(startDate);
                    const endDateObj = new Date(endDate);
                    const termEndDate = new Date('{{ $currentTerm ? $currentTerm->end_date : "" }}');
                    
                    if (termEndDate && endDateObj > termEndDate) {
                        conflictMessage += `<div class="text-yellow-600 mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Warning: Your recurring reservation extends beyond the current academic term (ends on ${termEndDate.toLocaleDateString()})
                        </div>`;
                    }
                    
                    recurringConflictStatus.innerHTML = conflictMessage;
                } else {
                    // Check for academic term boundary warning
                    const endDateObj = new Date(endDate);
                    const termEndDate = new Date('{{ $currentTerm ? $currentTerm->end_date : "" }}');
                    
                    let message = `
                        <span class="text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            All recurring dates are available
                        </span>
                    `;
                    
                    if (termEndDate && endDateObj > termEndDate) {
                        message += `<div class="text-yellow-600 mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Warning: Your recurring reservation extends beyond the current academic term (ends on ${termEndDate.toLocaleDateString()})
                        </div>`;
                    }
                    
                    recurringConflictStatus.innerHTML = message;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                recurringConflictStatus.innerHTML = '<span class="text-red-600">Error checking for recurring conflicts</span>';
            });
        }
        
        // Initialize on page load
        toggleRecurrenceOptions();
        
        // Add event listeners for form changes
        isRecurringCheckbox.addEventListener('change', toggleRecurrenceOptions);
        
        // Add debounced conflict checking
        let timeout;
        const debouncedCheck = function() {
            clearTimeout(timeout);
            timeout = setTimeout(checkConflict, 500);
        };
        
        let recurringTimeout;
        const debouncedRecurringCheck = function() {
            clearTimeout(recurringTimeout);
            recurringTimeout = setTimeout(checkRecurringConflicts, 500);
        };
        
        // Setup event listeners
        dateInput.addEventListener('change', debouncedCheck);
        startTimeInput.addEventListener('change', debouncedCheck);
        endTimeInput.addEventListener('change', debouncedCheck);
        
        if (recurrencePatternSelect && recurrenceEndDateInput) {
            dateInput.addEventListener('change', debouncedRecurringCheck);
            startTimeInput.addEventListener('change', debouncedRecurringCheck);
            endTimeInput.addEventListener('change', debouncedRecurringCheck);
            recurrencePatternSelect.addEventListener('change', debouncedRecurringCheck);
            recurrenceEndDateInput.addEventListener('change', debouncedRecurringCheck);
            isRecurringCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    debouncedRecurringCheck();
                } else {
                    recurringConflictStatus.innerHTML = '';
                }
            });
        }
    });
</script>
@endpush
@endsection
