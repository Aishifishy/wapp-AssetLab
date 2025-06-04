/**
 * Dynamic Alert Helper Functions
 * 
 * Provides JavaScript functions to create alerts dynamically using the same
 * component structure as the Blade components for consistency.
 */

class AlertHelper {
    /**
     * Show a dynamic alert message
     * @param {string} type - Alert type: success, error, warning, info
     * @param {string} message - Alert message content
     * @param {Object} options - Additional options
     */
    static show(type, message, options = {}) {
        const {
            title = null,
            dismissible = true,
            duration = null,
            container = 'body',
            position = 'top-right'
        } = options;

        const alert = this.createElement(type, message, { title, dismissible });
        
        // Add positioning classes based on position
        const positionClasses = this.getPositionClasses(position);
        alert.classList.add(...positionClasses);

        // Add to container
        const containerElement = document.querySelector(container);
        if (containerElement) {
            containerElement.appendChild(alert);
            
            // Auto-dismiss if duration is set
            if (duration && dismissible) {
                setTimeout(() => {
                    this.dismiss(alert);
                }, duration);
            }
        }

        return alert;
    }

    /**
     * Show success alert
     */
    static success(message, options = {}) {
        return this.show('success', message, options);
    }

    /**
     * Show error alert
     */
    static error(message, options = {}) {
        return this.show('error', message, options);
    }

    /**
     * Show warning alert
     */
    static warning(message, options = {}) {
        return this.show('warning', message, options);
    }

    /**
     * Show info alert
     */
    static info(message, options = {}) {
        return this.show('info', message, options);
    }

