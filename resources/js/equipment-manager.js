/**
 * Equipment Management JavaScript
 * Handles equipment borrowing, filtering, modal interactions, and CRUD operations
 */
class EquipmentManager {
    constructor() {
        this.init();
    }

    init() {        document.addEventListener('DOMContentLoaded', () => {
            this.bindEvents();
            this.setupFilters();
            this.setupDateValidation();
            this.setupSearch();
            this.initRequestDateValidation();
        });
    }    bindEvents() {
        // Borrow button event listeners
        document.querySelectorAll('.borrow-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-equipment-id');
                this.openBorrowModal(id);
            });
        });

        // Add equipment button
        document.querySelectorAll('[data-action="open-add-modal"]').forEach(button => {
            button.addEventListener('click', () => this.openAddModal());
        });

        // Edit equipment buttons
        document.querySelectorAll('[data-action="edit-equipment"]').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-equipment-id');
                this.openEditModal(id);
            });
        });

        // Delete equipment buttons
        document.querySelectorAll('[data-action="delete-equipment"]').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-equipment-id');
                this.confirmDelete(id);
            });
        });

        // Close modal buttons with data attributes
        document.querySelectorAll('[data-action="close-modal"]').forEach(button => {
            button.addEventListener('click', () => {
                const targetModal = button.getAttribute('data-target');
                this.closeModal(targetModal);
            });
        });

        // Close modal when clicking outside
        window.addEventListener('click', (event) => {
            this.handleOutsideClick(event);
        });

        // Status filter
        const statusFilter = document.getElementById('status-filter');
        if (statusFilter) {
            statusFilter.addEventListener('change', () => this.handleStatusFilter());
        }
    }

    setupFilters() {
        const searchInput = document.getElementById('search');
        const categoryFilter = document.getElementById('category-filter');
        
        if (searchInput) {
            searchInput.addEventListener('input', () => this.filterEquipment());
        }
        if (categoryFilter) {
            categoryFilter.addEventListener('change', () => this.filterEquipment());
        }
    }

    setupDateValidation() {
        const fromDateInput = document.getElementById('requested_from');
        const untilDateInput = document.getElementById('requested_until');

        if (fromDateInput) {
            fromDateInput.addEventListener('change', () => this.validateDates());
        }
        if (untilDateInput) {
            untilDateInput.addEventListener('change', () => this.validateDates());
        }
    }

    filterEquipment() {
        const searchInput = document.getElementById('search');
        const categoryFilter = document.getElementById('category-filter');
        const equipmentCards = document.querySelectorAll('.grid > div:not(.col-span-full)');
        
        if (!searchInput || !categoryFilter) return;

        const searchTerm = searchInput.value.toLowerCase();
        const categoryId = categoryFilter.value;
        
        equipmentCards.forEach(card => {
            const nameElement = card.querySelector('h3');
            const descriptionElement = card.querySelector('p.text-sm');
            
            if (!nameElement || !descriptionElement) return;

            const name = nameElement.textContent.toLowerCase();
            const description = descriptionElement.textContent.toLowerCase();
            const cardCategoryId = card.getAttribute('data-category-id');
            
            const matchesSearch = name.includes(searchTerm) || description.includes(searchTerm);
            const matchesCategory = categoryId === '' || cardCategoryId === categoryId;
            
            card.style.display = (matchesSearch && matchesCategory) ? 'block' : 'none';
        });
    }

    openBorrowModal(equipmentId) {
        const equipmentIdInput = document.getElementById('equipment_id');
        const modal = document.getElementById('borrowModal');
        const fromInput = document.getElementById('requested_from');
        const untilInput = document.getElementById('requested_until');

        if (equipmentIdInput) {
            equipmentIdInput.value = equipmentId;
        }
        
        if (modal) {
            modal.classList.remove('hidden');
        }
        
        // Set minimum dates for the datetime inputs
        const now = new Date();
        const nowString = now.toISOString().slice(0, 16);
        
        if (fromInput) {
            fromInput.min = nowString;
        }
        if (untilInput) {
            untilInput.min = nowString;
        }
    }

    closeBorrowModal() {
        const modal = document.getElementById('borrowModal');
        const form = document.getElementById('borrowForm');
        
        if (modal) {
            modal.classList.add('hidden');
        }
        if (form) {
            form.reset();
        }
    }    setupSearch() {
        const searchInput = document.getElementById('search');
        if (!searchInput) return;

        let searchTimeout;
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.handleSearch();
            }, 500); // 500ms delay to avoid too many requests
        });

        // Set the search input value from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const searchQuery = urlParams.get('search');
        if (searchQuery) {
            searchInput.value = searchQuery;
        }
    }

    handleSearch() {
        const searchInput = document.getElementById('search');
        const statusFilter = document.getElementById('status-filter');
        
        if (!searchInput) return;

        const searchQuery = searchInput.value.trim();
        const currentStatus = statusFilter ? statusFilter.value : '';
        
        // Build the URL with both search and status filters
        let url = window.location.pathname + '?';
        const params = new URLSearchParams();
        
        if (searchQuery) {
            params.append('search', searchQuery);
        }
        if (currentStatus) {
            params.append('status', currentStatus);
        }
        
        url += params.toString();
        window.location.href = url;
    }    handleStatusFilter() {
        const statusFilter = document.getElementById('status-filter');
        const searchInput = document.getElementById('search');
        
        if (!statusFilter) return;

        const status = statusFilter.value;
        const searchQuery = searchInput ? searchInput.value.trim() : '';
        
        // Build URL with filters
        let url = window.location.pathname + '?';
        const params = new URLSearchParams();
        
        if (status) {
            params.append('status', status);
        }
        if (searchQuery) {
            params.append('search', searchQuery);
        }
        
        url += params.toString();
        window.location.href = url;
    }

    // Equipment CRUD Modal Methods
    openAddModal() {
        const modal = document.getElementById('addModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    closeAddModal() {
        const modal = document.getElementById('addModal');
        const form = document.getElementById('addForm');
        
        if (modal) {
            modal.classList.add('hidden');
        }
        if (form) {
            form.reset();
        }
    }

    async openEditModal(id) {
        const form = document.getElementById('editForm');
        const modal = document.getElementById('editModal');
        
        if (form) {
            form.action = `/admin/equipment/${id}`;
        }
        
        try {
            // Fetch equipment details and populate form
            const response = await fetch(`/api/equipment/${id}`);
            const data = await response.json();
            
            this.populateEditForm(data);
            
            if (modal) {
                modal.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error fetching equipment data:', error);
            alert('Error loading equipment data');
        }
    }    populateEditForm(data) {
        // Handle both index.blade.php and manage.blade.php field naming
        const fieldMappings = [
            { target: 'edit_name', source: 'name' },
            { target: 'edit_description', source: 'description' },
            { target: 'edit_category', source: 'category_id' },
            { target: 'edit_rfid_tag', source: 'rfid_tag' },
            { target: 'edit_rfid', source: 'rfid_tag' }, // Alternative field name in manage.blade.php
            { target: 'edit_location', source: 'location' },
            { target: 'edit_status', source: 'status' }
        ];
        
        fieldMappings.forEach(mapping => {
            const element = document.getElementById(mapping.target);
            if (element && data[mapping.source] !== undefined) {
                element.value = data[mapping.source] || '';
            }
        });
    }

    closeEditModal() {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        
        if (modal) {
            modal.classList.add('hidden');
        }
        if (form) {
            form.reset();
        }
    }

    confirmDelete(id) {
        if (confirm('Are you sure you want to delete this equipment?')) {
            this.deleteEquipment(id);
        }
    }

    deleteEquipment(id) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/equipment/${id}`;
        
        // Add CSRF token and method override
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }

    // Borrowing requests functionality
    openOnsiteBorrowModal() {
        const modal = document.getElementById('onsiteBorrowModal');
        const form = document.getElementById('onsiteBorrowForm');
        
        if (modal) {
            modal.classList.remove('hidden');
        }
        if (form) {
            form.reset();
        }
    }

    closeOnsiteBorrowModal() {
        const modal = document.getElementById('onsiteBorrowModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    openReturnModal(requestId) {
        const modal = document.getElementById('returnModal');
        const form = document.getElementById('returnForm');
        
        if (form && requestId) {
            form.action = `/admin/equipment/requests/${requestId}/return`;
            form.reset();
        }
        
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    closeReturnModal() {
        const modal = document.getElementById('returnModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Date validation for equipment requests
    initRequestDateValidation() {
        const requestedFromInput = document.getElementById('requested_from');
        const requestedUntilInput = document.getElementById('requested_until');
        
        if (requestedFromInput && requestedUntilInput) {
            // Set minimum dates
            const now = new Date();
            const nowString = now.toISOString().slice(0, 16);
            requestedFromInput.min = nowString;
            requestedUntilInput.min = nowString;

            // Add validation listeners
            requestedFromInput.addEventListener('change', this.validateRequestDates.bind(this));
            requestedUntilInput.addEventListener('change', this.validateRequestDates.bind(this));
        }

        // Set minimum for return date in return modal
        const requestedUntilReturnInput = document.getElementById('requested_until');
        if (requestedUntilReturnInput) {
            const today = new Date();
            today.setMinutes(today.getMinutes() - today.getTimezoneOffset());
            requestedUntilReturnInput.min = today.toISOString().slice(0, 16);
        }
    }

    validateRequestDates() {
        const fromDate = document.getElementById('requested_from')?.value;
        const untilDate = document.getElementById('requested_until')?.value;
        
        if (fromDate && untilDate && fromDate >= untilDate) {
            alert('The return date must be after the borrow date.');
            const untilInput = document.getElementById('requested_until');
            if (untilInput) {
                untilInput.value = '';
            }
        }
    }

    // Generic modal methods
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        
        if (modalId === 'borrowModal') {
            this.closeBorrowModal();
        } else if (modalId === 'addModal') {
            this.closeAddModal();
        } else if (modalId === 'editModal') {
            this.closeEditModal();
        } else if (modal) {
            modal.classList.add('hidden');
        }
    }

    handleOutsideClick(event) {
        const modals = ['borrowModal', 'addModal', 'editModal'];
        
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && event.target === modal) {
                this.closeModal(modalId);
            }
        });
    }
}

// Make functions globally available for onclick handlers
window.openBorrowModal = function(equipmentId) {
    if (window.equipmentManager) {
        window.equipmentManager.openBorrowModal(equipmentId);
    }
};

window.closeBorrowModal = function() {
    if (window.equipmentManager) {
        window.equipmentManager.closeBorrowModal();
    }
};

window.openAddModal = function() {
    if (window.equipmentManager) {
        window.equipmentManager.openAddModal();
    }
};

window.closeAddModal = function() {
    if (window.equipmentManager) {
        window.equipmentManager.closeAddModal();
    }
};

window.openEditModal = function(id) {
    if (window.equipmentManager) {
        window.equipmentManager.openEditModal(id);
    }
};

window.closeEditModal = function() {
    if (window.equipmentManager) {
        window.equipmentManager.closeEditModal();
    }
};

window.confirmDelete = function(id) {
    if (window.equipmentManager) {
        window.equipmentManager.confirmDelete(id);
    }
};

// Borrowing request functions
window.openOnsiteBorrowModal = function() {
    if (window.equipmentManager) {
        window.equipmentManager.openOnsiteBorrowModal();
    }
};

window.closeOnsiteBorrowModal = function() {
    if (window.equipmentManager) {
        window.equipmentManager.closeOnsiteBorrowModal();
    }
};

window.openReturnModal = function(requestId) {
    if (window.equipmentManager) {
        window.equipmentManager.openReturnModal(requestId);
    }
};

window.closeReturnModal = function() {
    if (window.equipmentManager) {
        window.equipmentManager.closeReturnModal();
    }
};

// Initialize when module is imported
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.equipmentManager = new EquipmentManager();
    });
} else {
    window.equipmentManager = new EquipmentManager();
}

export default EquipmentManager;
