/**
 * Unified Conflict Checker Utility
 * Centralizes all conflict checking logic for reservations and schedules
 */
class ConflictChecker {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.cache = new Map(); // Add caching for repeated requests
        this.lastRequestTime = 0;
        this.debounceDelay = 300; // ms
    }

    /**
     * Check for reservation conflicts
     * @param {Object} params - The conflict check parameters
     * @param {number} params.laboratory_id - Laboratory ID
     * @param {string} params.date - Date in YYYY-MM-DD format
     * @param {string} params.start_time - Start time in HH:mm format
     * @param {string} params.end_time - End time in HH:mm format
     * @param {number|null} params.reservation_id - Reservation ID to exclude (for updates)
     * @returns {Promise<Object>} Conflict check result
     */
    async checkReservationConflict(params) {
        if (!this.validateTimeInputs(params.start_time, params.end_time)) {
            return {
                success: false,
                error: 'End time must be after start time'
            };
        }

        // Check cache first
        const cacheKey = this.generateCacheKey('single', params);
        if (this.cache.has(cacheKey)) {
            return this.cache.get(cacheKey);
        }

        // Debounce rapid requests
        const now = Date.now();
        if (now - this.lastRequestTime < this.debounceDelay) {
            return new Promise(resolve => {
                setTimeout(() => {
                    resolve(this.checkReservationConflict(params));
                }, this.debounceDelay);
            });
        }
        this.lastRequestTime = now;

        try {
            const response = await fetch('/api/reservation/check-conflict', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify({
                    laboratory_id: params.laboratory_id,
                    date: params.date,
                    start_time: params.start_time,
                    end_time: params.end_time,
                    reservation_id: params.reservation_id || null
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            // Cache successful results for 30 seconds
            if (result.success) {
                this.cache.set(cacheKey, result);
                setTimeout(() => this.cache.delete(cacheKey), 30000);
            }
            
            return result;
        } catch (error) {
            console.error('Conflict check failed:', error);
            return {
                success: false,
                error: 'Failed to check for conflicts. Please try again.'
            };
        }
    }

    /**
     * Check for recurring reservation conflicts
     * @param {Object} params - The recurring conflict check parameters
     * @returns {Promise<Object>} Conflict check result
     */
    async checkRecurringConflicts(params) {
        try {
            const response = await fetch('/api/reservation/check-recurring-conflicts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify(params)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Recurring conflict check failed:', error);
            return {
                success: false,
                error: 'Failed to check for recurring conflicts. Please try again.'
            };
        }
    }

    /**
     * Display conflict message in the UI
     * @param {HTMLElement} container - Container element to display message
     * @param {Object} result - Conflict check result
     */
    displayConflictMessage(container, result) {
        if (!container) return;

        if (!result.success) {
            container.innerHTML = `<div class="text-red-600 bg-red-50 border border-red-200 rounded p-3">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                ${result.error || 'An error occurred while checking for conflicts.'}
            </div>`;
            return;
        }

        if (result.has_conflict) {
            const conflictTypeIcons = {
                'single_reservation': 'fa-calendar-times',
                'recurring_reservation': 'fa-repeat',
                'class_schedule': 'fa-chalkboard-teacher'
            };
            
            const icon = conflictTypeIcons[result.conflict_type] || 'fa-exclamation-triangle';
            
            container.innerHTML = `<div class="text-red-600 bg-red-50 border border-red-200 rounded p-3">
                <i class="fas ${icon} mr-2"></i>
                ${result.message}
            </div>`;
        } else {
            container.innerHTML = `<div class="text-green-600 bg-green-50 border border-green-200 rounded p-3">
                <i class="fas fa-check-circle mr-2"></i>
                No conflicts found. This time slot is available.
            </div>`;
        }
    }

    /**
     * Validate time inputs
     * @param {string} startTime - Start time in HH:mm format
     * @param {string} endTime - End time in HH:mm format
     * @returns {boolean} True if valid
     */
    validateTimeInputs(startTime, endTime) {
        if (!startTime || !endTime) return false;
        
        const start = new Date(`2000-01-01T${startTime}:00`);
        const end = new Date(`2000-01-01T${endTime}:00`);
        
        return end > start;
    }

    /**
     * Create or get conflict message container
     * @param {string} containerId - ID for the container
     * @param {HTMLElement} insertAfter - Element to insert after
     * @returns {HTMLElement} Conflict message container
     */
    getOrCreateConflictContainer(containerId, insertAfter) {
        let container = document.getElementById(containerId);
        
        if (!container) {
            container = document.createElement('div');
            container.id = containerId;
            container.className = 'mt-4';
            
            if (insertAfter && insertAfter.parentNode) {
                insertAfter.parentNode.insertBefore(container, insertAfter.nextSibling);
            }
        }
        
        return container;
    }

    /**
     * Show loading state
     * @param {HTMLElement} container - Container to show loading in
     */
    showLoading(container) {
        if (container) {
            container.innerHTML = `<div class="text-gray-500 bg-gray-50 border border-gray-200 rounded p-3">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                Checking for conflicts...
            </div>`;
        }
    }

    /**
     * Generate cache key for requests
     */
    generateCacheKey(type, params) {
        return `${type}_${JSON.stringify(params)}`;
    }

    /**
     * Clear the request cache
     */
    clearCache() {
        this.cache.clear();
    }
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ConflictChecker;
} else {
    window.ConflictChecker = ConflictChecker;
}
