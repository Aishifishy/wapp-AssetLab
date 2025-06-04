/**
 * Modal Management JavaScript
 * Handles generic modal interactions and form submissions
 */
class ModalManager {
    constructor() {
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.bindGlobalEvents();
        });
    }    bindGlobalEvents() {
        // Global click outside to close modals
        window.addEventListener('click', (event) => {
            const modals = document.querySelectorAll('[id$="Modal"]');
            modals.forEach(modal => {
                if (event.target === modal) {
                    this.closeModal(modal.id);
                }
            });
        });

        // Escape key to close modals
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                const openModals = document.querySelectorAll('[id$="Modal"]:not(.hidden)');
                openModals.forEach(modal => {
                    this.closeModal(modal.id);
                });
            }
        });

        // Handle confirmation actions
        document.addEventListener('click', (event) => {
            if (event.target.classList.contains('confirm-action')) {
                const confirmMessage = event.target.getAttribute('data-confirm-message') || 'Are you sure?';
                if (!confirm(confirmMessage)) {
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }
            }
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // Restore scrolling
            
            // Reset form if it exists
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
            }
        }
    }

    confirmAction(message = 'Are you sure?') {
        return confirm(message);
    }

    confirmDelete(itemName = 'this item') {
        return confirm(`Are you sure you want to delete ${itemName}?`);
    }

    confirmCancel(actionName = 'this action') {
        return confirm(`Are you sure you want to cancel ${actionName}?`);
    }
}

// Make functions globally available
window.openModal = function(modalId) {
    if (window.modalManager) {
        window.modalManager.openModal(modalId);
    }
};

window.closeModal = function(modalId) {
    if (window.modalManager) {
        window.modalManager.closeModal(modalId);
    }
};

window.confirmAction = function(message) {
    return window.modalManager ? window.modalManager.confirmAction(message) : confirm(message);
};

window.confirmDelete = function(itemName) {
    return window.modalManager ? window.modalManager.confirmDelete(itemName) : confirm('Are you sure?');
};

window.confirmCancel = function(actionName) {
    return window.modalManager ? window.modalManager.confirmCancel(actionName) : confirm('Are you sure?');
};

// Initialize when module is imported
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.modalManager = new ModalManager();
    });
} else {
    window.modalManager = new ModalManager();
}

export default ModalManager;
