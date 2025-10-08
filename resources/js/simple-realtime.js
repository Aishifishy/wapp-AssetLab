/**
 * Simple Real-time Manager
 * 
 * A lightweight solution for real-time updates using AJAX polling
 * instead of complex WebSocket connections.
 */

class SimpleRealTime {
    constructor(options = {}) {
        this.options = {
            pollInterval: options.pollInterval || 30000, // 30 seconds
            maxRetries: options.maxRetries || 3,
            retryDelay: options.retryDelay || 5000, // 5 seconds
            enableNotifications: options.enableNotifications || true,
            debug: options.debug || false,
            ...options
        };
        
        this.isPolling = false;
        this.pollTimer = null;
        this.lastUpdate = null;
        this.retryCount = 0;
        this.callbacks = {};
        this.isVisible = true;
        
        this.init();
    }
    
    init() {
        this.setupVisibilityHandler();
        this.setupNetworkStatus();
        
        if (this.options.debug) {
            console.log('SimpleRealTime initialized', this.options);
        }
    }
    
    // Handle page visibility changes to pause/resume polling
    setupVisibilityHandler() {
        document.addEventListener('visibilitychange', () => {
            this.isVisible = !document.hidden;
            
            if (this.isVisible && this.isPolling) {
                this.log('Page visible - resuming real-time updates');
                this.resumePolling();
            } else if (!this.isVisible && this.isPolling) {
                this.log('Page hidden - pausing real-time updates');
                this.pausePolling();
            }
        });
    }
    
    // Handle network status changes
    setupNetworkStatus() {
        window.addEventListener('online', () => {
            this.log('Network back online - resuming updates');
            if (this.isPolling) {
                this.resumePolling();
            }
        });
        
        window.addEventListener('offline', () => {
            this.log('Network offline - pausing updates');
            this.pausePolling();
        });
    }
    
    // Start polling for updates
    start(endpoint, options = {}) {
        if (this.isPolling) {
            this.stop();
        }
        
        this.endpoint = endpoint;
        this.pollOptions = options;
        this.isPolling = true;
        this.retryCount = 0;
        
        this.log('Starting real-time polling', { endpoint, options });
        
        // Do initial poll immediately
        this.poll();
        
        // Set up regular polling
        this.pollTimer = setInterval(() => {
            if (this.isVisible && navigator.onLine) {
                this.poll();
            }
        }, this.options.pollInterval);
        
        return this;
    }
    
    // Stop polling
    stop() {
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
            this.pollTimer = null;
        }
        
        this.isPolling = false;
        this.log('Stopped real-time polling');
        
