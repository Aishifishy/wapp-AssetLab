/**
 * Form Utilities JavaScript
 * Handles form validation, date/time inputs, and dynamic form behaviors
 */
class FormUtilities {
    constructor() {
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.setupDateTimeInputs();
            this.setupFormValidation();
            this.setupDynamicFields();
        });
    }

    setupDateTimeInputs() {
        // Set minimum dates for datetime-local inputs to current time
        const dateTimeInputs = document.querySelectorAll('input[type="datetime-local"]');
        const now = new Date();
        const nowString = now.toISOString().slice(0, 16);

        dateTimeInputs.forEach(input => {
            if (!input.min) {
                input.min = nowString;
            }
        });

        // Setup date range validation
        this.setupDateRangeValidation();
    }

    setupDateRangeValidation() {
        const dateRangePairs = this.findDateRangePairs();
        
        dateRangePairs.forEach(pair => {
            pair.start.addEventListener('change', () => this.validateDateRange(pair));
            pair.end.addEventListener('change', () => this.validateDateRange(pair));
        });
    }

    findDateRangePairs() {
        const pairs = [];
        const commonPairs = [
            { start: 'requested_from', end: 'requested_until' },
            { start: 'start_date', end: 'end_date' },
            { start: 'from_date', end: 'to_date' },
            { start: 'start_time', end: 'end_time' }
        ];

        commonPairs.forEach(pair => {
            const startInput = document.getElementById(pair.start);
            const endInput = document.getElementById(pair.end);
            
            if (startInput && endInput) {
                pairs.push({ start: startInput, end: endInput });
            }
        });

        return pairs;
    }

    validateDateRange(pair) {
        if (!pair.start.value || !pair.end.value) return;

        const startDate = new Date(pair.start.value);
        const endDate = new Date(pair.end.value);

        if (startDate >= endDate) {
            const fieldName = this.getFieldDisplayName(pair.end);
            alert(`The ${fieldName} must be after the start date/time`);
            pair.end.value = '';
            pair.end.focus();
        }
    }

    getFieldDisplayName(input) {
        const label = document.querySelector(`label[for="${input.id}"]`);
        if (label) {
            return label.textContent.toLowerCase().replace(':', '');
        }
        
        const nameMap = {
            'requested_until': 'return date',
            'end_date': 'end date',
            'to_date': 'end date',
            'end_time': 'end time'
        };
        
        return nameMap[input.id] || 'end date';
    }

    setupFormValidation() {
        // Setup real-time validation for required fields
        const requiredInputs = document.querySelectorAll('input[required], select[required], textarea[required]');
        
        requiredInputs.forEach(input => {
            input.addEventListener('blur', () => this.validateRequired(input));
            input.addEventListener('input', () => this.clearValidationError(input));
        });
    }

    validateRequired(input) {
        if (!input.value.trim()) {
            this.showValidationError(input, 'This field is required');
        } else {
            this.clearValidationError(input);
        }
    }

    showValidationError(input, message) {
        this.clearValidationError(input);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'validation-error text-red-600 text-sm mt-1';
        errorDiv.textContent = message;
        errorDiv.id = `${input.id}-error`;
        
        input.parentNode.appendChild(errorDiv);
        input.classList.add('border-red-500');
    }

    clearValidationError(input) {
        const existingError = document.getElementById(`${input.id}-error`);
        if (existingError) {
            existingError.remove();
        }
        input.classList.remove('border-red-500');
    }    setupDynamicFields() {
        // Setup conditional field display
        this.setupConditionalFields();
          // Setup laboratory selection handlers
        this.setupLaboratorySelection();
        
        // Setup navigation handlers
        this.setupNavigationHandlers();
          // Setup academic year date calculations
        this.setupAcademicYearCalculations();
        
        // Setup academic term functionality
        this.setupAcademicTerms();
    }

    setupConditionalFields() {
        // Find select elements that might trigger conditional fields
        const triggerSelects = document.querySelectorAll('select[data-target]');
        
        triggerSelects.forEach(select => {
            select.addEventListener('change', () => this.handleConditionalField(select));
            // Trigger on page load
            this.handleConditionalField(select);
        });
    }

    handleConditionalField(select) {
        const targetId = select.getAttribute('data-target');
        const targetElement = document.getElementById(targetId);
        const showValue = select.getAttribute('data-show-value');
        
        if (targetElement) {
            if (select.value === showValue) {
                targetElement.classList.remove('hidden');
            } else {
                targetElement.classList.add('hidden');
            }
        }
    }

    // Utility function to serialize form data
    serializeForm(form) {
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        return data;
    }

    // Utility function to populate form from data
    populateForm(form, data) {
        Object.keys(data).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = data[key];
                } else if (input.type === 'radio') {
                    const radio = form.querySelector(`[name="${key}"][value="${data[key]}"]`);
                    if (radio) radio.checked = true;
                } else {
                    input.value = data[key];
                }            }
        });
    }

    setupLaboratorySelection() {
        const laboratorySelect = document.getElementById('laboratory_id');
        if (!laboratorySelect) return;

        laboratorySelect.addEventListener('change', () => {
            const selectedOption = laboratorySelect.options[laboratorySelect.selectedIndex];
            const capacity = selectedOption.getAttribute('data-capacity');
            
            const capacitySpan = document.getElementById('lab-capacity');
            const infoDiv = document.getElementById('laboratory-info');
            
            if (capacity && capacitySpan && infoDiv) {
                capacitySpan.textContent = capacity;
                infoDiv.classList.remove('hidden');
            } else if (infoDiv) {
                infoDiv.classList.add('hidden');
            }
        });
    }    setupNavigationHandlers() {
        // Handle back buttons
        document.querySelectorAll('[data-action="go-back"]').forEach(button => {
            button.addEventListener('click', () => {
                window.history.back();
            });
        });
    }

    setupAcademicYearCalculations() {
        const startDateInput = document.getElementById('start_date');
        if (!startDateInput) return;

        startDateInput.addEventListener('change', () => {
            const endDateInput = document.getElementById('end_date');
            if (!endDateInput) return;

            const startDate = new Date(startDateInput.value);
            if (!startDate || isNaN(startDate)) return;

            // Calculate end date (one year later minus one day)
            const endDate = new Date(startDate);
            endDate.setFullYear(startDate.getFullYear() + 1);
            endDate.setDate(endDate.getDate() - 1);

            // Format as YYYY-MM-DD for input[type="date"]            const formattedEndDate = endDate.toISOString().split('T')[0];
            endDateInput.value = formattedEndDate;
        });
    }

    setupAcademicTerms() {
        // Setup term number to name mapping
        const termNumberSelect = document.getElementById('term_number');
        const termNameInput = document.getElementById('term_name');
        
        if (termNumberSelect && termNameInput) {
            const termNames = {
                '1': 'First Term',
                '2': 'Second Term', 
                '3': 'Third Term'
            };

            termNumberSelect.addEventListener('change', () => {
                const selectedTerm = termNumberSelect.value;
                termNameInput.value = termNames[selectedTerm] || '';
            });

            // Set initial term name if term number is pre-selected
            if (termNumberSelect.value) {
                termNameInput.value = termNames[termNumberSelect.value] || '';
            }
        }

        // Setup academic year date constraints from server data
        this.setupAcademicYearConstraints();
    }

    setupAcademicYearConstraints() {
        // Check if academic year dates are provided via data attributes
        const container = document.querySelector('[data-academic-start]');
        if (!container) return;

        const startDate = container.getAttribute('data-academic-start');
        const endDate = container.getAttribute('data-academic-end');

        if (startDate && endDate) {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            if (startDateInput) {
                startDateInput.min = startDate;
                startDateInput.max = endDate;
            }
            if (endDateInput) {
                endDateInput.min = startDate;
                endDateInput.max = endDate;
            }
        }
    }
}

// Initialize when module is imported
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.formUtilities = new FormUtilities();
    });
} else {
    window.formUtilities = new FormUtilities();
}

export default FormUtilities;
