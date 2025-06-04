/**
 * Navigation Manager JavaScript
 * Handles sidebar navigation, submenu states, and active link highlighting
 */
class NavigationManager {
    constructor() {
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.setupActiveLinks();
            this.setupSubmenus();
            this.handleEquipmentSubmenu();
        });
    }

    setupActiveLinks() {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link, .sidebar-link');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && currentPath.includes(href) && href !== '/') {
                link.classList.add('active');
                
                // Also activate parent menu item if this is a submenu link
                const parentMenu = link.closest('.submenu');
                if (parentMenu) {
                    const parentToggle = parentMenu.previousElementSibling;
                    if (parentToggle) {
                        parentToggle.classList.add('active');
                    }
                }
            }
        });
    }

    setupSubmenus() {
        const submenuToggles = document.querySelectorAll('[data-toggle="submenu"]');
        
        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSubmenu(toggle);
            });
        });
    }

    toggleSubmenu(toggle) {
        const targetId = toggle.getAttribute('data-target');
        const submenu = document.getElementById(targetId);
        
        if (submenu) {
            const isOpen = submenu.classList.contains('show');
            
            // Close all other submenus
            document.querySelectorAll('.submenu').forEach(menu => {
                menu.classList.remove('show');
            });
            
            // Toggle current submenu
            if (!isOpen) {
                submenu.classList.add('show');
                toggle.classList.add('active');
            } else {
                toggle.classList.remove('active');
            }
        }
    }

    handleEquipmentSubmenu() {
        // Keep equipment submenu open when on equipment pages
        const currentPath = window.location.pathname;
        
        if (currentPath.includes('/admin/equipment')) {
            const equipmentSubmenu = document.getElementById('equipmentSubmenu');
            if (equipmentSubmenu) {
                equipmentSubmenu.classList.add('show');
                
                // Also activate the equipment toggle
                const equipmentToggle = document.querySelector('[data-target="equipmentSubmenu"]');
                if (equipmentToggle) {
                    equipmentToggle.classList.add('active');
                }
            }
        }

        // Prevent submenu from closing when clicking submenu items
        const submenuLinks = document.querySelectorAll('#equipmentSubmenu .nav-link');
        submenuLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent event from bubbling up
            });
        });
    }

    // Utility method to highlight current page
    highlightCurrentPage() {
        const currentPath = window.location.pathname;
        const breadcrumbs = document.querySelector('.breadcrumbs');
        
        if (breadcrumbs) {
            // Update breadcrumbs based on current path
            this.updateBreadcrumbs(currentPath);
        }
    }

    updateBreadcrumbs(path) {
        // Extract meaningful parts from the path
        const pathParts = path.split('/').filter(part => part !== '');
        const breadcrumbContainer = document.querySelector('.breadcrumbs');
        
        if (!breadcrumbContainer) return;

        // Clear existing breadcrumbs except the first one (Home/Dashboard)
        const existingCrumbs = breadcrumbContainer.querySelectorAll('.breadcrumb-item:not(:first-child)');
        existingCrumbs.forEach(crumb => crumb.remove());

        // Add new breadcrumbs
        pathParts.forEach((part, index) => {
            if (index === 0) return; // Skip 'admin' or 'user'
            
            const breadcrumbItem = document.createElement('li');
            breadcrumbItem.className = 'breadcrumb-item';
            breadcrumbItem.textContent = this.formatBreadcrumbText(part);
            
            breadcrumbContainer.appendChild(breadcrumbItem);
        });
    }

    formatBreadcrumbText(text) {
        // Convert kebab-case or snake_case to Title Case
        return text
            .replace(/[-_]/g, ' ')
            .replace(/\b\w/g, letter => letter.toUpperCase());
    }

    // Mobile menu toggle
    toggleMobileMenu() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        
        if (sidebar) {
            sidebar.classList.toggle('mobile-open');
        }
        
        if (overlay) {
            overlay.classList.toggle('active');
        }
    }

    closeMobileMenu() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        
        if (sidebar) {
            sidebar.classList.remove('mobile-open');
        }
        
        if (overlay) {
            overlay.classList.remove('active');
        }
    }
}

// Make functions globally available
window.toggleMobileMenu = function() {
    if (window.navigationManager) {
        window.navigationManager.toggleMobileMenu();
    }
};

window.closeMobileMenu = function() {
    if (window.navigationManager) {
        window.navigationManager.closeMobileMenu();
    }
};

// Initialize when module is imported
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.navigationManager = new NavigationManager();
    });
} else {
    window.navigationManager = new NavigationManager();
}

export default NavigationManager;