        return this;
    }
    
    // Pause polling temporarily
    pausePolling() {
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
            this.pollTimer = null;
        }
    }
    
    // Resume polling
    resumePolling() {
        if (this.isPolling && !this.pollTimer) {
            this.pollTimer = setInterval(() => {
                if (this.isVisible && navigator.onLine) {
                    this.poll();
                }
            }, this.options.pollInterval);
        }
    }
    
    // Perform the actual polling request
    async poll() {
        if (!this.endpoint) return;
        
        try {
            const params = new URLSearchParams({
                ...this.pollOptions,
                last_update: this.lastUpdate || ''
            });
            
            const response = await fetch(`${this.endpoint}?${params}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.handleUpdate(data);
                this.lastUpdate = data.timestamp;
                this.retryCount = 0; // Reset retry count on success
                
                // Update status indicator
                this.updateStatusIndicator('connected');
            } else {
                throw new Error(data.message || 'Unknown error');
            }
            
        } catch (error) {
            this.handleError(error);
        }
    }
    
    // Handle successful update
    handleUpdate(data) {
        this.log('Received update', data);
        
        // Trigger callbacks
        this.trigger('update', data);
        
        // Handle equipment updates
        if (data.equipment && data.equipment.length > 0) {
            this.trigger('equipmentUpdate', data.equipment);
            this.updateEquipmentStatuses(data.equipment);
        }
        
        // Handle notifications
        if (data.recent_requests > 0) {
            this.trigger('notification', {
                type: 'equipment_requests',
                count: data.recent_requests,
                message: `You have ${data.recent_requests} recent equipment request(s)`
            });
        }
    }
    
    // Handle polling errors
    handleError(error) {
        this.log('Polling error', error);
        
        this.retryCount++;
        this.updateStatusIndicator('error');
        
        if (this.retryCount <= this.options.maxRetries) {
            this.log(`Retrying in ${this.options.retryDelay}ms (attempt ${this.retryCount}/${this.options.maxRetries})`);
            
            setTimeout(() => {
                this.poll();
            }, this.options.retryDelay);
        } else {
            this.log('Max retries exceeded, stopping polling');
            this.updateStatusIndicator('disconnected');
            this.trigger('error', error);
        }
    }
    
    // Update equipment status badges in the UI
    updateEquipmentStatuses(equipmentList) {
        equipmentList.forEach(equipment => {
            const statusBadges = document.querySelectorAll(`[data-equipment-id="${equipment.id}"].status-badge`);
            const equipmentCards = document.querySelectorAll(`[data-equipment-name]`);
            
            statusBadges.forEach(badge => {
                const newClasses = this.getStatusBadgeClasses(equipment.status);
                const newText = this.getStatusDisplayText(equipment.status);
                
                badge.className = `inline-flex items-center px-3 py-1 rounded-full text-xs font-bold status-badge ${newClasses}`;
                badge.textContent = newText;
            });
            
            // Update card data attributes
            equipmentCards.forEach(card => {
                if (card.querySelector(`[data-equipment-id="${equipment.id}"]`)) {
                    card.setAttribute('data-status', equipment.status);
                }
            });
        });
    }
    
    // Get CSS classes for status badges
    getStatusBadgeClasses(status) {
        const statusMap = {
            'available': 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'borrowed': 'bg-amber-100 text-amber-800 border-amber-200',
            'unavailable': 'bg-red-100 text-red-800 border-red-200',
            'maintenance': 'bg-red-100 text-red-800 border-red-200'
        };
        return statusMap[status] || 'bg-gray-100 text-gray-800 border-gray-200';
    }
    
    // Get display text for status
    getStatusDisplayText(status) {
        const statusMap = {
            'available': '🟢 Available',
            'borrowed': '🟡 Borrowed',
            'unavailable': '🔴 Unavailable',
            'maintenance': '🔴 Maintenance'
        };
        return statusMap[status] || status.charAt(0).toUpperCase() + status.slice(1);
    }
    
    // Update connection status indicator
    updateStatusIndicator(status) {
        const indicator = document.getElementById('realtime-status');
        if (!indicator) return;
        
        const statusConfig = {
            connected: {
                class: 'bg-emerald-500',
                text: 'Live',
                icon: '🟢'
            },
            connecting: {
                class: 'bg-yellow-500',
                text: 'Connecting',
                icon: '🟡'
            },
            error: {
                class: 'bg-red-500',
                text: 'Error',
                icon: '🔴'
            },
            disconnected: {
                class: 'bg-gray-500',
                text: 'Offline',
                icon: '⚫'
            }
        };
        
        const config = statusConfig[status] || statusConfig.disconnected;
        
        indicator.className = `fixed bottom-4 right-4 ${config.class} text-white px-3 py-1 rounded-full text-xs flex items-center space-x-1 z-50 transition-all duration-200`;
        indicator.innerHTML = `
            <span>${config.icon}</span>
            <span>${config.text}</span>
        `;
    }
    
    // Create and add status indicator to page
    addStatusIndicator() {
        // Remove existing indicator
        const existing = document.getElementById('realtime-status');
        if (existing) {
            existing.remove();
        }
        
        const indicator = document.createElement('div');
        indicator.id = 'realtime-status';
        indicator.className = 'fixed bottom-4 right-4 bg-gray-500 text-white px-3 py-1 rounded-full text-xs flex items-center space-x-1 z-50 transition-all duration-200';
        indicator.innerHTML = `
            <span>⚫</span>
            <span>Initializing</span>
        `;
        
        document.body.appendChild(indicator);
        return indicator;
    }
    
    // Register event callbacks
    on(event, callback) {
        if (!this.callbacks[event]) {
            this.callbacks[event] = [];
        }
        this.callbacks[event].push(callback);
        return this;
    }
    
    // Trigger event callbacks
    trigger(event, data) {
        if (this.callbacks[event]) {
            this.callbacks[event].forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    this.log('Callback error', error);
                }
            });
        }
    }
    
    // Logging helper
    log(message, data = null) {
        if (this.options.debug) {
            if (data) {
                console.log(`[SimpleRealTime] ${message}`, data);
            } else {
                console.log(`[SimpleRealTime] ${message}`);
            }
        }
    }
    
    // Get current status
    getStatus() {
        return {
            isPolling: this.isPolling,
            isVisible: this.isVisible,
            isOnline: navigator.onLine,
            retryCount: this.retryCount,
            lastUpdate: this.lastUpdate
        };
    }
}

// Export for use in other modules
window.SimpleRealTime = SimpleRealTime;

// Auto-initialize if on equipment borrow page
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('/equipment') && window.location.pathname.includes('borrow')) {
        // Initialize real-time updates for equipment borrowing page
        const realTime = new SimpleRealTime({
            debug: true,
            pollInterval: 30000 // 30 seconds
        });
        
        // Add status indicator
        realTime.addStatusIndicator();
        
        // Start polling
        const categoryId = new URLSearchParams(window.location.search).get('category');
        realTime.start('/ruser/equipment/live-status', { 
            category_id: categoryId 
        });
        
        // Handle equipment updates
        realTime.on('equipmentUpdate', function(equipmentList) {
            console.log('Equipment updated:', equipmentList);
        });
        
        // Handle notifications
        realTime.on('notification', function(notification) {
            if (notification.count > 0) {
                // Could show a toast notification here
                console.log('Notification:', notification.message);
            }
        });
        
        // Handle errors
        realTime.on('error', function(error) {
            console.error('Real-time connection error:', error);
        });
        
        // Store globally for debugging
        window.realTimeManager = realTime;
    }
});