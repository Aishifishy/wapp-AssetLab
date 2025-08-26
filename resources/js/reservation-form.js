/**
 * Laboratory Reservation Create Form
 * Handles conflict checking, time validation, and recurring patterns
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reservation-form');
    const laboratorySelect = document.getElementById('laboratory_id');
    const dateInput = document.getElementById('reservation_date');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const isRecurringCheckbox = document.getElementById('is_recurring');
    const recurringDetails = document.getElementById('recurring-details');
    const recurringPatternSelect = document.getElementById('recurring_pattern');
    const endDateInput = document.getElementById('end_date');
    const conflictAlert = document.getElementById('conflict-alert');
    const conflictListContainer = document.getElementById('conflict-list');
    const submitButton = form.querySelector('button[type="submit"]');

    let conflictCheckTimeout;

    // Toggle recurring details visibility
    isRecurringCheckbox.addEventListener('change', function() {
        if (this.checked) {
            recurringDetails.classList.remove('hidden');
            endDateInput.required = true;
        } else {
            recurringDetails.classList.add('hidden');
            endDateInput.required = false;
            endDateInput.value = '';
        }
    });

    // Check for conflicts when form inputs change
    function checkConflicts() {
        const laboratoryId = laboratorySelect.value;
        const reservationDate = dateInput.value;
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;

        if (!laboratoryId || !reservationDate || !startTime || !endTime) {
            hideConflictAlert();
            return;
        }

        clearTimeout(conflictCheckTimeout);
        conflictCheckTimeout = setTimeout(() => {
            fetchConflicts(laboratoryId, reservationDate, startTime, endTime);
        }, 500);
    }

    // Fetch conflicts from server
    async function fetchConflicts(laboratoryId, date, startTime, endTime) {
        try {
            const response = await fetch('/ruser/laboratory/check-conflicts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    laboratory_id: laboratoryId,
                    reservation_date: date,
                    start_time: startTime,
                    end_time: endTime
                })
            });

            const data = await response.json();
            
            if (data.hasConflicts) {
                showConflictAlert(data.conflicts);
                disableSubmit();
            } else {
                hideConflictAlert();
                enableSubmit();
            }
        } catch (error) {
            console.error('Error checking conflicts:', error);
            hideConflictAlert();
            enableSubmit();
        }
    }

    // Show conflict alert with details
    function showConflictAlert(conflicts) {
        conflictAlert.classList.remove('hidden');
        
        conflictListContainer.innerHTML = conflicts.map(conflict => `
            <div class="flex items-center justify-between p-3 bg-red-50 rounded border border-red-200">
                <div>
                    <div class="font-medium text-red-800">
                        ${formatDate(conflict.reservation_date)} at ${formatTime(conflict.start_time)} - ${formatTime(conflict.end_time)}
                    </div>
                    <div class="text-sm text-red-600">
                        ${conflict.purpose || 'No purpose specified'}
                    </div>
                </div>
                <div class="text-sm text-red-600 font-medium">
                    ${conflict.status}
                </div>
            </div>
        `).join('');
    }

    // Hide conflict alert
    function hideConflictAlert() {
        conflictAlert.classList.add('hidden');
        conflictListContainer.innerHTML = '';
    }

    // Disable submit button
    function disableSubmit() {
        submitButton.disabled = true;
        submitButton.classList.add('opacity-50', 'cursor-not-allowed');
    }

    // Enable submit button
    function enableSubmit() {
        submitButton.disabled = false;
        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    // Validate time inputs
    function validateTime() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;

        if (startTime && endTime) {
            if (startTime >= endTime) {
                showTimeError('End time must be after start time');
                disableSubmit();
                return false;
            } else {
                hideTimeError();
                enableSubmit();
                return true;
            }
        }
        return true;
    }

    // Show time validation error
    function showTimeError(message) {
        let errorElement = document.getElementById('time-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.id = 'time-error';
            errorElement.className = 'mt-2 text-sm text-red-600';
            endTimeInput.parentNode.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }

    // Hide time validation error
    function hideTimeError() {
        const errorElement = document.getElementById('time-error');
        if (errorElement) {
            errorElement.remove();
        }
    }

    // Format date for display
    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    // Format time for display
    function formatTime(timeString) {
        return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    }

    // Add event listeners
    [laboratorySelect, dateInput, startTimeInput, endTimeInput].forEach(input => {
        input.addEventListener('change', checkConflicts);
    });

    startTimeInput.addEventListener('change', validateTime);
    endTimeInput.addEventListener('change', validateTime);

    // Form submission validation
    form.addEventListener('submit', function(e) {
        if (!validateTime()) {
            e.preventDefault();
            return false;
        }

        // Additional validation for recurring reservations
        if (isRecurringCheckbox.checked) {
            const endDate = endDateInput.value;
            const startDate = dateInput.value;

            if (!endDate) {
                e.preventDefault();
                alert('Please select an end date for recurring reservations.');
                return false;
            }

            if (new Date(endDate) <= new Date(startDate)) {
                e.preventDefault();
                alert('End date must be after the start date.');
                return false;
            }
        }
    });

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    dateInput.min = today;
    if (endDateInput) {
        endDateInput.min = today;
    }

    // Update end date minimum when start date changes
    dateInput.addEventListener('change', function() {
        if (endDateInput) {
            endDateInput.min = this.value;
            if (endDateInput.value && endDateInput.value <= this.value) {
                endDateInput.value = '';
            }
        }
    });
});
