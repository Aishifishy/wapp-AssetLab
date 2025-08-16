/**
 * ResourEase Common Utilities
 * Reusable JavaScript functions for the entire application
 */

// =====================================
// DOM UTILITIES
// =====================================

/**
 * Safely get element by ID
 */
function getElement(id) {
    return document.getElementById(id);
}

/**
 * Safely get elements by selector
 */
function getElements(selector) {
    return document.querySelectorAll(selector);
}

/**
 * Add event listener with error handling
 */
function addListener(element, event, callback) {
    if (element && typeof callback === 'function') {
        element.addEventListener(event, callback);
    }
}

/**
 * Remove all children from element
 */
function clearElement(element) {
    if (element) {
        element.innerHTML = '';
    }
}

/**
 * Toggle element visibility
 */
function toggleElement(element, show = null) {
    if (!element) return;
    
    if (show === null) {
        element.classList.toggle('hidden');
    } else {
        element.classList.toggle('hidden', !show);
    }
}

/**
 * Show/Hide loading state
 */
function toggleLoading(element, show = true) {
    if (!element) return;
    
    if (show) {
        element.classList.remove('hidden');
    } else {
        element.classList.add('hidden');
    }
}

// =====================================
// TABLE UTILITIES
// =====================================

/**
 * Generic table filter function
 */
function filterTable(tableSelector, searchValue, filterCriteria = {}) {
    const rows = getElements(`${tableSelector} tbody tr`);
    const searchTerm = searchValue.toLowerCase();
    
    rows.forEach(row => {
        const searchData = row.dataset.search?.toLowerCase() || '';
        let matchesSearch = searchData.includes(searchTerm);
        let matchesFilters = true;
        
        // Apply additional filter criteria
        for (const [key, value] of Object.entries(filterCriteria)) {
            if (value && row.dataset[key] !== value) {
                matchesFilters = false;
                break;
            }
        }
        
        const shouldShow = matchesSearch && matchesFilters;
        row.style.display = shouldShow ? '' : 'none';
    });
}

/**
 * Generic table sorting function
 */
function sortTable(tableSelector, sortKey, direction = 'asc') {
    const tbody = document.querySelector(`${tableSelector} tbody`);
    if (!tbody) return;
    
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        let aVal = a.dataset[sortKey] || '';
        let bVal = b.dataset[sortKey] || '';
        
        // Handle different data types
        if (!isNaN(aVal) && !isNaN(bVal)) {
            aVal = parseFloat(aVal);
            bVal = parseFloat(bVal);
            return direction === 'asc' ? aVal - bVal : bVal - aVal;
        }
        
        if (direction === 'asc') {
            return aVal.localeCompare(bVal);
        } else {
            return bVal.localeCompare(aVal);
        }
    });
    
    // Re-append sorted rows
    rows.forEach(row => tbody.appendChild(row));
}

/**
 * Initialize sortable table headers
 */
function initSortableTable(tableSelector) {
    const headers = getElements(`${tableSelector} th[data-sort]`);
    const sortState = {};
    
    headers.forEach(header => {
        const sortKey = header.dataset.sort;
        sortState[sortKey] = 'asc';
        
        addListener(header, 'click', () => {
            // Toggle sort direction
            sortState[sortKey] = sortState[sortKey] === 'asc' ? 'desc' : 'asc';
            
            // Update visual indicators
            updateSortIndicators(tableSelector, sortKey, sortState[sortKey]);
            
            // Sort table
            sortTable(tableSelector, sortKey, sortState[sortKey]);
        });
    });
}

/**
 * Update sort direction indicators
 */
function updateSortIndicators(tableSelector, activeKey, direction) {
    const headers = getElements(`${tableSelector} th[data-sort]`);
    
    headers.forEach(header => {
        const icon = header.querySelector('i');
        if (!icon) return;
        
        icon.className = 'fas ml-2 text-gray-400';
        
        if (header.dataset.sort === activeKey) {
            icon.className += direction === 'asc' ? ' fa-sort-up' : ' fa-sort-down';
        } else {
            icon.className += ' fa-sort';
        }
    });
}

// =====================================
// MODAL UTILITIES
// =====================================

/**
 * Generic modal handler
 */
class ModalManager {
    constructor() {
        this.init();
    }
    
    init() {
        // Handle modal triggers
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-modal-target]');
            if (trigger) {
                const modalId = trigger.dataset.modalTarget;
                this.showModal(modalId);
            }
            
