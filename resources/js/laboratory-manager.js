/**
 * Laboratory Management JavaScript
 * Handles laboratory data tables, modals, and status updates
 */
class LaboratoryManager {
    constructor() {
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initDataTable();
            this.bindEvents();
        });
    }

    initDataTable() {
        const table = document.getElementById('laboratoriesTable');
        if (!table) return;

        // Check if jQuery and DataTables are available
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $(table).DataTable({
                order: [[1, 'asc']],
                pageLength: 10,
                responsive: true,
                dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
                language: {
                    search: "",
                    searchPlaceholder: "Search...",
                    lengthMenu: "_MENU_ per page",
                },
                drawCallback: function() {
                    // Add Tailwind classes to DataTables elements
                    $('.dataTables_length select').addClass('pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md');
                    $('.dataTables_filter input').addClass('pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md');
                    $('.paginate_button').addClass('px-3 py-1 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200');
                    $('.paginate_button.current').addClass('bg-blue-600 text-white hover:bg-blue-700');
                }
            });
        }
    }

    bindEvents() {
        // Modal trigger buttons
        document.querySelectorAll('[data-modal-target]').forEach(button => {
            button.addEventListener('click', () => {
                const modalId = button.getAttribute('data-modal-target');
                this.openModal(modalId);
            });
        });

        // Modal close buttons
        document.querySelectorAll('[data-modal-close]').forEach(button => {
            button.addEventListener('click', () => {
                this.closeModal(button);
            });
        });

        // Close modal when clicking outside
        window.addEventListener('click', (event) => {
            if (event.target.classList.contains('fixed') && event.target.classList.contains('inset-0')) {
                this.closeModalByElement(event.target);
            }
        });

        // Handle Escape key to close modals
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                this.closeAllModals();
            }
        });        // Notification close buttons
        document.querySelectorAll('[data-action="close-notification"]').forEach(button => {
            button.addEventListener('click', () => {
                button.parentElement.remove();
            });
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            // Focus on the first input in the modal
            const firstInput = modal.querySelector('input, select, textarea, button');
            if (firstInput) {
                firstInput.focus();
            }
        }
    }

    closeModal(button) {
        const modal = button.closest('.fixed');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    closeModalByElement(modalElement) {
        modalElement.classList.add('hidden');
    }

    closeAllModals() {
        document.querySelectorAll('.fixed.inset-0:not(.hidden)').forEach(modal => {
            modal.classList.add('hidden');
        });
    }
}

// Initialize when module is imported
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.laboratoryManager = new LaboratoryManager();
    });
} else {
    window.laboratoryManager = new LaboratoryManager();
}

export default LaboratoryManager;
