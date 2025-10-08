@extends('layouts.admin')

@section('title', 'Edit Laboratory Reservation')
@section('header', 'Edit Laboratory Reservation')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Edit Reservation</h1>
        <a href="{{ route('admin.laboratory.reservations') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            <i class="fas fa-arrow-left mr-2"></i> Back to Reservations
        </a>
    </div>

    <x-flash-messages />

    <!-- Current Reservation Info Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
            <h3 class="text-lg font-medium text-blue-900 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Current Reservation Details
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Requester</h4>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->user->name }}</p>
                    <p class="text-sm text-gray-600">{{ $reservation->user->email }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Current Laboratory</h4>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->laboratory->name }}</p>
                    <p class="text-sm text-gray-600">{{ $reservation->laboratory->building }}, Room {{ $reservation->laboratory->room_number }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Current Date & Time</h4>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->reservation_date->format('M d, Y') }}</p>
                    <p class="text-sm text-gray-600">{{ $reservation->formatted_start_time }} - {{ $reservation->formatted_end_time }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Status</h4>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($reservation->status === 'approved') bg-green-100 text-green-800
                            @elseif($reservation->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($reservation->status === 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($reservation->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Students</h4>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->num_students }} students</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Created</h4>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->created_at->format('M d, Y') }}</p>
                    <p class="text-sm text-gray-600">{{ $reservation->created_at->diffForHumans() }}</p>
                </div>
            </div>
            
            @if($reservation->purpose)
                <div class="mt-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Current Purpose</h4>
                    <p class="mt-1 text-base text-gray-900 bg-gray-50 p-3 rounded border">{{ $reservation->purpose }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-edit mr-2 text-blue-600"></i>
                Edit Reservation
            </h3>
            <p class="text-sm text-gray-600 mt-1">Make changes to the reservation details. The user will be notified of any changes made.</p>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.laboratory.update-reservation', $reservation) }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Laboratory Selection -->
                    <div class="form-group">
                        <label for="laboratory_id" class="form-label form-label-required">
                            Laboratory
                        </label>
                        <select name="laboratory_id" id="laboratory_id" required class="form-select">
                            <option value="">Select Laboratory</option>
                            @foreach($laboratories as $lab)
                                <option value="{{ $lab->id }}" 
                                        data-capacity="{{ $lab->capacity }}"
                                        {{ old('laboratory_id', $reservation->laboratory_id) == $lab->id ? 'selected' : '' }}>
                                    {{ $lab->name }} ({{ $lab->building }}, Room {{ $lab->room_number }}) - Capacity: {{ $lab->capacity }}
                                </option>
                            @endforeach
                        </select>
                        <x-form-error field="laboratory_id" />
                    </div>
                    
                    <!-- Reservation Date -->
                    <div class="form-group">
                        <label for="reservation_date" class="form-label form-label-required">
                            Date
                        </label>
                        <input type="date" name="reservation_date" id="reservation_date" required
                            min="{{ date('Y-m-d') }}"
                            value="{{ old('reservation_date', $reservation->reservation_date->format('Y-m-d')) }}"
                            class="form-input">
                        <x-form-error field="reservation_date" />
                    </div>
                    
                    <!-- Start Time -->
                    <div class="form-group">
                        <label for="start_time" class="form-label form-label-required">
                            Start Time (7:00 AM - 9:00 PM)
                        </label>
                        <input type="time" name="start_time" id="start_time" required
                            min="07:00" max="21:00"
                            value="{{ old('start_time', \Carbon\Carbon::parse($reservation->start_time)->format('H:i')) }}"
                            class="form-input">
                        <x-form-error field="start_time" />
                    </div>
                    
                    <!-- End Time -->
                    <div class="form-group">
                        <label for="end_time" class="form-label form-label-required">
                            End Time (7:00 AM - 9:00 PM)
                        </label>
                        <input type="time" name="end_time" id="end_time" required
                            min="07:00" max="21:00"
                            value="{{ old('end_time', \Carbon\Carbon::parse($reservation->end_time)->format('H:i')) }}"
                            class="form-input">
                        <x-form-error field="end_time" />
                    </div>
                    
                    <!-- Number of Students -->
                    <div class="form-group">
                        <label for="num_students" class="form-label form-label-required">
                            Number of Students
                        </label>
                        <input type="number" name="num_students" id="num_students" required
                            min="1" max="{{ $reservation->laboratory->capacity }}"
                            value="{{ old('num_students', $reservation->num_students) }}"
                            class="form-input">
                        <p class="text-xs text-gray-500 mt-1">Maximum capacity will update based on selected laboratory</p>
                        <x-form-error field="num_students" />
                    </div>
                    
                    <!-- Course Code -->
                    <div class="form-group">
                        <label for="course_code" class="form-label">
                            Course Code
                        </label>
                        <input type="text" name="course_code" id="course_code"
                            value="{{ old('course_code', $reservation->course_code) }}"
                            placeholder="e.g., CS101"
                            class="form-input">
                        <x-form-error field="course_code" />
                    </div>
                    
                    <!-- Subject -->
                    <div class="form-group">
                        <label for="subject" class="form-label">
                            Subject
                        </label>
                        <input type="text" name="subject" id="subject"
                            value="{{ old('subject', $reservation->subject) }}"
                            placeholder="e.g., Introduction to Programming"
                            class="form-input">
                        <x-form-error field="subject" />
                    </div>
                    
                    <!-- Section -->
                    <div class="form-group">
                        <label for="section" class="form-label">
                            Section
                        </label>
                        <input type="text" name="section" id="section"
                            value="{{ old('section', $reservation->section) }}"
                            placeholder="e.g., A, B, 1"
                            class="form-input">
                        <x-form-error field="section" />
                    </div>
                </div>
                
                <!-- Purpose -->
                <div class="form-group mt-6">
                    <label for="purpose" class="form-label form-label-required">
                        Purpose
                    </label>
                    <textarea name="purpose" id="purpose" rows="3" required
                        placeholder="Describe the purpose of this reservation"
                        class="form-textarea">{{ old('purpose', $reservation->purpose) }}</textarea>
                    <x-form-error field="purpose" />
                </div>
                
                <!-- Admin Notes -->
                <div class="form-group mt-6">
                    <label for="admin_notes" class="form-label">
                        Admin Notes <span class="text-gray-500">(Internal use only)</span>
                    </label>
                    <textarea name="admin_notes" id="admin_notes" rows="2"
                        placeholder="Internal notes about this reservation (not visible to user)"
                        class="form-textarea">{{ old('admin_notes', $reservation->admin_notes) }}</textarea>
                    <x-form-error field="admin_notes" />
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.laboratory.reservations') }}" 
                       class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    
                    <button type="button" 
                            onclick="openCancelModal()"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-ban mr-2"></i>Cancel Reservation
                    </button>
                    
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Reservation Modal -->
<div id="cancelModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.laboratory.cancel-reservation', $reservation) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-ban text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Cancel Reservation
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to cancel this reservation? This action cannot be undone and the user will be notified.
                                </p>
                            </div>
                            <div class="mt-4">
                                <label for="cancellation_reason" class="block text-sm font-medium text-gray-700">Cancellation Reason</label>
                                <textarea name="cancellation_reason" id="cancellation_reason" rows="3" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" 
                                          placeholder="Provide a reason for cancelling this reservation..." required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel Reservation
                    </button>
                    <button type="button" 
                            onclick="closeCancelModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Keep Reservation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const laboratorySelect = document.getElementById('laboratory_id');
    const numStudentsInput = document.getElementById('num_students');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');

    // Update capacity when laboratory changes
    laboratorySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const capacity = selectedOption.dataset.capacity;
            numStudentsInput.max = capacity;
            
            // Reset num_students if it exceeds new capacity
            if (parseInt(numStudentsInput.value) > parseInt(capacity)) {
                numStudentsInput.value = capacity;
            }
            
            // Update placeholder
            numStudentsInput.placeholder = `Max: ${capacity}`;
        }
    });

    // Validate time inputs are within office hours (7 AM - 9 PM)
    function validateTimeInput(input) {
        const time = input.value;
        if (time) {
            const [hours, minutes] = time.split(':').map(Number);
            const timeInMinutes = hours * 60 + minutes;
            const minTime = 7 * 60; // 7:00 AM
            const maxTime = 21 * 60; // 9:00 PM
            
            if (timeInMinutes < minTime || timeInMinutes > maxTime) {
                input.setCustomValidity('Time must be between 7:00 AM and 9:00 PM');
                return false;
            } else {
                input.setCustomValidity('');
                return true;
            }
        }
        return true;
    }

    // Validate end time is after start time
    function validateTimeRange() {
        if (startTimeInput.value && endTimeInput.value) {
            if (startTimeInput.value >= endTimeInput.value) {
                endTimeInput.setCustomValidity('End time must be after start time');
                return false;
            } else {
                endTimeInput.setCustomValidity('');
                return true;
            }
        }
        return true;
    }

    // Add event listeners for time validation
    startTimeInput.addEventListener('change', function() {
        validateTimeInput(this);
        validateTimeRange();
    });

    endTimeInput.addEventListener('change', function() {
        validateTimeInput(this);
        validateTimeRange();
    });

    // Form validation before submission
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!validateTimeInput(startTimeInput) || 
            !validateTimeInput(endTimeInput) || 
            !validateTimeRange()) {
            e.preventDefault();
            alert('Please correct the time selection errors before submitting.');
        }
    });
});

// Modal functions
function openCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('cancelModal');
    if (e.target === modal) {
        closeCancelModal();
    }
});
</script>
@endpush