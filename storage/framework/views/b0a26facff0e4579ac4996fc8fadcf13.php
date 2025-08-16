<div class="flex items-center space-x-2">
    <span class="text-sm font-medium text-gray-700" id="view-label">View:</span>
    <div class="flex rounded-md shadow-sm" role="group" aria-labelledby="view-label">
        <button id="calendar-view" 
                type="button"
                role="tab"
                aria-pressed="<?php echo e($defaultView === 'calendar' ? 'true' : 'false'); ?>"
                aria-selected="<?php echo e($defaultView === 'calendar' ? 'true' : 'false'); ?>"
                aria-controls="<?php echo e($calendarContentId); ?>"
                tabindex="<?php echo e($defaultView === 'calendar' ? '0' : '-1'); ?>"
                class="view-toggle-btn px-4 py-2 text-sm font-medium <?php echo e($defaultView === 'calendar' ? 'text-blue-700 bg-blue-50 border-blue-500' : 'text-gray-700 bg-white border-gray-300'); ?> border rounded-l-md hover:bg-blue-100 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors duration-200">
            <?php if($showIcons): ?><i class="fas fa-calendar mr-2" aria-hidden="true"></i><?php endif; ?>
            Calendar
        </button>
        <button id="table-view" 
                type="button"
                role="tab"
                aria-pressed="<?php echo e($defaultView === 'table' ? 'true' : 'false'); ?>"
                aria-selected="<?php echo e($defaultView === 'table' ? 'true' : 'false'); ?>"
                aria-controls="<?php echo e($tableContentId); ?>"
                tabindex="<?php echo e($defaultView === 'table' ? '0' : '-1'); ?>"
                class="view-toggle-btn px-4 py-2 text-sm font-medium <?php echo e($defaultView === 'table' ? 'text-blue-700 bg-blue-50 border-blue-500' : 'text-gray-700 bg-white border-gray-300'); ?> border rounded-r-md hover:bg-gray-50 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none border-l-0 transition-colors duration-200">
            <?php if($showIcons): ?><i class="fas fa-table mr-2" aria-hidden="true"></i><?php endif; ?>
            Table
        </button>
    </div>
</div>

<?php if (! $__env->hasRenderedOnce('3cf04648-1185-4228-896d-1bceedd588ba')): $__env->markAsRenderedOnce('3cf04648-1185-4228-896d-1bceedd588ba');
$__env->startPush('scripts'); ?>
<script>
/**
 * Centralized Calendar Toggle Component
 * Handles view switching between calendar and table views with full accessibility support
 */
class CalendarToggleManager {
    constructor(calendarContentId = 'calendar-content', tableContentId = 'table-content', defaultView = 'calendar') {
        this.calendarContentId = calendarContentId;
        this.tableContentId = tableContentId;
        this.defaultView = defaultView;
        this.initialized = false;
        
        this.init();
    }
    
    init() {
        if (this.initialized) return;
        
        const calendarContent = document.getElementById(this.calendarContentId);
        const tableContent = document.getElementById(this.tableContentId);
        const calendarBtn = document.getElementById('calendar-view');
        const tableBtn = document.getElementById('table-view');
        
        if (!calendarContent || !tableContent || !calendarBtn || !tableBtn) {
            console.warn('Calendar toggle elements not found:', {
                calendarContent: !!calendarContent,
                tableContent: !!tableContent,
                calendarBtn: !!calendarBtn,
                tableBtn: !!tableBtn
            });
            return;
        }
        
        this.calendarContent = calendarContent;
        this.tableContent = tableContent;
        this.calendarBtn = calendarBtn;
        this.tableBtn = tableBtn;
        
        this.bindEvents();
        this.switchView(this.defaultView);
        this.initialized = true;
        
        console.log('Calendar toggle initialized successfully');
    }
    
