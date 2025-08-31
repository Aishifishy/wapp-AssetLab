/**
 * Laboratory Reservation Calendar
 * Handles FullCalendar integration and event management
 */

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    // Exit early if calendar element doesn't exist (not on calendar page)
    if (!calendarEl) {
        return;
    }
    
    // Check if FullCalendar is available
    if (typeof FullCalendar === 'undefined') {
        console.warn('FullCalendar library not loaded');
        return;
    }
    
    const filterForm = document.getElementById('filterForm');
    const modal = document.getElementById('eventModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    const closeModal = document.getElementById('closeModal');

    // Calendar configuration
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: '{{ $view }}',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            fetchEvents(fetchInfo, successCallback, failureCallback);
        },
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        eventDidMount: function(info) {
            styleEvent(info);
        },
        slotMinTime: '07:00:00',
        slotMaxTime: '22:00:00',
        allDaySlot: false,
        editable: false,
        selectable: false,
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5], // Monday - Friday
            startTime: '07:00',
            endTime: '18:00'
        }
    });

    // Fetch events from server
    async function fetchEvents(fetchInfo, successCallback, failureCallback) {
        try {
            const laboratory = document.getElementById('laboratory').value;
            const url = new URL('{{ route("ruser.laboratory.calendar.events") }}', window.location.origin);
            
            const params = {
                start: fetchInfo.startStr,
                end: fetchInfo.endStr
            };
            
            if (laboratory) {
                params.laboratory = laboratory;
            }
            
            Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));
            
            const response = await fetch(url);
            const events = await response.json();
            
            successCallback(events);
        } catch (error) {
            console.error('Error fetching events:', error);
            failureCallback(error);
        }
    }

    // Style events based on type and status
    function styleEvent(info) {
        const event = info.event;
        const status = event.extendedProps.status;
        const type = event.extendedProps.type || 'reservation';
        
        // Color coding based on status and type
        const colorMap = {
            'reservation': {
                'pending': '#f59e0b',      // amber
                'approved': '#10b981',      // emerald
                'rejected': '#ef4444',      // red
                'cancelled': '#6b7280',     // gray
                'completed': '#3b82f6'      // blue
            },
            'schedule': {
                'default': '#8b5cf6'        // violet
            }
        };
        
        const typeColors = colorMap[type] || colorMap['reservation'];
        const color = typeColors[status] || typeColors['default'] || '#6b7280';
        
        info.el.style.backgroundColor = color;
        info.el.style.borderColor = color;
        
        // Add visual indicators
        if (status === 'pending') {
            info.el.style.opacity = '0.8';
            info.el.style.fontStyle = 'italic';
        }
        
        if (type === 'schedule') {
            info.el.style.borderStyle = 'dashed';
        }
    }

    // Show event details in modal
    function showEventDetails(event) {
        const extProps = event.extendedProps;
        const startTime = event.start ? event.start.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        }) : '';
        const endTime = event.end ? event.end.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        }) : '';
        const eventDate = event.start ? event.start.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }) : '';

        modalTitle.textContent = event.title;
        
        let content = `
            <div class="space-y-4">
                <div>
                    <h4 class="font-medium text-gray-900">Date & Time</h4>
                    <p class="text-gray-700">${eventDate}</p>
                    <p class="text-gray-700">${startTime} - ${endTime}</p>
                </div>
        `;

        if (extProps.laboratory) {
            content += `
                <div>
                    <h4 class="font-medium text-gray-900">Laboratory</h4>
                    <p class="text-gray-700">${extProps.laboratory}</p>
                </div>
            `;
        }

        if (extProps.purpose) {
            content += `
                <div>
                    <h4 class="font-medium text-gray-900">Purpose</h4>
                    <p class="text-gray-700">${extProps.purpose}</p>
                </div>
            `;
        }

        if (extProps.instructor) {
            content += `
                <div>
                    <h4 class="font-medium text-gray-900">Instructor</h4>
                    <p class="text-gray-700">${extProps.instructor}</p>
                </div>
            `;
        }

        if (extProps.students) {
            content += `
                <div>
                    <h4 class="font-medium text-gray-900">Students</h4>
                    <p class="text-gray-700">${extProps.students}</p>
                </div>
            `;
        }

        if (extProps.status) {
            const statusColors = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'approved': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800',
                'cancelled': 'bg-gray-100 text-gray-800',
                'completed': 'bg-blue-100 text-blue-800'
            };
            
            const statusClass = statusColors[extProps.status] || 'bg-gray-100 text-gray-800';
            
            content += `
                <div>
                    <h4 class="font-medium text-gray-900">Status</h4>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                        ${extProps.status.charAt(0).toUpperCase() + extProps.status.slice(1)}
                    </span>
                </div>
            `;
        }

        // Add action buttons for reservations
        if (extProps.type === 'reservation' && extProps.reservationId) {
            content += `
                <div class="pt-4 border-t border-gray-200">
                    <a href="/ruser/laboratory/reservations/${extProps.reservationId}" 
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        View Details
                    </a>
                </div>
            `;
        }

        content += '</div>';
        modalContent.innerHTML = content;
        
        showModal();
    }

    // Modal functions
    function showModal() {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function hideModal() {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Filter form submission
    function handleFilterSubmit(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }
        
        // Update URL with filters
        const newUrl = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
        window.history.pushState({}, '', newUrl);
        
        // Refresh calendar
        calendar.refetchEvents();
    }

    // Event listeners
    if (filterForm) {
        filterForm.addEventListener('submit', handleFilterSubmit);
    }

    if (closeModal) {
        closeModal.addEventListener('click', hideModal);
    }

    // Close modal when clicking outside
    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                hideModal();
            }
        });
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            hideModal();
        }
    });

    // Initialize calendar
    calendar.render();

    // Update calendar view when filter changes
    const viewSelect = document.getElementById('view');
    if (viewSelect) {
        viewSelect.addEventListener('change', function() {
            calendar.changeView(this.value);
        });
    }

    // Auto-refresh events every 30 seconds
    setInterval(() => {
        calendar.refetchEvents();
    }, 30000);
});
