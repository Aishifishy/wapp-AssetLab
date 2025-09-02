@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-content-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Create Schedule Override</h1>
        <a href="{{ route('admin.laboratory.reservations') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            <i class="fas fa-arrow-left mr-2"></i> Back to Reservations
        </a>
    </div>

    <x-flash-messages />

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Override Details</h3>
            <p class="mt-1 text-sm text-gray-600">Create a one-time modification to a regular class schedule.</p>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.laboratory.store-override') }}" method="POST" id="override-form">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div>
                            <label for="laboratory_id" class="block text-sm font-medium text-gray-700">Laboratory</label>
                            <select name="laboratory_id" 
                                    id="laboratory_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    required>
                                <option value="">Select Laboratory</option>
                                @foreach($laboratories as $lab)
                                    <option value="{{ $lab->id }}" 
                                            {{ old('laboratory_id', $selectedLaboratory) == $lab->id ? 'selected' : '' }}>
                                        {{ $lab->name }} ({{ $lab->building }} - {{ $lab->room_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('laboratory_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="override_date" class="block text-sm font-medium text-gray-700">Override Date</label>
                            <input type="date" 
                                   name="override_date" 
                                   id="override_date" 
                                   value="{{ old('override_date', $selectedDate) }}"
                                   min="{{ now()->toDateString() }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   required>
                            @error('override_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="override_type" class="block text-sm font-medium text-gray-700">Override Type</label>
                            <select name="override_type" 
                                    id="override_type" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    required>
                                <option value="">Select Override Type</option>
                                <option value="cancel" {{ old('override_type') === 'cancel' ? 'selected' : '' }}>Cancel - Remove the class for this date</option>
                                <option value="reschedule" {{ old('override_type') === 'reschedule' ? 'selected' : '' }}>Reschedule - Change the time only</option>
                                <option value="replace" {{ old('override_type') === 'replace' ? 'selected' : '' }}>Replace - Change class details completely</option>
                            </select>
                            @error('override_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Override</label>
                            <textarea name="reason" 
                                      id="reason" 
                                      rows="3"
                                      placeholder="Explain why this override is needed..."
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                      required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700">Expiration Date (Optional)</label>
                            <input type="datetime-local" 
                                   name="expires_at" 
                                   id="expires_at" 
                                   value="{{ old('expires_at') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">If set, the override will automatically deactivate after this date/time</p>
                            @error('expires_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Original Schedule Information -->
                        <div id="original-schedule-info" class="bg-gray-50 rounded-lg p-4" style="display: none;">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Original Schedule for Selected Date</h4>
                            <div id="schedules-list"></div>
                        </div>

                        <!-- New Schedule Details (shown for reschedule/replace) -->
                        <div id="new-schedule-details" style="display: none;">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">New Schedule Details</h4>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="new_start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                                    <input type="time" 
                                           name="new_start_time" 
                                           id="new_start_time" 
                                           value="{{ old('new_start_time') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    @error('new_start_time')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="new_end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                                    <input type="time" 
                                           name="new_end_time" 
                                           id="new_end_time" 
                                           value="{{ old('new_end_time') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    @error('new_end_time')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Additional fields for replace type -->
                            <div id="replace-details" style="display: none;">
                                <div class="space-y-4">
                                    <div>
                                        <label for="new_subject_code" class="block text-sm font-medium text-gray-700">Subject Code</label>
                                        <input type="text" 
                                               name="new_subject_code" 
                                               id="new_subject_code" 
                                               value="{{ old('new_subject_code') }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        @error('new_subject_code')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="new_subject_name" class="block text-sm font-medium text-gray-700">Subject Name</label>
                                        <input type="text" 
                                               name="new_subject_name" 
                                               id="new_subject_name" 
                                               value="{{ old('new_subject_name') }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        @error('new_subject_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="new_instructor_name" class="block text-sm font-medium text-gray-700">Instructor Name</label>
                                        <input type="text" 
                                               name="new_instructor_name" 
                                               id="new_instructor_name" 
                                               value="{{ old('new_instructor_name') }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        @error('new_instructor_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="new_section" class="block text-sm font-medium text-gray-700">Section</label>
                                        <input type="text" 
                                               name="new_section" 
                                               id="new_section" 
                                               value="{{ old('new_section') }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        @error('new_section')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="new_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                        <textarea name="new_notes" 
                                                  id="new_notes" 
                                                  rows="2"
                                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('new_notes') }}</textarea>
                                        @error('new_notes')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cancel Message -->
                        <div id="cancel-message" class="bg-red-50 rounded-lg p-4" style="display: none;">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-red-400 text-lg"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Cancel Schedule</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>This will cancel the regular class schedule for the selected date. The recurring schedule will remain unchanged for other dates.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.laboratory.reservations') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i>
                            Create Override
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const laboratorySelect = document.getElementById('laboratory_id');
    const dateInput = document.getElementById('override_date');
    const overrideTypeSelect = document.getElementById('override_type');
    const originalScheduleInfo = document.getElementById('original-schedule-info');
    const schedulesListDiv = document.getElementById('schedules-list');
    const newScheduleDetails = document.getElementById('new-schedule-details');
    const replaceDetails = document.getElementById('replace-details');
    const cancelMessage = document.getElementById('cancel-message');

    // Handle override type changes
    overrideTypeSelect.addEventListener('change', function() {
        const type = this.value;
        
        // Hide all conditional sections
        newScheduleDetails.style.display = 'none';
        replaceDetails.style.display = 'none';
        cancelMessage.style.display = 'none';
        
        if (type === 'cancel') {
            cancelMessage.style.display = 'block';
        } else if (type === 'reschedule' || type === 'replace') {
            newScheduleDetails.style.display = 'block';
            
            if (type === 'replace') {
                replaceDetails.style.display = 'block';
            }
        }
    });

    // Handle laboratory and date changes to fetch schedules
    function fetchSchedules() {
        const laboratoryId = laboratorySelect.value;
        const date = dateInput.value;
        
        if (laboratoryId && date) {
            fetch('{{ route("admin.laboratory.get-schedules-for-date") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    laboratory_id: laboratoryId,
                    date: date
                })
            })
            .then(response => response.json())
            .then(data => {
                displaySchedules(data.schedules);
            })
            .catch(error => {
                console.error('Error fetching schedules:', error);
                schedulesListDiv.innerHTML = '<p class="text-red-500 text-sm">Error loading schedules</p>';
            });
        }
    }

    function displaySchedules(schedules) {
        if (schedules.length === 0) {
            schedulesListDiv.innerHTML = '<p class="text-gray-500 text-sm">No regular schedules found for this date</p>';
            originalScheduleInfo.style.display = 'block';
            return;
        }

        let html = '<div class="space-y-2">';
        schedules.forEach(schedule => {
            html += `
                <div class="p-3 border border-gray-200 rounded-md">
                    <div class="flex justify-between items-start">
                        <div>
                            <h5 class="font-medium text-gray-900">${schedule.subject_name}</h5>
                            <p class="text-sm text-gray-600">${schedule.instructor_name} - ${schedule.section}</p>
                            <p class="text-sm text-gray-500">${schedule.time_range}</p>
                        </div>
                        <button type="button" 
                                class="text-blue-600 hover:text-blue-800 text-sm"
                                onclick="selectSchedule(${schedule.id}, '${schedule.start_time}', '${schedule.end_time}', '${schedule.subject_name}', '${schedule.instructor_name}', '${schedule.section}')">
                            Select
                        </button>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        schedulesListDiv.innerHTML = html;
        originalScheduleInfo.style.display = 'block';
    }

    // Function to select a schedule for override
    window.selectSchedule = function(scheduleId, startTime, endTime, subjectName, instructorName, section) {
        // Set the laboratory_schedule_id hidden field
        let hiddenInput = document.querySelector('input[name="laboratory_schedule_id"]');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'laboratory_schedule_id';
            document.getElementById('override-form').appendChild(hiddenInput);
        }
        hiddenInput.value = scheduleId;

        // Pre-fill the new schedule details for reschedule
        document.getElementById('new_start_time').value = startTime;
        document.getElementById('new_end_time').value = endTime;
        document.getElementById('new_subject_name').value = subjectName;
        document.getElementById('new_instructor_name').value = instructorName;
        document.getElementById('new_section').value = section;

        // Highlight selected schedule
        document.querySelectorAll('#schedules-list .border-gray-200').forEach(el => {
            el.classList.remove('border-blue-500', 'bg-blue-50');
            el.classList.add('border-gray-200');
        });
        event.target.closest('.border-gray-200').classList.remove('border-gray-200');
        event.target.closest('div').classList.add('border-blue-500', 'bg-blue-50');
    };

    laboratorySelect.addEventListener('change', fetchSchedules);
    dateInput.addEventListener('change', fetchSchedules);

    // Initial load if values are pre-selected
    if (laboratorySelect.value && dateInput.value) {
        fetchSchedules();
    }

    // Initial override type setup
    if (overrideTypeSelect.value) {
        overrideTypeSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
