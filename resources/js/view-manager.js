/**
 * View Manager - Handles view switching and toggle functionality
 */
class ViewManager {
    constructor() {
        this.currentView = 'calendar';
        this.initialized = false;
    }

    /**
     * Initialize schedule toggle functionality
     */
    initScheduleToggle() {
        console.log('Initializing schedule toggle...');
        
        const calendarContent = document.getElementById('calendar-content');
        const tableContent = document.getElementById('table-content');
        const calendarBtn = document.getElementById('calendar-view');
        const tableBtn = document.getElementById('table-view');
        
        if (!calendarContent || !tableContent || !calendarBtn || !tableBtn) {
            console.error('Required elements not found:', {
                calendarContent: !!calendarContent,
                tableContent: !!tableContent,
                calendarBtn: !!calendarBtn,
                tableBtn: !!tableBtn
            });
            return false;
        }
        
        console.log('All elements found, setting up toggle...');
        
        // Add click event listeners
        calendarBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Calendar button clicked');
            this.switchView('calendar');
        });
        
        tableBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Table button clicked');
            this.switchView('table');
        });
        
        // Add keyboard event listeners
        calendarBtn.addEventListener('keydown', (e) => {
            this.handleKeyNavigation(e, 'calendar');
        });
        
        tableBtn.addEventListener('keydown', (e) => {
            this.handleKeyNavigation(e, 'table');
        });
        
        // Set default view to calendar
        this.switchView('calendar');
        
        // Mark as initialized
        calendarBtn.dataset.initialized = 'true';
        this.initialized = true;
        
        console.log('Schedule toggle initialized successfully');
        return true;
    }

    /**
     * Switch between calendar and table views
     * @param {string} viewType - 'calendar' or 'table'
     */
    switchView(viewType) {
        console.log('Switching to view:', viewType);
        
        const calendarContent = document.getElementById('calendar-content');
        const tableContent = document.getElementById('table-content');
        const calendarBtn = document.getElementById('calendar-view');
        const tableBtn = document.getElementById('table-view');
        
        if (!calendarContent || !tableContent || !calendarBtn || !tableBtn) {
            console.error('Cannot switch view: elements not found');
            return;
        }
        
        try {
            if (viewType === 'calendar') {
                // Show calendar, hide table
                calendarContent.style.display = 'block';
                tableContent.style.display = 'none';
                calendarContent.classList.remove('hidden');
                tableContent.classList.add('hidden');
                
                // Update ARIA attributes
                calendarContent.setAttribute('aria-hidden', 'false');
                tableContent.setAttribute('aria-hidden', 'true');
                
                // Update calendar button - active state
                this.setButtonActive(calendarBtn);
                this.setButtonInactive(tableBtn);
                
            } else if (viewType === 'table') {
                // Show table, hide calendar
                calendarContent.style.display = 'none';
                tableContent.style.display = 'block';
                calendarContent.classList.add('hidden');
                tableContent.classList.remove('hidden');
                
                // Update ARIA attributes
                calendarContent.setAttribute('aria-hidden', 'true');
                tableContent.setAttribute('aria-hidden', 'false');
                
                // Update table button - active state
                this.setButtonActive(tableBtn);
                this.setButtonInactive(calendarBtn);
            }
            
            this.currentView = viewType;
            this.announceViewChange(viewType);
            
            console.log('View switched successfully to:', viewType);
        } catch (error) {
            console.error('Error switching view:', error);
        }
    }

    /**
     * Set button to active state
     * @param {HTMLElement} button 
     */
    setButtonActive(button) {
        button.setAttribute('aria-pressed', 'true');
        button.setAttribute('aria-selected', 'true');
        button.setAttribute('tabindex', '0');
        
        // Update classes based on button position
        if (button.id === 'calendar-view') {
            button.className = 'view-toggle-btn px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-500 rounded-l-md hover:bg-blue-100 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors duration-200';
        } else {
            button.className = 'view-toggle-btn px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-500 rounded-r-md hover:bg-blue-100 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none border-l-0 transition-colors duration-200';
        }
    }

    /**
     * Set button to inactive state
     * @param {HTMLElement} button 
     */
    setButtonInactive(button) {
        button.setAttribute('aria-pressed', 'false');
        button.setAttribute('aria-selected', 'false');
        button.setAttribute('tabindex', '-1');
        
        // Update classes based on button position
        if (button.id === 'calendar-view') {
            button.className = 'view-toggle-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors duration-200';
        } else {
            button.className = 'view-toggle-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none border-l-0 transition-colors duration-200';
        }
    }

    /**
     * Handle keyboard navigation for view toggle
     * @param {KeyboardEvent} event 
     * @param {string} currentView 
     */
    handleKeyNavigation(event, currentView) {
        const calendarBtn = document.getElementById('calendar-view');
        const tableBtn = document.getElementById('table-view');
        
        switch(event.key) {
            case 'ArrowLeft':
            case 'ArrowUp':
                event.preventDefault();
                if (currentView === 'table') {
                    this.switchView('calendar');
                    calendarBtn?.focus();
                } else {
                    this.switchView('table');
                    tableBtn?.focus();
                }
                break;
                
            case 'ArrowRight':
            case 'ArrowDown':
                event.preventDefault();
                if (currentView === 'calendar') {
                    this.switchView('table');
                    tableBtn?.focus();
                } else {
                    this.switchView('calendar');
                    calendarBtn?.focus();
                }
                break;
                
            case 'Home':
                event.preventDefault();
                this.switchView('calendar');
                calendarBtn?.focus();
                break;
                
            case 'End':
                event.preventDefault();
                this.switchView('table');
                tableBtn?.focus();
                break;
                
            case 'Enter':
            case ' ':
                event.preventDefault();
                // Current button is already focused, just activate it
                break;
        }
    }

    /**
     * Announce view change to screen readers
     * @param {string} viewType 
     */
    announceViewChange(viewType) {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = `Switched to ${viewType} view`;
        document.body.appendChild(announcement);
        
        // Remove the announcement after it's been read
        setTimeout(() => {
            if (announcement.parentNode) {
                announcement.parentNode.removeChild(announcement);
            }
        }, 1000);
    }

    /**
     * Initialize with multiple fallback methods for compatibility
     */
    init() {
        const initFn = () => {
            const calendarBtn = document.getElementById('calendar-view');
            if (calendarBtn && !calendarBtn.dataset.initialized && !this.initialized) {
                this.initScheduleToggle();
            }
        };

        // Multiple initialization methods for compatibility
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFn);
        } else {
            // DOM is already ready
            initFn();
        }
        
        // Backup initialization when window loads
        window.addEventListener('load', () => {
            // Small delay to ensure all scripts are loaded
            setTimeout(initFn, 100);
        });
        
        // Immediate execution as final fallback
        setTimeout(initFn, 50);
    }
}

// Create and export instance
const viewManager = new ViewManager();

// Initialize on module load
viewManager.init();

export default viewManager;
