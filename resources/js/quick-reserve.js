/**
 * Quick Laboratory Reservation
 * Handles template-based quick reservation form
 */

document.addEventListener('DOMContentLoaded', function() {
    const templateSelect = document.getElementById('template');
    
    // Exit early if template select doesn't exist (not on quick reserve page)
    if (!templateSelect) {
        return;
    }
    
    const customForm = document.getElementById('custom-form');
    const templateForm = document.getElementById('template-form');
    const laboratorySelect = document.getElementById('laboratory_id');
    const purposeInput = document.getElementById('purpose');
    const dateInput = document.getElementById('reservation_date');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const numStudentsInput = document.getElementById('num_students');

    // Quick reservation templates
    const templates = {
        'class-activity': {
            purpose: 'Class Activity',
            duration: 2, // hours
            defaultStudents: 30
        },
        'examination': {
            purpose: 'Examination',
            duration: 2,
            defaultStudents: 35
        },
        'laboratory-exercise': {
            purpose: 'Laboratory Exercise',
            duration: 3,
            defaultStudents: 25
        },
        'workshop': {
            purpose: 'Workshop/Training',
            duration: 4,
            defaultStudents: 20
        },
        'meeting': {
            purpose: 'Faculty Meeting',
            duration: 1,
            defaultStudents: 10
        }
    };

    // Toggle between template and custom form
    function toggleFormMode() {
        const selectedTemplate = templateSelect.value;
        
        if (selectedTemplate === 'custom') {
            templateForm.classList.add('hidden');
            customForm.classList.remove('hidden');
            clearTemplateFields();
        } else {
            customForm.classList.add('hidden');
            templateForm.classList.remove('hidden');
            applyTemplate(selectedTemplate);
        }
    }

    // Apply selected template
    function applyTemplate(templateKey) {
        const template = templates[templateKey];
        if (!template) return;

        // Fill in template values
        if (purposeInput) {
            purposeInput.value = template.purpose;
        }

        if (numStudentsInput) {
            numStudentsInput.value = template.defaultStudents;
        }

        // Set default times if start time is selected
        if (startTimeInput && startTimeInput.value) {
            setEndTimeFromDuration(template.duration);
        }
    }

    // Set end time based on duration
    function setEndTimeFromDuration(durationHours) {
        const startTime = startTimeInput.value;
        if (!startTime) return;

        const [hours, minutes] = startTime.split(':').map(Number);
        const startDate = new Date();
        startDate.setHours(hours, minutes, 0, 0);
        
        const endDate = new Date(startDate.getTime() + (durationHours * 60 * 60 * 1000));
        
        const endHours = endDate.getHours().toString().padStart(2, '0');
        const endMinutes = endDate.getMinutes().toString().padStart(2, '0');
        
        if (endTimeInput) {
            endTimeInput.value = `${endHours}:${endMinutes}`;
        }
    }

    // Clear template-filled fields
    function clearTemplateFields() {
        if (purposeInput) purposeInput.value = '';
        if (numStudentsInput) numStudentsInput.value = '';
        if (endTimeInput) endTimeInput.value = '';
    }

    // Validate form before submission
    function validateForm(event) {
        const laboratory = laboratorySelect.value;
        const date = dateInput.value;
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        const purpose = purposeInput.value.trim();
        const numStudents = numStudentsInput.value;

        const errors = [];

        if (!laboratory) errors.push('Please select a laboratory');
        if (!date) errors.push('Please select a date');
        if (!startTime) errors.push('Please select a start time');
        if (!endTime) errors.push('Please select an end time');
        if (!purpose) errors.push('Please enter a purpose');
        if (!numStudents || numStudents < 1) errors.push('Please enter number of students');

        // Time validation
        if (startTime && endTime && startTime >= endTime) {
            errors.push('End time must be after start time');
        }

        // Date validation (not in the past)
        if (date) {
            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                errors.push('Reservation date cannot be in the past');
            }
        }

        if (errors.length > 0) {
            event.preventDefault();
            showValidationErrors(errors);
            return false;
        }

        return true;
    }

    // Show validation errors
    function showValidationErrors(errors) {
        // Remove existing error messages
        const existingErrors = document.querySelectorAll('.validation-error');
        existingErrors.forEach(error => error.remove());

        // Create and show new error message
        const errorContainer = document.createElement('div');
        errorContainer.className = 'validation-error bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6';
        
        const errorList = document.createElement('ul');
        errorList.className = 'list-disc list-inside space-y-1';
        
        errors.forEach(error => {
            const errorItem = document.createElement('li');
            errorItem.textContent = error;
            errorList.appendChild(errorItem);
        });

        errorContainer.appendChild(errorList);
        
        // Insert at the top of the form
        const form = document.querySelector('form');
        form.insertBefore(errorContainer, form.firstChild);
        
        // Scroll to error
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Set minimum date to today
    function setMinimumDate() {
        const today = new Date().toISOString().split('T')[0];
        if (dateInput) {
            dateInput.min = today;
        }
    }

    // Event listeners
    if (templateSelect) {
        templateSelect.addEventListener('change', toggleFormMode);
    }

    if (startTimeInput) {
        startTimeInput.addEventListener('change', function() {
            const selectedTemplate = templateSelect.value;
            if (selectedTemplate && selectedTemplate !== 'custom' && templates[selectedTemplate]) {
                setEndTimeFromDuration(templates[selectedTemplate].duration);
            }
        });
    }

    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', validateForm);
    }

    // Initialize
    setMinimumDate();
    toggleFormMode(); // Set initial state
});
