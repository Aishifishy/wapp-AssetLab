/**
 * Calendar Manager - Handles FullCalendar functionality and calendar-related features
 * Including academic calendar, reservation calendar, and calendar views
 */

class CalendarManager {
    constructor() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.init();
            });
        } else {
            this.init();
        }
    }    init() {
        this.initAcademicCalendar();
        this.initReservationCalendar();
        
        // Support for new calendarConfig pattern
        if (window.calendarConfig) {
            this.initReservationCalendarWithConfig();
        }
    }

    initAcademicCalendar() {
        const calendarEl = document.getElementById('calendar');
        
        console.log('Calendar element found:', !!calendarEl);
        console.log('Calendar events available:', !!window.calendarEvents);
        console.log('FullCalendar available:', typeof FullCalendar !== 'undefined');
        
        // Only initialize if we're on the academic calendar page
        if (!calendarEl || !window.calendarEvents) {
            console.log('Academic calendar initialization skipped - missing element or events');
            return;
        }

        // Check if FullCalendar is available, wait if not
        if (typeof FullCalendar === 'undefined') {
            console.log('FullCalendar not ready, waiting...');
            setTimeout(() => this.initAcademicCalendar(), 100);
            return;
        }

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            buttonText: {
                today: 'Today',
                month: 'Month',
                week: 'Week'
            },
            dayMaxEvents: true,
            events: window.calendarEvents,
            eventClick: function(info) {
                const event = info.event;
                const props = event.extendedProps;
                
                // Handle event click - could be extended for modals or navigation
                console.log('Event clicked:', event.title, props);
            }
        });

        console.log('Rendering academic calendar with events:', window.calendarEvents);
        calendar.render();
        console.log('Academic calendar rendered successfully');
    }

    initReservationCalendar() {
        const calendarEl = document.getElementById('calendar');
        const modal = document.getElementById('eventModal');
        const closeModal = document.getElementById('closeModal');
        const modalTitle = document.getElementById('modal-title');
        const modalContent = document.getElementById('modal-content');
        const viewDetailsBtn = document.getElementById('viewDetailsBtn');

        // Only initialize if we're on the reservation calendar page
        if (!calendarEl || !modal || !window.reservationCalendarConfig) {
            return;
        }

        // Check if FullCalendar is available
        if (typeof FullCalendar === 'undefined') {
            console.warn('FullCalendar library not loaded');
            return;
        }

        let currentEventUrl = '';
        
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: window.reservationCalendarConfig.view || 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Today',
                month: 'Month',
                week: 'Week',
                day: 'Day'
            },
            height: 'auto',
            events: window.reservationCalendarConfig.events || [],
            eventClick: (info) => {
                this.handleEventClick(info, modal, modalTitle, modalContent, viewDetailsBtn);
            }
        });

        calendar.render();

        // Modal event handlers
        if (closeModal) {
            closeModal.addEventListener('click', () => {
                modal.classList.add('hidden');
            });
        }

        if (viewDetailsBtn) {
            viewDetailsBtn.addEventListener('click', () => {
                if (currentEventUrl) {
                    window.location.href = currentEventUrl;
                }
            });
        }

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });

        // Store current event URL for view details button
        this.currentEventUrl = '';
    }

    handleEventClick(info, modal, modalTitle, modalContent, viewDetailsBtn) {
        const event = info.event;
        const eventData = {
            title: event.title,
            start: event.start,
            end: event.end,
            ...event.extendedProps
        };

        if (modalTitle) {
            modalTitle.textContent = eventData.title || 'Event Details';
        }

        if (modalContent) {
            if (eventData.type === 'schedule') {
                modalContent.innerHTML = `
                    <div class="space-y-3">
                        <div>
                            <span class="font-medium text-gray-700">Subject:</span>
                            <span class="text-gray-900">${eventData.subject || 'N/A'}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Instructor:</span>
                            <span class="text-gray-900">${eventData.instructor || 'N/A'}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Section:</span>
                            <span class="text-gray-900">${eventData.section || 'N/A'}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Laboratory:</span>
                            <span class="text-gray-900">${eventData.laboratory || 'N/A'}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Time:</span>
                            <span class="text-gray-900">
                                ${eventData.start ? eventData.start.toLocaleTimeString() : ''} - 
                                ${eventData.end ? eventData.end.toLocaleTimeString() : ''}
                            </span>
                        </div>
                    </div>
                `;
                
                // Hide view details button for schedules
                if (viewDetailsBtn) {
                    viewDetailsBtn.style.display = 'none';
                }
            } else if (eventData.type === 'reservation') {
                modalContent.innerHTML = `
                    <div class="space-y-3">
                        <div>
                            <span class="font-medium text-gray-700">Purpose:</span>
                            <span class="text-gray-900">${eventData.purpose || 'N/A'}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Requested by:</span>
                            <span class="text-gray-900">${eventData.requester || 'N/A'}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Laboratory:</span>
                            <span class="text-gray-900">${eventData.laboratory || 'N/A'}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Status:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                ${eventData.status === 'approved' ? 'bg-green-100 text-green-800' : 
                                  eventData.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                  'bg-red-100 text-red-800'}">
                                ${eventData.status ? eventData.status.charAt(0).toUpperCase() + eventData.status.slice(1) : 'N/A'}
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Time:</span>
                            <span class="text-gray-900">
                                ${eventData.start ? eventData.start.toLocaleString() : ''} - 
                                ${eventData.end ? eventData.end.toLocaleString() : ''}
                            </span>
                        </div>
                    </div>
                `;
                
                // Set up view details button
                this.currentEventUrl = eventData.url;
                if (viewDetailsBtn) {
                    viewDetailsBtn.style.display = 'block';
                }
            } else {
                // Generic event display
                modalContent.innerHTML = `
                    <div class="space-y-3">
                        <div>
                            <span class="font-medium text-gray-700">Title:</span>
                            <span class="text-gray-900">${eventData.title || 'N/A'}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Start:</span>
                            <span class="text-gray-900">${eventData.start ? eventData.start.toLocaleString() : 'N/A'}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">End:</span>
                            <span class="text-gray-900">${eventData.end ? eventData.end.toLocaleString() : 'N/A'}</span>
                        </div>
                        ${eventData.description ? `
                        <div>
                            <span class="font-medium text-gray-700">Description:</span>
                            <p class="text-gray-900 mt-1">${eventData.description}</p>
                        </div>
                        ` : ''}
                    </div>
                `;
                
                // Set up view details button if URL exists
                if (eventData.url) {
                    this.currentEventUrl = eventData.url;
                    if (viewDetailsBtn) {
                        viewDetailsBtn.style.display = 'block';
                    }
                } else {
                    if (viewDetailsBtn) {
                        viewDetailsBtn.style.display = 'none';
                    }
                }
            }
        }        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    /**
     * Initialize reservation calendar with new calendarConfig pattern
     */
    initReservationCalendarWithConfig() {
        const calendarEl = document.getElementById('calendar');
        const modal = document.getElementById('eventModal');
        const closeModal = document.getElementById('closeModal');
        const modalTitle = document.getElementById('modal-title');
        const modalContent = document.getElementById('modal-content');
        const viewDetailsBtn = document.getElementById('viewDetailsBtn');

        // Only initialize if we're on the reservation calendar page
        if (!calendarEl || !window.calendarConfig) {
            return;
        }

        // Check if FullCalendar is available
        if (typeof FullCalendar === 'undefined') {
            console.warn('FullCalendar library not loaded');
            return;
        }

        let currentEventUrl = '';
        
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: window.calendarConfig.view || 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            slotMinTime: '07:00:00',
            slotMaxTime: '19:00:00',
            allDaySlot: false,
            buttonText: {
                today: 'Today',
                month: 'Month',
                week: 'Week',
                day: 'Day'
            },
            height: 'auto',
            events: window.calendarConfig.events || [],
            eventClick: (info) => {
                const event = info.event;
                const eventData = event.extendedProps;
                
                if (eventData.type === 'schedule') {
                    if (modalTitle) modalTitle.textContent = 'Regular Class Schedule';
                    if (modalContent) {
                        modalContent.innerHTML = `
                            <div class="grid grid-cols-1 gap-3">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Subject</h4>
                                    <p class="mt-1 text-base text-gray-900">${eventData.subject || 'N/A'}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Time</h4>
                                    <p class="mt-1 text-base text-gray-900">${eventData.time || 'N/A'}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Section</h4>
                                    <p class="mt-1 text-base text-gray-900">${eventData.section || 'N/A'}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Instructor</h4>
                                    <p class="mt-1 text-base text-gray-900">${eventData.instructor || 'N/A'}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Laboratory</h4>
                                    <p class="mt-1 text-base text-gray-900">${eventData.laboratory || 'N/A'}</p>
                                </div>
                            </div>
                        `;
                    }
                    if (viewDetailsBtn) viewDetailsBtn.style.display = 'none';
                } else {
                    if (modalTitle) modalTitle.textContent = 'Reservation Details';
                    if (modalContent) {
                        modalContent.innerHTML = `
                            <div class="grid grid-cols-1 gap-3">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Laboratory</h4>
                                    <p class="mt-1 text-base text-gray-900">${eventData.laboratory || 'N/A'}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Time</h4>
                                    <p class="mt-1 text-base text-gray-900">${event.title}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Purpose</h4>
                                    <p class="mt-1 text-base text-gray-900">${eventData.purpose || 'N/A'}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Status</h4>
                                    <p class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        ${eventData.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ''}
                                        ${eventData.status === 'approved' ? 'bg-green-100 text-green-800' : ''}
                                    ">
                                        ${eventData.status ? eventData.status.charAt(0).toUpperCase() + eventData.status.slice(1) : 'N/A'}
                                    </p>
                                </div>
                            </div>
                        `;
                    }
                    
                    // Set up view details button
                    currentEventUrl = eventData.url;
                    if (viewDetailsBtn) viewDetailsBtn.style.display = 'block';
                }
                
                if (modal) modal.classList.remove('hidden');
            }
        });

        calendar.render();

        // Modal event handlers
        if (closeModal) {
            closeModal.addEventListener('click', () => {
                if (modal) modal.classList.add('hidden');
            });
        }

        if (viewDetailsBtn) {
            viewDetailsBtn.addEventListener('click', () => {
                if (currentEventUrl) {
                    window.location.href = currentEventUrl;
                }
            });
        }

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (modal && e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    }
}

// Initialize when module is imported
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.calendarManager = new CalendarManager();
    });
} else {
    window.calendarManager = new CalendarManager();
}

export default CalendarManager;