            const closer = e.target.closest('[data-modal-close]');
            if (closer) {
                const modal = closer.closest('[role="dialog"]');
                if (modal) {
                    this.hideModal(modal.id);
                }
            }
        });
        
        // Close modal on outside click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay')) {
                const modal = e.target.querySelector('[role="dialog"]');
                if (modal) {
                    this.hideModal(modal.id);
                }
            }
        });
        
        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('[role="dialog"]:not(.hidden)');
                if (openModal) {
                    this.hideModal(openModal.id);
                }
            }
        });
    }
    
    showModal(modalId) {
        const modal = getElement(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }
    
    hideModal(modalId) {
        const modal = getElement(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }
}

// =====================================
// FORM UTILITIES
// =====================================

/**
 * Debounce function for form inputs
 */
function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            timeout = null;
            if (!immediate) func(...args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func(...args);
    };
}

/**
 * Validate form field
 */
function validateField(field, rules = {}) {
    const value = field.value.trim();
    const errors = [];
    
    if (rules.required && !value) {
        errors.push('This field is required');
    }
    
    if (rules.minLength && value.length < rules.minLength) {
        errors.push(`Minimum length is ${rules.minLength} characters`);
    }
    
    if (rules.maxLength && value.length > rules.maxLength) {
        errors.push(`Maximum length is ${rules.maxLength} characters`);
    }
    
    if (rules.email && value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
        errors.push('Please enter a valid email address');
    }
    
    return errors;
}

/**
 * Display field validation errors
 */
function showFieldErrors(field, errors) {
    // Remove existing error messages
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error messages
    if (errors.length > 0) {
        field.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
        field.classList.remove('border-gray-300', 'focus:border-blue-500', 'focus:ring-blue-500');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error mt-1 text-sm text-red-600';
        errorDiv.textContent = errors[0];
        field.parentNode.appendChild(errorDiv);
    } else {
        field.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
        field.classList.add('border-gray-300', 'focus:border-blue-500', 'focus:ring-blue-500');
    }
}

// =====================================
// API UTILITIES
// =====================================

/**
 * Generic fetch wrapper with error handling
 */
async function apiRequest(url, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
    };
    
    const config = { ...defaultOptions, ...options };
    if (config.headers && options.headers) {
        config.headers = { ...defaultOptions.headers, ...options.headers };
    }
    
    try {
        const response = await fetch(url, config);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return await response.json();
        }
        
        return await response.text();
    } catch (error) {
        console.error('API request failed:', error);
        throw error;
    }
}

/**
 * Show notification message
 */
function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg transition-all duration-300 transform translate-x-full`;
    
    const typeClasses = {
        success: 'bg-green-100 text-green-800 border border-green-200',
        error: 'bg-red-100 text-red-800 border border-red-200',
        warning: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        info: 'bg-blue-100 text-blue-800 border border-blue-200'
    };
    
    notification.className += ` ${typeClasses[type] || typeClasses.info}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, duration);
}

// =====================================
// CONFIRMATION DIALOGS
// =====================================

/**
 * Initialize confirmation dialogs
 */
function initConfirmationDialogs() {
    document.addEventListener('click', (e) => {
        const element = e.target.closest('.confirm-action');
        if (element) {
            e.preventDefault();
            
            const message = element.dataset.confirmMessage || 'Are you sure?';
            
            if (confirm(message)) {
                if (element.tagName === 'BUTTON' && element.form) {
                    element.form.submit();
                } else if (element.href) {
                    window.location.href = element.href;
                }
            }
        }
    });
}

// =====================================
// INITIALIZATION
// =====================================

/**
 * Initialize common utilities when DOM is ready
 */
document.addEventListener('DOMContentLoaded', () => {
    // Initialize modal manager
    new ModalManager();
    
    // Initialize confirmation dialogs
    initConfirmationDialogs();
    
    // Initialize all sortable tables
    const tables = getElements('table[data-sortable="true"]');
    tables.forEach(table => {
        const tableSelector = `#${table.id}` || '.sortable-table';
        initSortableTable(tableSelector);
    });
});

// Export utilities for use in other scripts
window.ResourEaseUtils = {
    getElement,
    getElements,
    addListener,
    clearElement,
    toggleElement,
    toggleLoading,
    filterTable,
    sortTable,
    initSortableTable,
    ModalManager,
    debounce,
    validateField,
    showFieldErrors,
    apiRequest,
    showNotification
};