    bindEvents() {
        // Click event listeners
        this.calendarBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.switchView('calendar');
        });
        
        this.tableBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.switchView('table');
        });
        
        // Keyboard navigation
        this.calendarBtn.addEventListener('keydown', (e) => {
            this.handleKeyNavigation(e, 'calendar');
        });
        
        this.tableBtn.addEventListener('keydown', (e) => {
            this.handleKeyNavigation(e, 'table');
        });
    }
    
    switchView(viewType) {
        try {
            if (viewType === 'calendar') {
                this.showCalendar();
            } else if (viewType === 'table') {
                this.showTable();
            }
            
            this.announceViewChange(viewType);
        } catch (error) {
            console.error('Error switching view:', error);
        }
    }
    
    showCalendar() {
        // Show calendar, hide table
        this.calendarContent.style.display = 'block';
        this.tableContent.style.display = 'none';
        this.calendarContent.classList.remove('hidden');
        this.tableContent.classList.add('hidden');
        
        // Update ARIA attributes
        this.calendarContent.setAttribute('aria-hidden', 'false');
        this.tableContent.setAttribute('aria-hidden', 'true');
        
        // Update button states
        this.setButtonActive(this.calendarBtn, 'left');
        this.setButtonInactive(this.tableBtn, 'right');
    }
    
    showTable() {
        // Show table, hide calendar
        this.calendarContent.style.display = 'none';
        this.tableContent.style.display = 'block';
        this.calendarContent.classList.add('hidden');
        this.tableContent.classList.remove('hidden');
        
        // Update ARIA attributes
        this.calendarContent.setAttribute('aria-hidden', 'true');
        this.tableContent.setAttribute('aria-hidden', 'false');
        
        // Update button states
        this.setButtonActive(this.tableBtn, 'right');
        this.setButtonInactive(this.calendarBtn, 'left');
    }
    
    setButtonActive(button, position) {
        button.setAttribute('aria-pressed', 'true');
        button.setAttribute('aria-selected', 'true');
        button.setAttribute('tabindex', '0');
        
        const roundedClass = position === 'left' ? 'rounded-l-md' : 'rounded-r-md hover:bg-blue-100 border-l-0';
        const borderClass = position === 'left' ? '' : 'border-l-0';
        
        button.className = `view-toggle-btn px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-500 ${roundedClass} hover:bg-blue-100 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none ${borderClass} transition-colors duration-200`;
    }
    
    setButtonInactive(button, position) {
        button.setAttribute('aria-pressed', 'false');
        button.setAttribute('aria-selected', 'false');
        button.setAttribute('tabindex', '-1');
        
        const roundedClass = position === 'left' ? 'rounded-l-md' : 'rounded-r-md border-l-0';
        const hoverClass = position === 'left' ? 'hover:bg-gray-50' : 'hover:bg-gray-50';
        const borderClass = position === 'left' ? '' : 'border-l-0';
        
        button.className = `view-toggle-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 ${roundedClass} ${hoverClass} focus:z-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none ${borderClass} transition-colors duration-200`;
    }
    
    announceViewChange(viewType) {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = `Switched to ${viewType} view`;
        document.body.appendChild(announcement);
        
        setTimeout(() => {
            if (announcement.parentNode) {
                announcement.parentNode.removeChild(announcement);
            }
        }, 1000);
    }
    
    handleKeyNavigation(event, currentView) {
        switch(event.key) {
            case 'ArrowLeft':
            case 'ArrowUp':
                event.preventDefault();
                if (currentView === 'table') {
                    this.switchView('calendar');
                    this.calendarBtn.focus();
                } else {
                    this.switchView('table');
                    this.tableBtn.focus();
                }
                break;
                
            case 'ArrowRight':
            case 'ArrowDown':
                event.preventDefault();
                if (currentView === 'calendar') {
                    this.switchView('table');
                    this.tableBtn.focus();
                } else {
                    this.switchView('calendar');
                    this.calendarBtn.focus();
                }
                break;
                
            case 'Home':
                event.preventDefault();
                this.switchView('calendar');
                this.calendarBtn.focus();
                break;
                
            case 'End':
                event.preventDefault();
                this.switchView('table');
                this.tableBtn.focus();
                break;
                
            case 'Enter':
            case ' ':
                event.preventDefault();
                // Toggle to the other view
                const newView = currentView === 'calendar' ? 'table' : 'calendar';
                this.switchView(newView);
                break;
        }
    }
}

// Initialize calendar toggle when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Create global instance for the default calendar toggle
    window.calendarToggle = new CalendarToggleManager('<?php echo e($calendarContentId); ?>', '<?php echo e($tableContentId); ?>', '<?php echo e($defaultView); ?>');
});

// Expose CalendarToggleManager for custom implementations
window.CalendarToggleManager = CalendarToggleManager;
</script>
<?php $__env->stopPush(); endif; ?>
<?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\components\calendar-toggle.blade.php ENDPATH**/ ?>