    /**
     * Create alert element
     */
    static createElement(type, message, options = {}) {
        const { title, dismissible } = options;
        
        // Get type configuration
        const config = this.getTypeConfig(type);
        
        // Create main alert div
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert-component border-l-4 p-4 mb-4 ${config.bgClass} ${config.borderClass} ${config.textClass}`;
        alertDiv.setAttribute('role', 'alert');

        // Create content wrapper
        const contentWrapper = document.createElement('div');
        contentWrapper.className = 'flex';

        // Create icon container
        const iconContainer = document.createElement('div');
        iconContainer.className = 'flex-shrink-0';
        const icon = document.createElement('i');
        icon.className = `${config.icon} mr-3 ${config.iconColor}`;
        iconContainer.appendChild(icon);

        // Create message container
        const messageContainer = document.createElement('div');
        messageContainer.className = 'flex-1';

        if (title) {
            const titleElement = document.createElement('h3');
            titleElement.className = 'text-sm font-medium';
            titleElement.textContent = title;
            messageContainer.appendChild(titleElement);

            const messageElement = document.createElement('div');
            messageElement.className = 'mt-1 text-sm';
            messageElement.textContent = message;
            messageContainer.appendChild(messageElement);
        } else {
            messageContainer.textContent = message;
        }

        // Assemble content
        contentWrapper.appendChild(iconContainer);
        contentWrapper.appendChild(messageContainer);

        // Add dismiss button if dismissible
        if (dismissible) {
            const dismissContainer = document.createElement('div');
            dismissContainer.className = 'flex-shrink-0 ml-3';
            
            const dismissButton = document.createElement('button');
            dismissButton.type = 'button';
            dismissButton.className = `inline-flex rounded-md p-1.5 ${config.textClass} hover:${config.hoverBg} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:${config.focusRing}`;
            dismissButton.innerHTML = `
                <span class="sr-only">Dismiss</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            `;

            dismissButton.addEventListener('click', () => {
                this.dismiss(alertDiv);
            });

            dismissContainer.appendChild(dismissButton);
            contentWrapper.appendChild(dismissContainer);
        }

        alertDiv.appendChild(contentWrapper);
        return alertDiv;
    }

    /**
     * Get type configuration
     */
    static getTypeConfig(type) {
        const configs = {
            success: {
                bgClass: 'bg-green-50',
                borderClass: 'border-green-400',
                textClass: 'text-green-700',
                iconColor: 'text-green-400',
                icon: 'fas fa-check-circle',
                hoverBg: 'bg-green-100',
                focusRing: 'ring-green-600'
            },
            error: {
                bgClass: 'bg-red-50',
                borderClass: 'border-red-400',
                textClass: 'text-red-700',
                iconColor: 'text-red-400',
                icon: 'fas fa-times-circle',
                hoverBg: 'bg-red-100',
                focusRing: 'ring-red-600'
            },
            warning: {
                bgClass: 'bg-yellow-50',
                borderClass: 'border-yellow-400',
                textClass: 'text-yellow-700',
                iconColor: 'text-yellow-400',
                icon: 'fas fa-exclamation-triangle',
                hoverBg: 'bg-yellow-100',
                focusRing: 'ring-yellow-600'
            },
            info: {
                bgClass: 'bg-blue-50',
                borderClass: 'border-blue-400',
                textClass: 'text-blue-700',
                iconColor: 'text-blue-400',
                icon: 'fas fa-info-circle',
                hoverBg: 'bg-blue-100',
                focusRing: 'ring-blue-600'
            }
        };

        return configs[type] || configs.info;
    }

    /**
     * Get position classes
     */
    static getPositionClasses(position) {
        const positions = {
            'top-right': ['fixed', 'top-4', 'right-4', 'z-50', 'max-w-sm'],
            'top-left': ['fixed', 'top-4', 'left-4', 'z-50', 'max-w-sm'],
            'bottom-right': ['fixed', 'bottom-4', 'right-4', 'z-50', 'max-w-sm'],
            'bottom-left': ['fixed', 'bottom-4', 'left-4', 'z-50', 'max-w-sm'],
            'top-center': ['fixed', 'top-4', 'left-1/2', 'transform', '-translate-x-1/2', 'z-50', 'max-w-sm'],
            'bottom-center': ['fixed', 'bottom-4', 'left-1/2', 'transform', '-translate-x-1/2', 'z-50', 'max-w-sm']
        };

        return positions[position] || positions['top-right'];
    }

    /**
     * Dismiss an alert with animation
     */
    static dismiss(alertElement) {
        if (!alertElement || !alertElement.parentNode) return;

        // Add fade-out animation
        alertElement.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
        alertElement.style.opacity = '0';
        alertElement.style.transform = 'translateY(-10px)';

        // Remove element after animation
        setTimeout(() => {
            if (alertElement.parentNode) {
                alertElement.parentNode.removeChild(alertElement);
            }
        }, 300);
    }

    /**
     * Replace JavaScript alert() calls with styled alerts
     */
    static replaceNativeAlert() {
        // Store original alert function
        window.originalAlert = window.alert;
        
        // Override alert function
        window.alert = function(message) {
            AlertHelper.info(message, {
                dismissible: true,
                duration: 5000
            });
        };
    }

    /**
     * Show form validation errors
     */
    static showFormErrors(errors) {
        if (typeof errors === 'object') {
            Object.entries(errors).forEach(([field, messages]) => {
                const fieldMessages = Array.isArray(messages) ? messages : [messages];
                fieldMessages.forEach(message => {
                    this.error(`${field}: ${message}`, { duration: 10000 });
                });
            });
        } else {
            this.error(errors);
        }
    }

    /**
     * Show confirmation dialog with styled alerts
     */
    static confirm(message, options = {}) {
        return new Promise((resolve) => {
            const {
                title = 'Confirmation',
                confirmText = 'Confirm',
                cancelText = 'Cancel',
                type = 'warning'
            } = options;

            // Create confirmation dialog structure
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50';

            const dialog = document.createElement('div');
            dialog.className = 'bg-white rounded-lg max-w-md w-full p-6 mx-4';

            const config = this.getTypeConfig(type);
            
            dialog.innerHTML = `
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="${config.icon} ${config.iconColor} text-2xl"></i>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <h3 class="text-lg font-medium text-gray-900">${title}</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">${message}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex flex-col-reverse sm:flex-row sm:gap-3 sm:justify-end">
                    <button type="button" class="cancel-btn mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        ${cancelText}
                    </button>
                    <button type="button" class="confirm-btn w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        ${confirmText}
                    </button>
                </div>
            `;

            overlay.appendChild(dialog);
            document.body.appendChild(overlay);

            // Handle clicks
            dialog.querySelector('.confirm-btn').addEventListener('click', () => {
                document.body.removeChild(overlay);
                resolve(true);
            });

            dialog.querySelector('.cancel-btn').addEventListener('click', () => {
                document.body.removeChild(overlay);
                resolve(false);
            });

            // Handle overlay click
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    document.body.removeChild(overlay);
                    resolve(false);
                }
            });
        });
    }
}

// Make AlertHelper globally available
window.AlertHelper = AlertHelper;

// Provide shorthand functions
window.showAlert = AlertHelper.show.bind(AlertHelper);
window.showSuccess = AlertHelper.success.bind(AlertHelper);
window.showError = AlertHelper.error.bind(AlertHelper);
window.showWarning = AlertHelper.warning.bind(AlertHelper);
window.showInfo = AlertHelper.info.bind(AlertHelper);

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Optional: Replace native alert function
    // AlertHelper.replaceNativeAlert();
    
    console.log('Alert Helper initialized');
});
