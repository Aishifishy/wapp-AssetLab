/**
 * Date Validation Manager - Handles date constraints and validation
 */
class DateValidationManager {
    constructor() {
        this.academicYear = null;
        this.termNames = {
            '1': 'First Term',
            '2': 'Second Term', 
            '3': 'Third Term'
        };
    }

    /**
     * Initialize academic term validation
     * @param {Object} academicYearData - Academic year data with start_date and end_date
     */
    initAcademicTermValidation(academicYearData) {
        this.academicYear = academicYearData;
        
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const termNumberSelect = document.getElementById('term_number');
        const termNameInput = document.getElementById('term_name');
        
        if (!startDateInput || !endDateInput) {
            console.warn('Date inputs not found for academic term validation');
            return;
        }
        
        // Set date constraints based on academic year
        this.setDateConstraints(startDateInput, endDateInput);
        
        // Set up term number change handler
        if (termNumberSelect && termNameInput) {
            this.initTermNameHandler(termNumberSelect, termNameInput);
        }
        
        // Set up end date validation
        this.initEndDateValidation(startDateInput, endDateInput);
        
        console.log('Academic term validation initialized');
    }

    /**
     * Set date constraints based on academic year
     * @param {HTMLElement} startDateInput 
     * @param {HTMLElement} endDateInput 
     */
    setDateConstraints(startDateInput, endDateInput) {
        if (!this.academicYear) return;
        
        const startDate = this.academicYear.start_date;
        const endDate = this.academicYear.end_date;
        
        startDateInput.setAttribute('min', startDate);
        startDateInput.setAttribute('max', endDate);
        endDateInput.setAttribute('min', startDate);
        endDateInput.setAttribute('max', endDate);
        
        console.log('Date constraints set:', { startDate, endDate });
    }

    /**
     * Initialize term name auto-population
     * @param {HTMLElement} termNumberSelect 
     * @param {HTMLElement} termNameInput 
     */
    initTermNameHandler(termNumberSelect, termNameInput) {
        const updateTermName = () => {
            const selectedValue = termNumberSelect.value;
            termNameInput.value = this.termNames[selectedValue] || '';
        };
        
        termNumberSelect.addEventListener('change', updateTermName);
        
        // Set initial term name if term number is pre-selected
        updateTermName();
    }

    /**
     * Initialize end date validation
     * @param {HTMLElement} startDateInput 
     * @param {HTMLElement} endDateInput 
     */
    initEndDateValidation(startDateInput, endDateInput) {
        const validateEndDate = () => {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            
            if (endDate <= startDate) {
                this.showValidationError('End date must be after start date');
                endDateInput.value = '';
            }
        };
        
        endDateInput.addEventListener('change', validateEndDate);
    }

    /**
     * Show validation error message
     * @param {string} message 
     */
    showValidationError(message) {
        // Try to use the existing showError function if available
        if (typeof window.showError === 'function') {
            window.showError(message, { duration: 5000 });
        } else {
            // Fallback to alert if showError is not available
            alert(message);
        }
    }

    /**
     * Initialize request date validation (for equipment requests)
     */
    initRequestDateValidation() {
        const requestDateInput = document.getElementById('request_date');
        const returnDateInput = document.getElementById('return_date');
        
        if (!requestDateInput) return;
        
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        requestDateInput.setAttribute('min', today);
        
        if (returnDateInput) {
            // Set up return date validation
            requestDateInput.addEventListener('change', () => {
                returnDateInput.setAttribute('min', requestDateInput.value || today);
                if (returnDateInput.value && returnDateInput.value < requestDateInput.value) {
                    returnDateInput.value = '';
                }
            });
            
            returnDateInput.addEventListener('change', () => {
                if (requestDateInput.value && returnDateInput.value < requestDateInput.value) {
                    this.showValidationError('Return date must be after request date');
                    returnDateInput.value = '';
                }
            });
        }
        
        console.log('Request date validation initialized');
    }

    /**
     * Initialize reservation date constraints
     */
    initReservationDateValidation() {
        const dateInput = document.getElementById('reservation_date');
        if (!dateInput) return;
        
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
        
        console.log('Reservation date validation initialized');
    }

    /**
     * Validate date range
     * @param {string} startDate 
     * @param {string} endDate 
     * @returns {boolean}
     */
    validateDateRange(startDate, endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        return end > start;
    }

    /**
     * Format date for display
     * @param {string|Date} date 
     * @returns {string}
     */
    formatDate(date) {
        return new Date(date).toLocaleDateString();
    }
}

// Create and export instance
const dateValidationManager = new DateValidationManager();

// Expose for backward compatibility
window.dateValidationManager = dateValidationManager;

export default dateValidationManager;
