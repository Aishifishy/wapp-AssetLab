/**
 * Calendar View Management
 * Handles switching between tabular and calendar views
 */
class CalendarViewManager {
    constructor() {
        this.init();
    }

    init() {
        // Set default view to tabular when page loads
        document.addEventListener('DOMContentLoaded', () => {
            this.switchView('tabular');
            this.bindEvents();
        });
    }    bindEvents() {
        // Bind delete confirmation events using data attributes
        document.querySelectorAll('.delete-schedule-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const confirmMessage = button.getAttribute('data-confirm-message') || 'Are you sure?';
                const formId = button.getAttribute('data-form-id');
                
                if (confirm(confirmMessage)) {
                    const form = document.getElementById(formId);
                    if (form) {
                        form.submit();
                    }
                }
            });
        });

        // Bind view switch buttons using data attributes
        document.querySelectorAll('[data-view-type]').forEach(button => {
            button.addEventListener('click', () => {
                const viewType = button.getAttribute('data-view-type');
                this.switchView(viewType);
            });
        });
    }

    switchView(viewType) {
        const tabularContent = document.getElementById('tabular-content');
        const calendarContent = document.getElementById('calendar-content');
        const tabularBtn = document.getElementById('tabular-view');
        const calendarBtn = document.getElementById('calendar-view');

        if (!tabularContent || !calendarContent || !tabularBtn || !calendarBtn) {
            return;
        }

        if (viewType === 'tabular') {
            tabularContent.classList.remove('hidden');
            calendarContent.classList.add('hidden');
            
            tabularBtn.classList.add('bg-blue-50', 'text-blue-700', 'border-blue-500');
            tabularBtn.classList.remove('bg-white', 'text-gray-700');
            
            calendarBtn.classList.add('bg-white', 'text-gray-700');
            calendarBtn.classList.remove('bg-blue-50', 'text-blue-700', 'border-blue-500');
        } else {
            tabularContent.classList.add('hidden');
            calendarContent.classList.remove('hidden');
            
            calendarBtn.classList.add('bg-blue-50', 'text-blue-700', 'border-blue-500');
            calendarBtn.classList.remove('bg-white', 'text-gray-700');
            
            tabularBtn.classList.add('bg-white', 'text-gray-700');
            tabularBtn.classList.remove('bg-blue-50', 'text-blue-700', 'border-blue-500');
        }
    }

    scrollToLaboratory(elementId) {
        // Switch to calendar view first
        this.switchView('calendar');
        
        // Scroll to specific laboratory
        const element = document.getElementById(elementId);
        if (element) {
            element.scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
}

// Make functions globally available for onclick handlers
window.switchView = function(viewType) {
    if (window.calendarViewManager) {
        window.calendarViewManager.switchView(viewType);
    }
};

window.scrollToLaboratory = function(elementId) {
    if (window.calendarViewManager) {
        window.calendarViewManager.scrollToLaboratory(elementId);
    }
};

// Initialize when module is imported
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.calendarViewManager = new CalendarViewManager();
    });
} else {
    window.calendarViewManager = new CalendarViewManager();
}

export default CalendarViewManager;
