/**
 * Reservation Manager - Handles laboratory reservation functionality
 * Including quick reservations, conflict checking, and reservation modals
 */

class ReservationManager {
    constructor() {
        this.conflictChecker = new ConflictChecker();
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.init();
            });
        } else {
            this.init();
        }
    }init() {
        this.bindEvents();
        this.initConflictChecking();
        this.initDateConstraints();
        this.initModalHandlers();
        this.initQuickReservation();
    }

    bindEvents() {
        // Template selection in quick reserve
        const templateSelect = document.getElementById('template');
        if (templateSelect) {
            templateSelect.addEventListener('change', () => this.handleTemplateSelection());
        }

        // Rejection modal handlers
        document.querySelectorAll('[data-action="open-reject-modal"]').forEach(button => {
            button.addEventListener('click', (e) => {
                const reservationId = button.getAttribute('data-reservation-id');
                this.openRejectModal(reservationId);
            });
        });

        // Date/time change handlers for conflict checking
        const dateInput = document.getElementById('reservation_date');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');

        if (dateInput && startTimeInput && endTimeInput) {
            dateInput.addEventListener('change', () => this.checkConflicts());
            startTimeInput.addEventListener('change', () => this.checkConflicts());
            endTimeInput.addEventListener('change', () => this.checkConflicts());
        }

        // Recurring reservation handlers
        const isRecurringCheckbox = document.getElementById('is_recurring');
        const recurrencePatternSelect = document.getElementById('recurrence_pattern');
        const recurrenceEndDateInput = document.getElementById('recurrence_end_date');

        if (isRecurringCheckbox) {
            isRecurringCheckbox.addEventListener('change', () => {
                if (isRecurringCheckbox.checked) {
                    this.debouncedRecurringCheck();
                } else {
                    const conflictArea = document.getElementById('recurring-conflict-status');
                    if (conflictArea) {
                        conflictArea.innerHTML = '';
                    }
                }
            });
        }

        if (recurrencePatternSelect && recurrenceEndDateInput) {
            [dateInput, startTimeInput, endTimeInput, recurrencePatternSelect, recurrenceEndDateInput].forEach(input => {
                if (input) {
                    input.addEventListener('change', () => this.debouncedRecurringCheck());
                }
            });
        }

        // Close modal when clicking outside
        window.addEventListener('click', (event) => {
            this.handleOutsideClick(event);
        });
    }

    handleTemplateSelection() {
        const templateSelect = document.getElementById('template');
        const purposeTextarea = document.getElementById('purpose');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const dateInput = document.getElementById('reservation_date');

        if (!templateSelect || !window.reservationTemplates) return;

        const selectedId = templateSelect.value;
        if (selectedId && window.reservationTemplates[selectedId]) {
            const template = window.reservationTemplates[selectedId];
            
            if (purposeTextarea) purposeTextarea.value = template.purpose;
            if (startTimeInput) startTimeInput.value = template.start_time;
            if (endTimeInput) endTimeInput.value = template.end_time;

            // Check for conflicts if all required fields have values
            if (dateInput && dateInput.value && startTimeInput && endTimeInput) {
                this.checkConflicts();
            }
        }
    }    async checkConflicts() {
        const templateSelect = document.getElementById('template');
        const dateInput = document.getElementById('reservation_date');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');

        if (!templateSelect || !window.reservationTemplates || !dateInput || !startTimeInput || !endTimeInput) {
            return;
        }

        const selectedId = templateSelect.value;
        if (!selectedId || !window.reservationTemplates[selectedId] || !dateInput.value || !startTimeInput.value || !endTimeInput.value) {
            return;
        }

        const laboratory_id = window.reservationTemplates[selectedId].laboratory_id;
        
        // Get or create conflict message container
        const conflictArea = this.conflictChecker.getOrCreateConflictContainer(
            'conflict-message', 
            document.querySelector('.grid')
        );

        // Show loading state
        this.conflictChecker.showLoading(conflictArea);

        // Check for conflicts using centralized checker
        const result = await this.conflictChecker.checkReservationConflict({
            laboratory_id: laboratory_id,
            date: dateInput.value,
            start_time: startTimeInput.value,
            end_time: endTimeInput.value
        });

        // Display results
        this.conflictChecker.displayConflictMessage(conflictArea, result);
    }    // Debounced recurring conflict check
    debouncedRecurringCheck() {
        if (this.recurringCheckTimeout) {
            clearTimeout(this.recurringCheckTimeout);
        }
        
        this.recurringCheckTimeout = setTimeout(() => {
            this.checkRecurringConflicts();
        }, 500);
    }

    checkRecurringConflicts() {
        const dateInput = document.getElementById('reservation_date');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const recurrencePatternSelect = document.getElementById('recurrence_pattern');
        const recurrenceEndDateInput = document.getElementById('recurrence_end_date');
        const laboratoryIdInput = document.getElementById('laboratory_id');
        const conflictStatus = document.getElementById('recurring-conflict-status');

        if (!dateInput || !startTimeInput || !endTimeInput || !recurrencePatternSelect || 
            !recurrenceEndDateInput || !laboratoryIdInput || !conflictStatus) {
            return;
        }

        if (!dateInput.value || !startTimeInput.value || !endTimeInput.value || 
            !recurrencePatternSelect.value || !recurrenceEndDateInput.value) {
            conflictStatus.innerHTML = '';
            return;
        }

        conflictStatus.innerHTML = '<p class="text-gray-500">Checking recurring conflicts...</p>';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch('/api/reservation/check-recurring-conflicts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                laboratory_id: laboratoryIdInput.value,
                start_date: dateInput.value,
                end_date: recurrenceEndDateInput.value,
                start_time: startTimeInput.value,
                end_time: endTimeInput.value,
                recurrence_pattern: recurrencePatternSelect.value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.conflicts && data.conflicts.length > 0) {
                    this.displayRecurringConflicts(conflictStatus, data.conflicts);
                } else {
                    conflictStatus.innerHTML = `
                        <div class="p-3 bg-green-50 border border-green-200 rounded-md">
                            <p class="text-green-600">No conflicts found for recurring reservation.</p>
                        </div>
                    `;
                }
            } else {
                conflictStatus.innerHTML = `
                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                        <p class="text-yellow-600">Could not check recurring conflicts.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error checking recurring conflicts:', error);
            conflictStatus.innerHTML = `
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="text-yellow-600">Error checking recurring conflicts.</p>
                </div>
            `;
        });
    }

    displayRecurringConflicts(conflictStatus, conflicts) {
        let html = `
            <div class="p-3 bg-red-50 border border-red-200 rounded-md">
                <p class="font-medium text-red-800">Conflicts found for recurring reservation:</p>
                <div class="mt-2 max-h-40 overflow-y-auto">
        `;
        
        conflicts.forEach(conflict => {
            html += `<p class="text-red-600 text-sm">• ${conflict.date}: ${conflict.description}</p>`;
        });
        
        html += `
                </div>
                <p class="mt-2 text-red-600">Please adjust your reservation details.</p>
            </div>
        `;
        
        conflictStatus.innerHTML = html;
    }

    initDateConstraints() {
        // Set minimum date to today for reservation date inputs
        const dateInputs = document.querySelectorAll('input[type="date"]');
        const today = new Date().toISOString().split('T')[0];
        
        dateInputs.forEach(input => {
            if (!input.min) {
                input.min = today;
            }
        });
    }

    initModalHandlers() {
        // Handle rejection modal close buttons
        document.querySelectorAll('[data-action="close-reject-modal"]').forEach(button => {
            button.addEventListener('click', () => this.closeRejectModal());
        });

        // Handle escape key to close modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeRejectModal();
            }
        });
    }

    openRejectModal(reservationId) {
        const modal = document.getElementById('rejection-modal');
        const form = document.getElementById('reject-form');
        const reasonTextarea = document.getElementById('rejection_reason');

        if (form && reservationId) {
            form.action = `/admin/laboratory/reservations/${reservationId}/reject`;
        }
        if (reasonTextarea) {
            reasonTextarea.value = '';
        }
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    closeRejectModal() {
        const modal = document.getElementById('rejection-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    handleOutsideClick(event) {
        const rejectModal = document.getElementById('rejection-modal');
        
        if (rejectModal && event.target === rejectModal) {
            this.closeRejectModal();
        }
    }    initConflictChecking() {
        // Create debounced functions for conflict checking
        this.debounceTimeout = null;
        this.recurringCheckTimeout = null;
    }

    /**
     * Initialize quick reservation functionality
     */
    initQuickReservation() {
        const templateSelect = document.getElementById('template');
        if (!templateSelect) return;

        const purposeTextarea = document.getElementById('purpose');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const dateInput = document.getElementById('reservation_date');

        if (!purposeTextarea || !startTimeInput || !endTimeInput || !dateInput) {
            console.warn('Quick reservation inputs not found');
            return;
        }

        // Get template data from window (set by Blade template)
        this.reservationTemplates = window.reservationTemplates || {};

        // Update form fields when template is selected
        templateSelect.addEventListener('change', () => {
            const selectedId = templateSelect.value;
            if (selectedId && this.reservationTemplates[selectedId]) {
                const template = this.reservationTemplates[selectedId];
                purposeTextarea.value = template.purpose;
                startTimeInput.value = template.start_time;
                endTimeInput.value = template.end_time;

                // Check for conflicts if date is already selected
                if (dateInput.value) {
                    this.checkQuickReservationConflicts();
                }
            }
        });

        // Add event listeners to check for conflicts when inputs change
        dateInput.addEventListener('change', () => this.checkQuickReservationConflicts());
        startTimeInput.addEventListener('change', () => this.checkQuickReservationConflicts());
        endTimeInput.addEventListener('change', () => this.checkQuickReservationConflicts());

        console.log('Quick reservation initialized');
    }

    /**
     * Check for conflicts in quick reservation
     */
    checkQuickReservationConflicts() {
        const templateSelect = document.getElementById('template');
        const dateInput = document.getElementById('reservation_date');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');

        const selectedId = templateSelect?.value;
        if (!selectedId || !this.reservationTemplates[selectedId] || 
            !dateInput?.value || !startTimeInput?.value || !endTimeInput?.value) {
            return; // Not all fields filled
        }

        const template = this.reservationTemplates[selectedId];
        const laboratory_id = template.laboratory_id;
        const laboratory_name = template.laboratory_name;

        // Create or get conflict message area
        let conflictArea = document.getElementById('conflict-message');
        if (!conflictArea) {
            conflictArea = document.createElement('div');
            conflictArea.id = 'conflict-message';
            conflictArea.className = 'mt-4 text-sm';
            document.querySelector('.grid')?.appendChild(conflictArea);
        }

        // Show loading message
        conflictArea.innerHTML = '<p class="text-gray-500">Checking for conflicts...</p>';

        // Send request to check for conflicts
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch('/api/reservation/check-conflict', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                laboratory_id: laboratory_id,
                date: dateInput.value,
                start_time: startTimeInput.value,
                end_time: endTimeInput.value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.has_conflict) {
                conflictArea.innerHTML = `
                    <div class="p-3 bg-red-50 border border-red-200 rounded-md">
                        <p class="font-medium text-red-800">${data.message}</p>
                        <p class="text-red-600">Laboratory ${laboratory_name} is not available at the selected time.</p>
                    </div>
                `;
            } else {
                conflictArea.innerHTML = `
                    <div class="p-3 bg-green-50 border border-green-200 rounded-md">
                        <p class="font-medium text-green-800">No conflicts found!</p>
                        <p class="text-green-600">Laboratory ${laboratory_name} is available at the selected time.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error checking conflicts:', error);
            conflictArea.innerHTML = `
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="font-medium text-yellow-800">Could not check for conflicts</p>
                    <p class="text-yellow-600">Please proceed with caution.</p>
                </div>
            `;
        });
    }
}

// Global function exposures for backward compatibility
window.openRejectModal = function(reservationId) {
    if (window.reservationManager) {
        window.reservationManager.openRejectModal(reservationId);
    }
};

window.closeRejectModal = function() {
    if (window.reservationManager) {
        window.reservationManager.closeRejectModal();
    }
};

// Initialize when module is imported
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.reservationManager = new ReservationManager();
    });
} else {
    window.reservationManager = new ReservationManager();
}

export default ReservationManager;
