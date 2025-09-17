@extends('layouts.ruser')

@section('title', 'Create Laboratory Reservation')
@section('header', 'Create Laboratory Reservation')

@section('content')
<div class="space-y-6">
    <x-flash-messages />

    <!-- Laboratory Information -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                Laboratory Information
            </h2>
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
                        <x-status-badge :status="$laboratory->status" type="laboratory" />
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation Form -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h3z" />
                </svg>
                Reservation Form
            </h2>
        </div>
        <div class="p-6">
            @if(!$currentTerm)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <p>Reservations are not available without an active academic term.</p>
                    </div>
                </div>
            @else
                <form id="reservation-form" action="{{ route('ruser.laboratory.reservations.store', $laboratory) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="laboratory_id" value="{{ $laboratory->id }}">
                    
                    <!-- Conflict Alert -->
                    <div id="conflict-alert" class="hidden mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <div>
                                <p class="font-medium">Time Conflict Detected</p>
                                <p class="text-sm">The following reservations conflict with your selected time:</p>
                                <div id="conflict-list" class="mt-2 space-y-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="purpose" class="form-label form-label-required">
                                Purpose
                            </label>
                            <textarea name="purpose" id="purpose" rows="3" required
                                class="form-textarea"
                                placeholder="Describe the purpose of this reservation">{{ old('purpose') }}</textarea>
                            <x-form-error field="purpose" />
                        </div>
                        
                        <div class="form-group">
                            <label for="reservation_date" class="form-label form-label-required">
                                Date
                            </label>
                            <input type="date" name="reservation_date" id="reservation_date" required
                                min="{{ date('Y-m-d') }}"
                                value="{{ old('reservation_date') }}"
                                class="form-input">
                            <x-form-error field="reservation_date" />
                        </div>
                        
                        <div class="form-group">
                            <label for="start_time" class="form-label form-label-required">
                                Start Time
                            </label>
                            <input type="time" name="start_time" id="start_time" required
                                value="{{ old('start_time') }}"
                                class="form-input">
                            <x-form-error field="start_time" />
                        </div>
                        
                        <div class="form-group">
                            <label for="end_time" class="form-label form-label-required">
                                End Time
                            </label>
                            <input type="time" name="end_time" id="end_time" required
                                value="{{ old('end_time') }}"
                                class="form-input">
                            <x-form-error field="end_time" />
                        </div>
                        
                        <div class="form-group">
                            <label for="num_students" class="form-label form-label-required">
                                Number of Students
                            </label>
                            <input type="number" name="num_students" id="num_students" required
                                min="1" max="{{ $laboratory->capacity }}"
                                value="{{ old('num_students') }}"
                                placeholder="Max: {{ $laboratory->capacity }}"
                                class="form-input">
                            <x-form-error field="num_students" />
                        </div>
                        
                        <div class="form-group">
                            <label for="course_code" class="form-label-optional">Course Code</label>
                            <input type="text" name="course_code" id="course_code"
                                value="{{ old('course_code') }}"
                                class="form-input">
                            <x-form-error field="course_code" />
                        </div>
                        
                        <div class="form-group">
                            <label for="subject" class="form-label-optional">Subject</label>
                            <input type="text" name="subject" id="subject"
                                value="{{ old('subject') }}"
                                class="form-input">
                            <x-form-error field="subject" />
                        </div>
                        
                        <div class="form-group">
                            <label for="section" class="form-label-optional">Section</label>
                            <input type="text" name="section" id="section"
                                value="{{ old('section') }}"
                                class="form-input">
                            <x-form-error field="section" />
                        </div>
                        
                        <div class="form-full-width">
                            <div class="form-group">
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_recurring" id="is_recurring" value="1"
                                        {{ old('is_recurring') ? 'checked' : '' }}
                                        class="form-checkbox">
                                    <label for="is_recurring" class="form-checkbox-label">
                                        Make this a recurring reservation
                                    </label>
                                </div>
                            </div>
                        </div>


                        
                        <div id="recurring-details" class="hidden md:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="recurring_pattern" class="block text-sm font-medium text-gray-700">
                                    Recurrence Pattern
                                </label>
                                <select name="recurring_pattern" id="recurring_pattern"
                                    class="mt-1 block w-full px-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="weekly" {{ old('recurring_pattern') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ old('recurring_pattern') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                                <x-form-error field="recurring_pattern" />
                            </div>
                            
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">
                                    End Date
                                </label>
                                <input type="date" name="end_date" id="end_date"
                                    min="{{ date('Y-m-d', strtotime('+1 week')) }}"
                                    value="{{ old('end_date') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <x-form-error field="end_date" />
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Always show schedule section if current term exists -->
                    @if($currentTerm)
                        <div class="mt-8" id="schedule-section">
                            <h3 class="text-base font-medium text-gray-800 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span id="schedule-title">Daily Schedule Overview</span>
                            </h3>
                            <p class="text-sm text-gray-600 mb-4" id="schedule-description">
                                Select a date to view the time slot availability and scheduled activities for that day.
                            </p>
                            
                            <!-- Color Legend -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4" id="schedule-legend">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Color Legend</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-green-100 border border-green-300 rounded mr-2"></div>
                                        <span>Available</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-blue-100 border border-blue-300 rounded mr-2"></div>
                                        <span>Regular Class</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-orange-100 border border-orange-300 rounded mr-2"></div>
                                        <span>Override/Reschedule</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-purple-100 border border-purple-300 rounded mr-2"></div>
                                        <span>Reservation</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Time Slots Container -->
                            <div id="time-slots-container" class="hidden">
                                <h6 class="text-sm font-medium text-gray-700 mb-3">Time Slots (7 AM - 9 PM):</h6>
                                <div id="time-slots-grid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 mb-4">
                                    <!-- Time slots will be populated here -->
                                </div>
                                <p class="text-xs text-gray-500">Hover over time slots for details</p>
                            </div>
                            
                            <!-- No date selected message -->
                            <div id="no-date-selected" class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h3z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Select a date to view schedule</h3>
                                <p class="mt-1 text-sm text-gray-500">Choose a reservation date to see if there are any regular classes scheduled for that day.</p>
                            </div>
                        </div>
                    @endif

                    <div class="mt-8 flex justify-end space-x-4">
                        <a href="{{ route('ruser.laboratory.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Submit Reservation
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Existing conflict checking code
    const form = document.getElementById('reservation-form');
    const dateInput = document.getElementById('reservation_date');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const conflictAlert = document.getElementById('conflict-alert');
    const conflictList = document.getElementById('conflict-list');

    // Recurring reservation toggle
    const recurringCheckbox = document.getElementById('is_recurring');
    const recurringDetails = document.getElementById('recurring-details');
    
    if (recurringCheckbox && recurringDetails) {
        recurringCheckbox.addEventListener('change', function() {
            if (this.checked) {
                recurringDetails.classList.remove('hidden');
            } else {
                recurringDetails.classList.add('hidden');
            }
        });
    }

    // Check conflicts function (existing functionality)
    function checkConflicts() {
        const date = dateInput.value;
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        
        if (date && startTime && endTime) {
            fetch(`{{ route('ruser.laboratory.conflicts.check', $laboratory) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    reservation_date: date,
                    start_time: startTime,
                    end_time: endTime
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.has_conflict) {
                    showConflictAlert(data.conflicts);
                } else {
                    hideConflictAlert();
                }
            })
            .catch(error => {
                console.error('Error checking conflicts:', error);
            });
        }
    }

    // Update schedule information based on selected date
    function updateScheduleForDate() {
        const date = dateInput.value;
        
        if (date) {
            fetch(`{{ route('ruser.laboratory.time-slots-overview', $laboratory) }}?date=${encodeURIComponent(date)}`)
            .then(response => response.json())
            .then(data => {
                updateTimeSlotsDisplay(data);
            })
            .catch(error => {
                console.error('Error fetching time slots:', error);
                showErrorMessage('Failed to load schedule information. Please try again.');
            });
        } else {
            // Reset to show no date selected message
            resetScheduleDisplay();
        }
    }

    function updateTimeSlotsDisplay(data) {
        const scheduleTitle = document.getElementById('schedule-title');
        const scheduleDescription = document.getElementById('schedule-description');
        const timeSlotsContainer = document.getElementById('time-slots-container');
        const timeSlotsGrid = document.getElementById('time-slots-grid');
        const noDateSelected = document.getElementById('no-date-selected');
        
        if (!scheduleTitle || !timeSlotsContainer) return;
        
        // Update title and description
        scheduleTitle.textContent = `Daily Schedule Overview - ${data.day_name}`;
        scheduleDescription.textContent = data.has_schedules 
            ? `Scheduled activities for ${data.day_name}. Green slots are available for reservation.`
            : `No scheduled activities for ${data.day_name}. All time slots are available for reservation.`;
        
        // Show time slots container and hide no date message
        timeSlotsContainer.classList.remove('hidden');
        noDateSelected.classList.add('hidden');
        
        // Generate time slots grid
        timeSlotsGrid.innerHTML = '';
        data.time_slots.forEach(slot => {
            const slotElement = document.createElement('div');
            let slotClass = 'px-2 py-1 text-xs border rounded text-center cursor-pointer transition-colors';
            let slotContent = slot.time.split(' - ')[0]; // Show only start hour
            let tooltip = '';
            
            if (slot.available) {
                slotClass += ' bg-green-100 text-green-800 border-green-300 hover:bg-green-200';
                tooltip = `${slot.time} - Available`;
            } else {
                const item = slot.item;
                
                switch (item.type) {
                    case 'regular':
                        slotClass += ' bg-blue-100 text-blue-800 border-blue-300 hover:bg-blue-200';
                        tooltip = `${slot.time} - Regular Class: ${item.subject_name} (${item.instructor})`;
                        break;
                    case 'override':
                        slotClass += ' bg-orange-100 text-orange-800 border-orange-300 hover:bg-orange-200';
                        tooltip = `${slot.time} - Override: ${item.subject_name} (${item.instructor})`;
                        break;
                    case 'reservation':
                        slotClass += ' bg-purple-100 text-purple-800 border-purple-300 hover:bg-purple-200';
                        tooltip = `${slot.time} - Reservation: ${item.subject_name} (${item.instructor})`;
                        break;
                    default:
                        slotClass += ' bg-gray-100 text-gray-800 border-gray-300';
                        tooltip = `${slot.time} - Occupied`;
                }
            }
            
            slotElement.className = slotClass;
            slotElement.title = tooltip;
            slotElement.textContent = slotContent;
            
            timeSlotsGrid.appendChild(slotElement);
        });
    }

    function resetScheduleDisplay() {
        const scheduleTitle = document.getElementById('schedule-title');
        const scheduleDescription = document.getElementById('schedule-description');
        const timeSlotsContainer = document.getElementById('time-slots-container');
        const noDateSelected = document.getElementById('no-date-selected');
        
        if (scheduleTitle) {
            scheduleTitle.textContent = 'Daily Schedule Overview';
        }
        if (scheduleDescription) {
            scheduleDescription.textContent = 'Select a date to view the time slot availability and scheduled activities for that day.';
        }
        if (timeSlotsContainer) {
            timeSlotsContainer.classList.add('hidden');
        }
        if (noDateSelected) {
            noDateSelected.classList.remove('hidden');
        }
    }

    function showErrorMessage(message) {
        const scheduleTitle = document.getElementById('schedule-title');
        const scheduleDescription = document.getElementById('schedule-description');
        const timeSlotsContainer = document.getElementById('time-slots-container');
        const noDateSelected = document.getElementById('no-date-selected');
        
        if (scheduleTitle) {
            scheduleTitle.textContent = 'Error Loading Schedule';
        }
        if (scheduleDescription) {
            scheduleDescription.textContent = message;
        }
        if (timeSlotsContainer) {
            timeSlotsContainer.classList.add('hidden');
        }
        if (noDateSelected) {
            noDateSelected.classList.remove('hidden');
        }
    }

    function showConflictAlert(conflicts) {
        conflictList.innerHTML = '';
        conflicts.forEach(conflict => {
            const conflictItem = document.createElement('div');
            conflictItem.className = 'text-sm bg-red-50 p-2 rounded border border-red-200';
            conflictItem.innerHTML = `
                <strong>${conflict.type}:</strong> ${conflict.description}
                <br><small class="text-red-600">${conflict.time}</small>
            `;
            conflictList.appendChild(conflictItem);
        });
        conflictAlert.classList.remove('hidden');
    }

    function hideConflictAlert() {
        conflictAlert.classList.add('hidden');
    }

    // Bind conflict checking and schedule updates to form inputs
    if (dateInput && startTimeInput && endTimeInput) {
        dateInput.addEventListener('change', function() {
            checkConflicts();
            updateScheduleForDate();
        });
        startTimeInput.addEventListener('change', checkConflicts);
        endTimeInput.addEventListener('change', checkConflicts);
    }
});
</script>
@endpush
