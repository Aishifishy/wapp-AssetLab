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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="purpose" class="block text-sm font-medium text-gray-700">
                                Purpose <span class="text-red-500">*</span>
                            </label>
                            <textarea name="purpose" id="purpose" rows="3" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Describe the purpose of this reservation">{{ old('purpose') }}</textarea>
                            <x-form-error field="purpose" />
                        </div>
                        
                        <div>
                            <label for="reservation_date" class="block text-sm font-medium text-gray-700">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="reservation_date" id="reservation_date" required
                                min="{{ date('Y-m-d') }}"
                                value="{{ old('reservation_date') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="reservation_date" />
                        </div>
                        
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">
                                Start Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="start_time" id="start_time" required
                                value="{{ old('start_time') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="start_time" />
                        </div>
                        
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700">
                                End Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="end_time" id="end_time" required
                                value="{{ old('end_time') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="end_time" />
                        </div>
                        
                        <div>
                            <label for="num_students" class="block text-sm font-medium text-gray-700">
                                Number of Students <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="num_students" id="num_students" required
                                min="1" max="{{ $laboratory->capacity }}"
                                value="{{ old('num_students') }}"
                                placeholder="Max: {{ $laboratory->capacity }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="num_students" />
                        </div>
                        
                        <div>
                            <label for="course_code" class="block text-sm font-medium text-gray-700">Course Code</label>
                            <input type="text" name="course_code" id="course_code"
                                value="{{ old('course_code') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="course_code" />
                        </div>
                        
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                            <input type="text" name="subject" id="subject"
                                value="{{ old('subject') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="subject" />
                        </div>
                        
                        <div>
                            <label for="section" class="block text-sm font-medium text-gray-700">Section</label>
                            <input type="text" name="section" id="section"
                                value="{{ old('section') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="section" />
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

                        @if($laboratory->requires_image)
                        <div class="col-span-1 md:col-span-2">
                            <label for="form_image" class="block text-sm font-medium text-gray-700 mb-2">
                                Facilities Request Form <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="form_image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload a file</span>
                                            <input id="form_image" name="form_image" type="file" class="sr-only" accept="image/*" required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF, WebP up to 10MB</p>
                                    <div id="image-preview" class="hidden mt-4">
                                        <img id="preview-image" class="mx-auto h-32 w-auto rounded-lg border border-gray-300" />
                                        <button type="button" id="remove-image" class="mt-2 text-red-600 hover:text-red-500 text-sm font-medium">Remove</button>
                                    </div>
                                    <!-- Upload progress bar -->
                                    <div id="upload-progress" class="hidden mt-4">
                                        <div class="bg-gray-200 rounded-full h-2">
                                            <div id="progress-bar" class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1">Uploading...</p>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">
                                <svg class="inline w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Please upload a clear image of the signed written form required for this laboratory reservation.
                            </p>
                            <x-form-error field="form_image" />
                        </div>
                        @endif
                        
                        <div id="recurring-details" class="hidden md:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="recurring_pattern" class="block text-sm font-medium text-gray-700">
                                    Recurrence Pattern
                                </label>
                                <select name="recurring_pattern" id="recurring_pattern"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
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
                                <span id="schedule-title">Regular Schedule Information</span>
                            </h3>
                            <p class="text-sm text-gray-600 mb-4" id="schedule-description">
                                Select a date to view the regular class schedule for that day.
                            </p>
                            
                            <div class="overflow-x-auto {{ $schedules->count() > 0 ? '' : 'hidden' }}" id="schedule-table-container">
                                <table class="min-w-full border border-gray-200 rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Day</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Time</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Subject</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Instructor</th>
                                        </tr>
                                    </thead>
                                    <tbody id="schedule-table-body" class="bg-white divide-y divide-gray-200">
                                        @if($schedules->count() > 0)
                                            @foreach($schedules as $schedule)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-2 text-sm text-gray-900 border-b">{{ $schedule->day }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900 border-b">
                                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                    </td>
                                                    <td class="px-4 py-2 text-sm text-gray-900 border-b">{{ $schedule->subject }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900 border-b">{{ $schedule->instructor }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- No schedules message -->
                            <div id="no-schedules-message" class="{{ $schedules->count() > 0 ? 'hidden' : '' }} text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Select a date to view schedule</h3>
                                <p class="mt-1 text-sm text-gray-500">Choose a reservation date to see if there are any regular classes scheduled for that day.</p>
                            </div>
                        </div>
                    @endif

                    <div class="mt-8 flex justify-end space-x-4">
                        <a href="{{ route('ruser.laboratory.reservations.index') }}" 
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
<script src="{{ asset('js/image-upload.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize image upload handler
    const imageUploadHandler = new ImageUploadHandler({
        imageInputId: 'form_image',
        imagePreviewId: 'image-preview',
        previewImageId: 'preview-image',
        removeImageBtnId: 'remove-image',
        maxFileSize: 10 * 1024 * 1024 // 10MB
    });

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
            fetch(`{{ route('ruser.laboratory.schedules.date', $laboratory) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    date: date
                })
            })
            .then(response => response.json())
            .then(data => {
                updateScheduleDisplay(data);
            })
            .catch(error => {
                console.error('Error fetching schedules:', error);
            });
        } else {
            // Reset to show all schedules when no date is selected
            resetScheduleDisplay();
        }
    }

    function updateScheduleDisplay(data) {
        const scheduleSection = document.getElementById('schedule-section');
        const scheduleTitle = document.getElementById('schedule-title');
        const scheduleDescription = document.getElementById('schedule-description');
        const scheduleTableBody = document.getElementById('schedule-table-body');
        const noSchedulesMessage = document.getElementById('no-schedules-message');
        const scheduleTableContainer = document.getElementById('schedule-table-container');
        
        if (!scheduleSection) return;
        
        // Update title and description
        scheduleTitle.textContent = `Schedule for ${data.day_name}`;
        scheduleDescription.textContent = data.has_schedules 
            ? `Regular classes scheduled for ${data.day_name}. Please check for conflicts before submitting.`
            : `No regular classes are scheduled for ${data.day_name}.`;
        
        if (data.has_schedules) {
            // Show table and hide no-schedules message
            scheduleTableContainer.classList.remove('hidden');
            noSchedulesMessage.classList.add('hidden');
            
            // Update table content
            scheduleTableBody.innerHTML = '';
            data.schedules.forEach(schedule => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-4 py-2 text-sm text-gray-900 border-b">${schedule.day}</td>
                    <td class="px-4 py-2 text-sm text-gray-900 border-b">${schedule.start_time} - ${schedule.end_time}</td>
                    <td class="px-4 py-2 text-sm text-gray-900 border-b">${schedule.subject}</td>
                    <td class="px-4 py-2 text-sm text-gray-900 border-b">${schedule.instructor}</td>
                `;
                scheduleTableBody.appendChild(row);
            });
        } else {
            // Hide table and show no-schedules message
            scheduleTableContainer.classList.add('hidden');
            noSchedulesMessage.classList.remove('hidden');
        }
    }

    function resetScheduleDisplay() {
        const scheduleTitle = document.getElementById('schedule-title');
        const scheduleDescription = document.getElementById('schedule-description');
        
        if (scheduleTitle) {
            scheduleTitle.textContent = 'Regular Schedule Information';
        }
        if (scheduleDescription) {
            scheduleDescription.textContent = 'This laboratory has regular classes scheduled. Please check for conflicts before submitting.';
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
