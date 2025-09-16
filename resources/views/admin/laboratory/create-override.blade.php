@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Laboratory Management</h1>
        <a href="{{ route('admin.laboratory.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            <i class="fas fa-arrow-left mr-2"></i> Back to Laboratories
        </a>
    </div>

    <x-flash-messages />

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('admin.laboratory.reservations') }}" 
               class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                <i class="fas fa-calendar-check mr-2"></i>
                Reservations
            </a>
            <a href="{{ route('admin.laboratory.schedule-overrides') }}" 
               class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Schedule Overrides
            </a>
        </nav>
    </div>

    <!-- Create Override Content -->
    <div class="flex justify-content-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Create Schedule Override</h2>
        <a href="{{ route('admin.laboratory.schedule-overrides') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            <i class="fas fa-arrow-left mr-2"></i> Back to Overrides
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Create Schedule Override</h3>
            <p class="mt-1 text-sm text-gray-600">Create a one-time modification when a user has made arrangements with the professor to use the laboratory during regular class hours.</p>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.laboratory.store-override') }}" method="POST" id="override-form">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div class="form-group">
                            <label for="laboratory_id" class="form-label form-label-required">Laboratory</label>
                            <select name="laboratory_id" 
                                    id="laboratory_id" 
                                    class="form-select @error('laboratory_id') form-select-error @enderror"
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
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="override_date" class="form-label form-label-required">Override Date</label>
                            <input type="date" 
                                   name="override_date" 
                                   id="override_date" 
                                   value="{{ old('override_date', $selectedDate) }}"
                                   min="{{ now()->toDateString() }}"
                                   class="form-input @error('override_date') form-input-error @enderror"
                                   required>
                            @error('override_date')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="override_type" class="form-label form-label-required">Override Type</label>
                            <select name="override_type" 
                                    id="override_type" 
                                    class="form-select @error('override_type') form-select-error @enderror"
                                    required>
                                <option value="">Select Override Type</option>
                                <option value="cancel" {{ old('override_type') === 'cancel' ? 'selected' : '' }}>Cancel - Remove the class for this date</option>
                                <option value="reschedule" {{ old('override_type') === 'reschedule' ? 'selected' : '' }}>Reschedule - Change the time only</option>
                                <option value="replace" {{ old('override_type') === 'replace' ? 'selected' : '' }}>Replace - Change class details completely</option>
                            </select>
                            @error('override_type')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="reason" class="form-label form-label-required">Reason for Override</label>
                            <textarea name="reason" 
                                      id="reason" 
                                      rows="3"
                                      placeholder="Explain why this override is needed..."
                                      class="form-textarea @error('reason') form-textarea-error @enderror"
                                      required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="requested_by" class="form-label-optional">Requested By (Optional)</label>
                            <select name="requested_by" 
                                    id="requested_by" 
                                    class="form-select @error('requested_by') form-select-error @enderror">
                                <option value="">Select User (if applicable)</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ (old('requested_by') ?? $requestedBy) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="form-help">Select the user who requested this schedule override (e.g., someone who made arrangements with the professor)</p>
                            @error('requested_by')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="expires_at" class="form-label-optional">Expiration Date (Optional)</label>
                            <input type="datetime-local" 
                                   name="expires_at" 
                                   id="expires_at" 
                                   value="{{ old('expires_at') }}"
                                   class="form-input @error('expires_at') form-input-error @enderror">
                            <p class="form-help">If set, the override will automatically deactivate after this date/time</p>
                            @error('expires_at')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Original Schedule Information -->
                        <div id="original-schedule-info" class="bg-gray-50 rounded-lg p-4" style="display: none;">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Schedules & Reservations for Selected Date</h4>
                            <p class="text-xs text-gray-600 mb-3">You can override regular classes or approved laboratory reservations</p>
                            <div id="schedules-list"></div>
                        </div>

                        <!-- New Schedule Details (shown for reschedule/replace) -->
                        <div id="new-schedule-details" style="display: none;">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">New Schedule Details</h4>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="form-group">
                                    <label for="new_start_time" class="form-label">Start Time</label>
                                    <input type="time" 
                                           name="new_start_time" 
                                           id="new_start_time" 
                                           value="{{ old('new_start_time') }}"
                                           class="form-input @error('new_start_time') form-input-error @enderror">
                                    @error('new_start_time')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="new_end_time" class="form-label">End Time</label>
                                    <input type="time" 
                                           name="new_end_time" 
                                           id="new_end_time" 
                                           value="{{ old('new_end_time') }}"
                                           class="form-input @error('new_end_time') form-input-error @enderror">
                                    @error('new_end_time')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Additional fields for replace type -->
                            <div id="replace-details" style="display: none;">
                                <div class="space-y-4">
                                    <div class="form-group">
                                        <label for="new_subject_code" class="form-label">Subject Code</label>
                                        <input type="text" 
                                               name="new_subject_code" 
                                               id="new_subject_code" 
                                               value="{{ old('new_subject_code') }}"
                                               class="form-input @error('new_subject_code') form-input-error @enderror">
                                        @error('new_subject_code')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="new_subject_name" class="form-label">Subject Name</label>
                                        <input type="text" 
                                               name="new_subject_name" 
                                               id="new_subject_name" 
                                               value="{{ old('new_subject_name') }}"
                                               class="form-input @error('new_subject_name') form-input-error @enderror">
                                        @error('new_subject_name')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="new_instructor_name" class="form-label">Instructor Name</label>
                                        <input type="text" 
                                               name="new_instructor_name" 
                                               id="new_instructor_name" 
                                               value="{{ old('new_instructor_name') }}"
                                               class="form-input @error('new_instructor_name') form-input-error @enderror">
                                        @error('new_instructor_name')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="new_section" class="form-label">Section</label>
                                        <input type="text" 
                                               name="new_section" 
                                               id="new_section" 
                                               value="{{ old('new_section') }}"
                                               class="form-input @error('new_section') form-input-error @enderror">
                                        @error('new_section')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="new_notes" class="form-label-optional">Notes</label>
                                        <textarea name="new_notes" 
                                                  id="new_notes" 
                                                  rows="2"
                                                  class="form-textarea @error('new_notes') form-textarea-error @enderror">{{ old('new_notes') }}</textarea>
                                        @error('new_notes')
                                            <p class="form-error">{{ $message }}</p>
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

                <div class="form-actions-section">
                    <div class="form-actions">
                        <a href="{{ route('admin.laboratory.reservations') }}" 
                           class="btn btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="btn btn-primary">
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
            schedulesListDiv.innerHTML = '<p class="text-gray-500 text-sm">No regular schedules or reservations found for this date</p>';
            originalScheduleInfo.style.display = 'block';
            return;
        }

        let html = '<div class="space-y-2">';
        schedules.forEach(schedule => {
            // Determine badge color and icon based on type
            let badgeClass = '';
            let icon = '';
            if (schedule.type === 'regular_schedule') {
                badgeClass = 'bg-blue-100 text-blue-800';
                icon = '<i class="fas fa-chalkboard-teacher mr-1"></i>';
            } else if (schedule.type === 'reservation') {
                badgeClass = 'bg-purple-100 text-purple-800';
                icon = '<i class="fas fa-calendar-check mr-1"></i>';
            }

            html += `
                <div class="p-3 border border-gray-200 rounded-md hover:border-gray-300 transition-colors">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center mb-1">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${badgeClass}">
                                    ${icon}${schedule.schedule_type_label}
                                </span>
                            </div>
                            <h5 class="font-medium text-gray-900">${schedule.subject_name}</h5>
                            <p class="text-sm text-gray-600">${schedule.instructor_name} - ${schedule.section}</p>
                            <p class="text-sm text-gray-500">${schedule.time_range}</p>
                            <p class="text-xs text-gray-400 mt-1">${schedule.details}</p>
                        </div>
                        <button type="button" 
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium px-3 py-1 border border-blue-300 rounded hover:bg-blue-50"
                                onclick="selectSchedule('${schedule.id}', '${schedule.type}', '${schedule.start_time}', '${schedule.end_time}', '${schedule.subject_name}', '${schedule.instructor_name}', '${schedule.section}', ${schedule.schedule_id || 'null'}, ${schedule.reservation_id || 'null'})">
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
    window.selectSchedule = function(scheduleId, scheduleType, startTime, endTime, subjectName, instructorName, section, actualScheduleId, reservationId) {
        // Clear existing hidden inputs
        document.querySelectorAll('input[name="laboratory_schedule_id"], input[name="laboratory_reservation_id"]').forEach(el => el.remove());

        // Set the appropriate hidden field based on type
        if (scheduleType === 'regular_schedule' && actualScheduleId) {
            let hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'laboratory_schedule_id';
            hiddenInput.value = actualScheduleId;
            document.getElementById('override-form').appendChild(hiddenInput);
        } else if (scheduleType === 'reservation' && reservationId) {
            let hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'laboratory_reservation_id';
            hiddenInput.value = reservationId;
            document.getElementById('override-form').appendChild(hiddenInput);
        }

        // Pre-fill the new schedule details for reschedule
        document.getElementById('new_start_time').value = startTime;
        document.getElementById('new_end_time').value = endTime;
        document.getElementById('new_subject_name').value = subjectName;
        document.getElementById('new_instructor_name').value = instructorName;
        document.getElementById('new_section').value = section;

        // Highlight selected schedule
        document.querySelectorAll('#schedules-list .border-gray-200, #schedules-list .border-gray-300').forEach(el => {
            el.classList.remove('border-blue-500', 'bg-blue-50');
            el.classList.add('border-gray-200');
        });
        event.target.closest('.border-gray-200, .border-gray-300').classList.remove('border-gray-200', 'border-gray-300');
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
