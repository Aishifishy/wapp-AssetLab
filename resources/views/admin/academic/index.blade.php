@extends('layouts.admin')

@section('title', 'Calendar')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
/* Custom calendar styles */
.fc-daygrid-day {
    position: relative;
    transition: all 0.2s ease;
}

/* Activity indicators */
.activity-indicators {
    pointer-events: auto;
    z-index: 5;
}

.activity-indicator {
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    cursor: pointer;
}

.activity-indicator:hover {
    transform: scale(1.3);
    z-index: 10;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
}

.activity-indicator:active {
    transform: scale(1.1);
}

/* Enhanced pulse animation for overdue items */
.pulse-animation {
    animation: enhanced-pulse 1.8s infinite;
}

@keyframes enhanced-pulse {
    0% {
        opacity: 1;
        transform: scale(1);
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.15);
        box-shadow: 0 4px 16px rgba(239, 68, 68, 0.5);
    }
    100% {
        opacity: 1;
        transform: scale(1);
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
    }
}

/* Gradient indicators for better visual appeal */
.activity-indicator.equipment-gradient {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.activity-indicator.lab-gradient {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.activity-indicator.overdue-gradient {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

/* Term background styling */
.fc .fc-bg-event {
    opacity: 0.1;
}

.fc .term-event.current-term {
    background-color: #10b981 !important;
    opacity: 0.15;
}

.fc .term-event:not(.current-term) {
    background-color: #3b82f6 !important;
    opacity: 0.08;
}

/* Calendar cell hover effect */
.fc-daygrid-day:hover {
    background-color: rgba(59, 130, 246, 0.05);
}

.fc-daygrid-day.fc-day-today {
    background-color: rgba(16, 185, 129, 0.08) !important;
}

/* Enhanced legend styling */
.calendar-legend {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: 1px solid #cbd5e0;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .activity-indicators {
        top: 2px;
        right: 2px;
        gap: 2px;
    }
    
    .activity-indicator {
        width: 7px !important;
        height: 7px !important;
        border-width: 1px;
    }
    
    .activity-indicator span {
        font-size: 7px !important;
        top: -6px !important;
        right: -6px !important;
        padding: 1px 3px !important;
        min-width: 12px !important;
    }
    
    .calendar-legend {
        padding: 8px;
        flex-direction: column;
        align-items: stretch;
    }
    
    .calendar-legend > div {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .calendar-legend .border-l {
        border-left: none;
        border-top: 1px solid #d1d5db;
        padding-left: 0;
        padding-top: 8px;
        margin-top: 8px;
    }
    
    /* Enhanced touch targets */
    .activity-indicator {
        min-width: 12px;
        min-height: 12px;
        touch-action: manipulation;
    }
    
    /* Larger tooltips for mobile */
    .activity-tooltip {
        font-size: 12px !important;
        padding: 10px 14px !important;
        max-width: 250px;
        word-wrap: break-word;
    }
}

/* Tablet optimizations */
@media (min-width: 769px) and (max-width: 1024px) {
    .activity-indicator {
        width: 9px !important;
        height: 9px !important;
    }
    
    .activity-indicator span {
        font-size: 8px !important;
        top: -7px !important;
        right: -7px !important;
    }
}

/* Large screen optimizations */
@media (min-width: 1440px) {
    .activity-indicator {
        width: 11px !important;
        height: 11px !important;
    }
    
    .activity-indicator span {
        font-size: 10px !important;
        top: -9px !important;
        right: -9px !important;
    }
}
</style>
@endpush

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Calendar</h1>
        <div class="flex space-x-3">
            <form action="{{ route('admin.academic.set-current-by-date') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition">
                    <i class="fas fa-magic mr-2"></i> Auto-Set Current
                </button>
            </form>
            <a href="{{ route('admin.academic.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition">
                <i class="fas fa-plus mr-2"></i> Add New Academic Year
            </a>
        </div>
    </div>    <!-- Flash Messages -->
    <x-flash-messages />

    <!-- Information about automatic term creation -->
    <!-- <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Note:</strong> Academic years automatically include three terms (First Term, Second Term, Third Term). 
                    You can edit the dates and details of each term using the "Edit" buttons below.
                </p>
            </div>
        </div>
    </div> -->

    <!-- Academic Year Selection -->
    <div class="mb-6 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-graduation-cap text-gray-500 mr-3"></i>
                    <h3 class="text-lg font-medium text-gray-900">Academic Year Management</h3>
                </div>
                <div class="flex items-center space-x-3">
                    <select id="academicYearSelect" class="form-select rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Academic Year</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" 
                                    data-current="{{ $year->terms->contains('is_current', true) ? 'true' : 'false' }}"
                                    {{ $year->terms->contains('is_current', true) ? 'selected' : '' }}>
                                {{ $year->name }} ({{ $year->start_date->format('M Y') }} - {{ $year->end_date->format('M Y') }})
                                @if($year->terms->contains('is_current', true)) - Current @endif
                            </option>
                        @endforeach
                    </select>
                    <button id="editYearBtn" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        <i class="fas fa-edit mr-2"></i> Edit Year
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Academic Terms for Selected Year -->
        <div id="academicTermsSection" class="hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h4 class="text-md font-medium text-gray-700 flex items-center">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Academic Terms for <span id="selectedYearName" class="font-semibold ml-1"></span>
                </h4>
            </div>
            <div id="academicTermsList" class="p-6">
                <!-- Terms will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-calendar-alt text-blue-500 mr-3"></i>
                    Calendar Overview
                </h3>
                <div class="calendar-legend rounded-lg p-3">
                    <div class="flex items-center space-x-6">
                    <!-- Term Legend -->
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-blue-100 border border-blue-300 rounded"></div>
                            <span class="text-sm text-gray-600">Academic Terms</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-100 border border-green-300 rounded"></div>
                            <span class="text-sm text-gray-600">Current Term</span>
                        </div>
                    </div>
                    
                    <!-- Activity Indicators Legend -->
                    <div class="border-l border-gray-300 pl-6">
                        <div class="text-xs font-medium text-gray-500 mb-2">Daily Activity Indicators</div>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center gap-1.5">
                                <div class="w-2.5 h-2.5 bg-blue-500 rounded-full"></div>
                                <span class="text-xs text-gray-600">Equipment Requests</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-2.5 h-2.5 bg-purple-500 rounded-full"></div>
                                <span class="text-xs text-gray-600">Lab Reservations</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse"></div>
                                <span class="text-xs text-gray-600">Overdue Items</span>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Daily Overview Modal -->
    <div id="dailyOverviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-calendar-day text-blue-500 mr-2"></i>
                        Daily Overview - <span id="modalDate" class="font-semibold ml-1"></span>
                    </h3>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="mt-4 max-h-96 overflow-y-auto">
                    <!-- Loading State -->
                    <div id="modalLoading" class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                        <p class="mt-2 text-gray-500">Loading daily overview...</p>
                    </div>

                    <!-- Content Container -->
                    <div id="modalContent" class="hidden">
                        <!-- Tab Navigation -->
                        <div class="border-b border-gray-200 mb-4">
                            <nav class="-mb-px flex space-x-6">
                                <button class="modal-tab-btn py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap border-blue-500 text-blue-600" data-tab="equipment">
                                    <i class="fas fa-tools mr-2"></i>Available Equipment
                                </button>
                                <button class="modal-tab-btn py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="due">
                                    <i class="fas fa-clock mr-2"></i>Due Returns
                                </button>
                                <button class="modal-tab-btn py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="labs">
                                    <i class="fas fa-desktop mr-2"></i>Computer Labs
                                </button>
                                <button class="modal-tab-btn py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="labreservations">
                                    <i class="fas fa-calendar-check mr-2"></i>Laboratory Reservations
                                </button>
                                <button class="modal-tab-btn py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="equipmentborrowing">
                                    <i class="fas fa-hand-holding mr-2"></i>Equipment Borrowing
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <!-- Available Equipment Tab -->
                        <div id="equipment-tab" class="modal-tab-content">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="equipmentList">
                                <!-- Equipment cards will be populated here -->
                            </div>
                        </div>

                        <!-- Due Returns Tab -->
                        <div id="due-tab" class="modal-tab-content hidden">
                            <div class="space-y-4">
                                <div id="dueEquipmentList">
                                    <!-- Due equipment will be populated here -->
                                </div>
                                <div id="overdueEquipmentList">
                                    <!-- Overdue equipment will be populated here -->
                                </div>
                            </div>
                        </div>

                        <!-- Computer Labs Tab -->
                                                <!-- Computer Labs Tab -->
                        <div id="labs-tab" class="modal-tab-content hidden">
                            <!-- Schedule Legend -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Color Legend</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-green-100 border border-green-300 rounded mr-2"></div>
                                        <span>Available</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-blue-100 border border-blue-300 rounded mr-2"></div>
                                        <span>Regular Class</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-orange-100 border border-orange-300 rounded mr-2"></div>
                                        <span>Override/Reschedule</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-purple-100 border border-purple-300 rounded mr-2"></div>
                                        <span>Reservation</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-6" id="labsList">
                                <!-- Lab data will be populated here -->
                            </div>
                        </div>

                        <!-- Laboratory Reservations Tab -->
                        <div id="labreservations-tab" class="modal-tab-content hidden">
                            <div class="space-y-4" id="labReservationsList">
                                <!-- Laboratory reservations will be populated here -->
                            </div>
                        </div>

                        <!-- Equipment Borrowing Tab -->
                        <div id="equipmentborrowing-tab" class="modal-tab-content hidden">
                            <div class="space-y-4" id="equipmentBorrowingList">
                                <!-- Equipment borrowing will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end pt-4 border-t border-gray-200 mt-4">
                    <button id="closeModalBtn" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
// Make calendar events and academic years data available to JavaScript
window.calendarEvents = {!! json_encode($calendarEvents) !!};
window.calendarActivities = {!! json_encode($calendarActivities) !!};
window.academicYearsData = {!! json_encode($academicYears->map(function($year) {
    return [
        'id' => $year->id,
        'name' => $year->name,
        'start_date' => $year->start_date->format('M d, Y'),
        'end_date' => $year->end_date->format('M d, Y'),
        'is_current' => $year->terms->contains('is_current', true),
        'terms' => $year->terms->sortBy('term_number')->map(function($term) use ($year) {
            return [
                'id' => $term->id,
                'name' => $term->name,
                'start_date' => $term->start_date->format('M d, Y'),
                'end_date' => $term->end_date->format('M d, Y'),
                'is_current' => $term->is_current,
                'edit_url' => route('admin.academic.terms.edit', ['academicYear' => $year->id, 'term' => $term->id])
            ];
        })->values()
    ];
})->values()) !!};

document.addEventListener('DOMContentLoaded', function() {
    const academicYearSelect = document.getElementById('academicYearSelect');
    const editYearBtn = document.getElementById('editYearBtn');
    const academicTermsSection = document.getElementById('academicTermsSection');
    const selectedYearName = document.getElementById('selectedYearName');
    const academicTermsList = document.getElementById('academicTermsList');

    // Modal elements
    const modal = document.getElementById('dailyOverviewModal');
    const modalDate = document.getElementById('modalDate');
    const modalLoading = document.getElementById('modalLoading');
    const modalContent = document.getElementById('modalContent');
    const closeModal = document.getElementById('closeModal');
    const closeModalBtn = document.getElementById('closeModalBtn');

    // Handle academic year selection
    academicYearSelect.addEventListener('change', function() {
        const selectedYearId = this.value;
        
        if (selectedYearId) {
            const yearData = window.academicYearsData.find(year => year.id == selectedYearId);
            
            if (yearData) {
                // Enable edit button and set its URL
                editYearBtn.disabled = false;
                editYearBtn.onclick = function() {
                    window.location.href = '/admin/academic/' + selectedYearId + '/edit';
                };
                
                // Show terms section
                academicTermsSection.classList.remove('hidden');
                selectedYearName.textContent = yearData.name;
                
                // Populate terms
                populateTerms(yearData.terms);
            }
        } else {
            // Disable edit button and hide terms section
            editYearBtn.disabled = true;
            editYearBtn.onclick = null;
            academicTermsSection.classList.add('hidden');
        }
    });

    // Modal functionality
    function openModal(date) {
        modal.classList.remove('hidden');
        modalDate.textContent = new Date(date).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        // Show loading state
        modalLoading.classList.remove('hidden');
        modalContent.classList.add('hidden');
        
        // Fetch daily overview data
        fetchDailyOverview(date);
    }

    function closeModalHandler() {
        modal.classList.add('hidden');
    }

    closeModal.addEventListener('click', closeModalHandler);
    closeModalBtn.addEventListener('click', closeModalHandler);

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModalHandler();
        }
    });

    // Tab functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-tab-btn') || e.target.closest('.modal-tab-btn')) {
            const btn = e.target.classList.contains('modal-tab-btn') ? e.target : e.target.closest('.modal-tab-btn');
            const tabName = btn.dataset.tab;
            
            // Update tab buttons
            document.querySelectorAll('.modal-tab-btn').forEach(b => {
                b.classList.remove('border-blue-500', 'text-blue-600');
                b.classList.add('border-transparent', 'text-gray-500');
            });
            btn.classList.remove('border-transparent', 'text-gray-500');
            btn.classList.add('border-blue-500', 'text-blue-600');
            
            // Update tab content
            document.querySelectorAll('.modal-tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(tabName + '-tab').classList.remove('hidden');
        }
    });

    // Fetch daily overview data
    async function fetchDailyOverview(date) {
        try {
            const response = await fetch(`/admin/academic/daily-overview?date=${date}`);
            
            if (response.ok) {
                const data = await response.json();
                populateModalData(data);
            } else {
                const errorText = await response.text();
                console.error('Server response error:', response.status, errorText);
                showModalError(`Failed to load daily overview data. Server returned status: ${response.status}`);
            }
        } catch (error) {
            console.error('Error fetching daily overview:', error);
            showModalError('An error occurred while loading data: ' + error.message);
        }
    }

    // Populate modal with data
    function populateModalData(data) {
        modalLoading.classList.add('hidden');
        modalContent.classList.remove('hidden');
        
        // Populate equipment list
        populateEquipmentList(data.available_equipment || []);
        
        // Populate due equipment
        populateDueEquipment(data.due_equipment || [], data.overdue_equipment || []);
        
        // Populate lab schedules
        populateLabSchedules(data.lab_schedules || []);
        
        // Populate laboratory reservations
        populateLabReservations(data.lab_reservations || []);
        
        // Populate equipment borrowing
        populateEquipmentBorrowing(data.equipment_borrowing || []);
    }

    function populateEquipmentList(equipmentCategories) {
        const container = document.getElementById('equipmentList');
        container.innerHTML = '';
        
        if (equipmentCategories.length === 0) {
            container.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500">No available equipment found.</div>';
            return;
        }
        
        // Change grid to single column for better category display
        container.className = 'space-y-4';
        
        // Remove any existing event listeners by cloning the container
        const newContainer = container.cloneNode(false);
        container.parentNode.replaceChild(newContainer, container);
        
        equipmentCategories.forEach((categoryData, index) => {
            const categoryCard = document.createElement('div');
            categoryCard.className = 'bg-green-50 border border-green-200 rounded-lg p-4';
            
            // Use index-based ID to avoid conflicts with special characters
            const categoryId = `category-${index}`;
            
            categoryCard.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h4 class="font-semibold text-gray-900 text-lg">${categoryData.category}</h4>
                        <p class="text-sm text-gray-600">Total Available: ${categoryData.total_count} items</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        ${categoryData.total_count} Available
                    </span>
                </div>
                <div class="mt-3">
                    <button class="toggle-details text-sm text-blue-600 hover:text-blue-800 focus:outline-none" 
                            data-target="${categoryId}">
                        Show Equipment Details
                    </button>
                    <div class="equipment-details hidden mt-2 space-y-1" id="details-${categoryId}">
                        ${categoryData.equipment_list.map(equipment => `
                            <div class="text-xs bg-white rounded px-2 py-1 border flex justify-between">
                                <span><strong>ID ${equipment.id}:</strong> ${equipment.name}</span>
                                ${equipment.rfid_tag ? `<span class="text-gray-500">RFID: ${equipment.rfid_tag}</span>` : ''}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            
            newContainer.appendChild(categoryCard);
        });
        
        // Add single event listener using event delegation
        newContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('toggle-details')) {
                e.preventDefault();
                const targetId = e.target.dataset.target;
                const detailsDiv = document.getElementById(`details-${targetId}`);
                
                if (detailsDiv) {
                    if (detailsDiv.classList.contains('hidden')) {
                        detailsDiv.classList.remove('hidden');
                        e.target.textContent = 'Hide Equipment Details';
                    } else {
                        detailsDiv.classList.add('hidden');
                        e.target.textContent = 'Show Equipment Details';
                    }
                }
            }
        });
    }

    function populateDueEquipment(dueToday, overdue) {
        const container = document.getElementById('dueEquipmentList');
        const overdueContainer = document.getElementById('overdueEquipmentList');
        
        // Due today
        container.innerHTML = '<h4 class="font-medium text-gray-900 mb-3">Due for Return Today</h4>';
        if (dueToday.length === 0) {
            container.innerHTML += '<p class="text-gray-500 text-sm">No equipment due for return today.</p>';
        } else {
            dueToday.forEach(item => {
                const card = document.createElement('div');
                card.className = 'bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-2';
                card.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div>
                            <h5 class="font-medium text-gray-900">${item.equipment_name}</h5>
                            <p class="text-sm text-gray-600">Borrower: ${item.borrower_name}</p>
                            <p class="text-sm text-gray-600">Due: ${item.due_time}</p>
                            <p class="text-xs text-gray-500 mt-1">${item.purpose}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Due Today
                        </span>
                    </div>
                `;
                container.appendChild(card);
            });
        }
        
        // Overdue
        overdueContainer.innerHTML = '<h4 class="font-medium text-gray-900 mb-3 mt-6">Overdue Equipment</h4>';
        if (overdue.length === 0) {
            overdueContainer.innerHTML += '<p class="text-gray-500 text-sm">No overdue equipment.</p>';
        } else {
            overdue.forEach(item => {
                const card = document.createElement('div');
                card.className = 'bg-red-50 border border-red-200 rounded-lg p-4 mb-2';
                card.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div>
                            <h5 class="font-medium text-gray-900">${item.equipment_name}</h5>
                            <p class="text-sm text-gray-600">Borrower: ${item.borrower_name}</p>
                            <p class="text-sm text-gray-600">Was due: ${item.due_date}</p>
                            <p class="text-xs text-gray-500 mt-1">${item.purpose}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            ${item.days_overdue} days overdue
                        </span>
                    </div>
                `;
                overdueContainer.appendChild(card);
            });
        }
    }

    function populateLabSchedules(labs) {
        const container = document.getElementById('labsList');
        container.innerHTML = '';
        
        // Store lab data globally for dropdown functionality
        window.currentLabData = labs;
        
        if (labs.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center py-8">No computer lab data found for this day.</p>';
            return;
        }
        
        labs.forEach(lab => {
            const labCard = document.createElement('div');
            labCard.className = 'bg-white border border-gray-200 rounded-lg p-6';
            
            // Lab header with status
            const statusColors = {
                'available': 'bg-green-100 text-green-800',
                'in_use': 'bg-blue-100 text-blue-800',
                'under_maintenance': 'bg-yellow-100 text-yellow-800',
                'reserved': 'bg-purple-100 text-purple-800'
            };
            const statusClass = statusColors[lab.status] || 'bg-gray-100 text-gray-800';
            
            // Generate dropdown for schedules
            let schedulesDropdownOptions = '';
            if (lab.schedules.length === 0) {
                schedulesDropdownOptions = '<option value="">No scheduled activities</option>';
            } else {
                schedulesDropdownOptions = '<option value="">Select a schedule to view details...</option>';
                lab.schedules.forEach((schedule, index) => {
                    const scheduleLabel = `${schedule.time_range} - ${schedule.subject_name}`;
                    schedulesDropdownOptions += `<option value="${index}">${scheduleLabel}</option>`;
                });
            }
            
            // Generate time slots grid
            let timeSlotsHtml = '<div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">';
            lab.time_slots.forEach(slot => {
                let slotClass = 'px-2 py-1 text-xs border rounded text-center cursor-pointer transition-colors';
                let slotContent = slot.time.split(' - ')[0]; // Show only start hour
                let tooltip = '';
                
                if (slot.available) {
                    slotClass += ' bg-green-100 text-green-800 border-green-300 hover:bg-green-200';
                    tooltip = 'Available';
                } else {
                    const item = slot.item;
                    // Debug: Log the slot data to console
                    if (item.type === 'override' || item.type === 'cancelled') {
                        console.log('Override/Cancelled slot found:', slot);
                    }
                    
                    switch (item.type) {
                        case 'regular':
                            // Treat both regular and special classes as "Regular Class" (blue)
                            slotClass += ' bg-blue-100 text-blue-800 border-blue-300 hover:bg-blue-200';
                            tooltip = `Regular Class: ${item.subject_code} - ${item.instructor}`;
                            break;
                        case 'override':
                            slotClass += ' bg-orange-100 text-orange-800 border-orange-300 hover:bg-orange-200';
                            tooltip = `Override/Reschedule: ${item.subject_code} - ${item.instructor}`;
                            break;
                        case 'reservation':
                            slotClass += ' bg-purple-100 text-purple-800 border-purple-300 hover:bg-purple-200';
                            tooltip = `Reservation: ${item.subject_name} - ${item.instructor}`;
                            break;
                        default:
                            slotClass += ' bg-gray-100 text-gray-800 border-gray-300';
                            tooltip = `Unknown type (${item.type}): ${item.subject_code || 'N/A'}`;
                            console.log('Unknown slot type:', slot);
                    }
                }
                
                timeSlotsHtml += `
                    <div class="${slotClass}" title="${tooltip}">
                        ${slotContent}
                    </div>
                `;
            });
            timeSlotsHtml += '</div>';
            
            labCard.innerHTML = `
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h5 class="text-lg font-medium text-gray-900">${lab.lab_name}</h5>
                        <p class="text-sm text-gray-600">${lab.lab_building} - Room ${lab.lab_room}</p>
                        <p class="text-sm text-gray-600">Capacity: ${lab.capacity} students | Computers: ${lab.computers}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                        ${lab.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                    </span>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Schedules for Today:</label>
                    <select class="w-full p-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            onchange="showScheduleDetails(this, ${lab.lab_id})" id="schedule-select-${lab.lab_id}">
                        ${schedulesDropdownOptions}
                    </select>
                    <div id="schedule-details-${lab.lab_id}" class="mt-3 hidden">
                        <!-- Schedule details will be shown here -->
                    </div>
                </div>
                
                <div>
                    <h6 class="text-sm font-medium text-gray-700 mb-3">Time Slots (7 AM - 9 PM):</h6>
                    ${timeSlotsHtml}
                    <p class="text-xs text-gray-500 mt-2">Hover over time slots for details</p>
                </div>
            `;
            
            container.appendChild(labCard);
        });
    }

    // Function to show schedule details when dropdown selection changes
    window.showScheduleDetails = function(selectElement, labId) {
        const selectedIndex = selectElement.value;
        const detailsContainer = document.getElementById(`schedule-details-${labId}`);
        
        if (selectedIndex === '') {
            detailsContainer.classList.add('hidden');
            return;
        }
        
        // Find the lab data
        const labData = window.currentLabData?.find(lab => lab.lab_id === labId);
        if (!labData || !labData.schedules[selectedIndex]) {
            detailsContainer.classList.add('hidden');
            return;
        }
        
        const schedule = labData.schedules[selectedIndex];
        
        // Determine schedule type styling
        let badgeClass = '';
        let badgeText = '';
        let cardClass = 'border-l-4 p-4 bg-gray-50';
        
        switch (schedule.type) {
            case 'regular':
                cardClass += ' border-l-blue-400';
                badgeClass = 'bg-blue-100 text-blue-800';
                badgeText = schedule.schedule_type === 'special' ? 'Special Class' : 'Regular Class';
                break;
            case 'override':
                cardClass += ' border-l-orange-400';
                badgeClass = 'bg-orange-100 text-orange-800';
                badgeText = 'Override - ' + (schedule.schedule_type === 'reschedule' ? 'Rescheduled' : 'Replacement');
                break;
            case 'cancelled':
                cardClass += ' border-l-red-400';
                badgeClass = 'bg-red-100 text-red-800';
                badgeText = 'Cancelled';
                break;
            case 'reservation':
                cardClass += ' border-l-purple-400';
                badgeClass = 'bg-purple-100 text-purple-800';
                badgeText = 'Laboratory Reservation';
                break;
        }
        
        let detailsHtml = `
            <div class="${cardClass}">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="font-medium text-gray-900">${schedule.subject_name}</h4>
                        <p class="text-sm text-gray-600">Code: ${schedule.subject_code}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${badgeClass}">
                        ${badgeText}
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Instructor:</span>
                        <p class="text-gray-900">${schedule.instructor}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Section:</span>
                        <p class="text-gray-900">${schedule.section}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Time:</span>
                        <p class="text-gray-900">${schedule.time_range}</p>
                    </div>
                </div>
        `;
        
        // Add type-specific information
        if (schedule.type === 'override' && schedule.override_reason) {
            detailsHtml += `
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <span class="font-medium text-gray-700">Override Reason:</span>
                    <p class="text-gray-900 text-sm">${schedule.override_reason}</p>
                </div>
            `;
        }
        
        if (schedule.type === 'reservation' && schedule.purpose) {
            detailsHtml += `
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Purpose:</span>
                            <p class="text-gray-900">${schedule.purpose}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Expected Students:</span>
                            <p class="text-gray-900">${schedule.num_students || 'N/A'}</p>
                        </div>
                    </div>
                </div>
            `;
        }
        
        if (schedule.notes && schedule.notes !== schedule.purpose && schedule.notes !== schedule.override_reason) {
            detailsHtml += `
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <span class="font-medium text-gray-700">Notes:</span>
                    <p class="text-gray-900 text-sm">${schedule.notes}</p>
                </div>
            `;
        }
        
        detailsHtml += '</div>';
        
        detailsContainer.innerHTML = detailsHtml;
        detailsContainer.classList.remove('hidden');
    };

    function populateLabReservations(reservations) {
        const container = document.getElementById('labReservationsList');
        container.innerHTML = '';
        
        if (reservations.length === 0) {
            container.innerHTML = '<div class="text-center py-8 text-gray-500">No laboratory reservations found for this day.</div>';
            return;
        }
        
        reservations.forEach(reservation => {
            const card = document.createElement('div');
            const statusClass = reservation.status === 'approved' ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200';
            const statusTextClass = reservation.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
            
            card.className = `${statusClass} border rounded-lg p-4`;
            card.innerHTML = `
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="font-medium text-gray-900">${reservation.laboratory_name}</h4>
                        <p class="text-sm text-gray-600">Reserved by: ${reservation.user_name}</p>
                        <p class="text-sm text-gray-600">Time: ${reservation.start_time} - ${reservation.end_time}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${statusTextClass}">
                        ${reservation.status.charAt(0).toUpperCase() + reservation.status.slice(1)}
                    </span>
                </div>
                <div class="text-sm text-gray-600">
                    <p><strong>Purpose:</strong> ${reservation.purpose}</p>
                    ${reservation.instructor ? `<p><strong>Instructor:</strong> ${reservation.instructor}</p>` : ''}
                    ${reservation.subject ? `<p><strong>Subject:</strong> ${reservation.subject}</p>` : ''}
                    <p><strong>Expected Attendees:</strong> ${reservation.expected_attendees}</p>
                </div>
            `;
            container.appendChild(card);
        });
    }

    function populateEquipmentBorrowing(borrowing) {
        const container = document.getElementById('equipmentBorrowingList');
        container.innerHTML = '';
        
        if (borrowing.length === 0) {
            container.innerHTML = '<div class="text-center py-8 text-gray-500">No equipment borrowing found for this day.</div>';
            return;
        }
        
        borrowing.forEach(request => {
            const card = document.createElement('div');
            const statusClass = request.status === 'approved' ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200';
            const statusTextClass = request.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
            
            card.className = `${statusClass} border rounded-lg p-4`;
            card.innerHTML = `
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="font-medium text-gray-900">${request.equipment_name}</h4>
                        <p class="text-sm text-gray-600">Borrower: ${request.user_name}</p>
                        <p class="text-sm text-gray-600">Time: ${request.borrow_time} - ${request.return_time}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${statusTextClass}">
                        ${request.status.charAt(0).toUpperCase() + request.status.slice(1)}
                    </span>
                </div>
                <div class="text-sm text-gray-600">
                    <p><strong>Purpose:</strong> ${request.purpose}</p>
                    <p><strong>Quantity:</strong> ${request.quantity}</p>
                    ${request.category ? `<p><strong>Category:</strong> ${request.category}</p>` : ''}
                    ${request.rfid_tag ? `<p><strong>RFID Tag:</strong> ${request.rfid_tag}</p>` : ''}
                </div>
            `;
            container.appendChild(card);
        });
    }

    function showModalError(message) {
        modalLoading.classList.add('hidden');
        modalContent.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-3xl text-red-400 mb-4"></i>
                <p class="text-gray-600">${message}</p>
            </div>
        `;
        modalContent.classList.remove('hidden');
    }

    // Populate terms list
    function populateTerms(terms) {
        academicTermsList.innerHTML = '';
        
        if (terms.length === 0) {
            academicTermsList.innerHTML = '<p class="text-gray-500 text-sm">No terms found for this academic year.</p>';
            return;
        }

        const termsGrid = document.createElement('div');
        termsGrid.className = 'grid grid-cols-1 md:grid-cols-3 gap-4';
        
        terms.forEach(term => {
            const termCard = document.createElement('div');
            termCard.className = 'bg-gray-50 rounded-lg p-4 border border-gray-200 hover:bg-gray-100 transition-colors';
            
            termCard.innerHTML = `
                <div class="flex justify-between items-start mb-2">
                    <h5 class="font-medium text-gray-900">${term.name}</h5>
                    ${term.is_current ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Current</span>' : ''}
                </div>
                <p class="text-sm text-gray-600 mb-3">${term.start_date} - ${term.end_date}</p>
                <a href="${term.edit_url}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-800 focus:outline-none">
                    <i class="fas fa-edit mr-1"></i> Edit Term
                </a>
            `;
            
            termsGrid.appendChild(termCard);
        });
        
        academicTermsList.appendChild(termsGrid);
    }

    // Trigger change event for initially selected year
    if (academicYearSelect.value) {
        academicYearSelect.dispatchEvent(new Event('change'));
    }

    // Initialize FullCalendar
    setTimeout(function() {
        const calendarEl = document.getElementById('calendar');
        if (calendarEl && typeof FullCalendar !== 'undefined' && window.calendarEvents) {
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
                    // Event clicked
                },
                dateClick: function(info) {
                    // Open modal with daily overview for clicked date
                    openModal(info.dateStr);
                },
                datesSet: function(dateInfo) {
                    // Debounced loading for better performance
                    clearTimeout(window.monthLoadTimer);
                    window.monthLoadTimer = setTimeout(() => {
                        loadMonthActivities(dateInfo.start);
                    }, 150);
                },
                dayCellDidMount: function(info) {
                    // Add activity indicators to calendar cells
                    addActivityIndicators(info.el, info.date);
                },
                loading: function(isLoading) {
                    // Show loading state
                    if (isLoading) {
                        calendarEl.style.opacity = '0.7';
                    } else {
                        calendarEl.style.opacity = '1';
                        // Trigger initial activity loading when calendar finishes loading
                        if (!window.initialActivitiesLoaded) {
                            window.initialActivitiesLoaded = true;
                            setTimeout(() => {
                                const currentDate = calendar.getDate();
                                loadMonthActivities(currentDate);
                            }, 200);
                        }
                    }
                }
            });
            calendar.render();
            
            // Store calendar instance globally
            window.calendarInstance = calendar;
            
            // Load initial activities for current month after DOM is ready
            setTimeout(() => {
                const currentDate = calendar.getDate();
                loadMonthActivities(currentDate);
            }, 100);
        }
    }, 500);

    // Function to load activities for a specific month
    async function loadMonthActivities(startDate) {
        const year = startDate.getFullYear();
        const month = startDate.getMonth() + 1; // JavaScript months are 0-based
        

        
        try {
            const response = await fetch(`/admin/academic/month-activities?year=${year}&month=${month}`);
            if (response.ok) {
                const newActivities = await response.json();

                
                // Update global activities
                window.calendarActivities = { ...window.calendarActivities, ...newActivities };
                
                // Refresh indicators for all visible cells
                refreshActivityIndicators();
            } else {
                console.error('Failed to fetch activities:', response.status, response.statusText);
            }
        } catch (error) {
            console.error('Failed to load month activities:', error);
        }
    }

    // Function to refresh activity indicators
    function refreshActivityIndicators() {
        if (!window.calendarInstance) return;
        
        // Remove existing indicators
        document.querySelectorAll('.activity-indicators').forEach(el => el.remove());
        
        // Wait a bit for calendar DOM to be ready, then re-add indicators
        setTimeout(() => {
            const dayCells = document.querySelectorAll('.fc-daygrid-day');

            
            dayCells.forEach(cell => {
                const dateStr = cell.getAttribute('data-date');
                if (dateStr) {
                    // Create date object with local timezone to avoid UTC shifts
                    const [year, month, day] = dateStr.split('-');
                    const date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
                    addActivityIndicators(cell, date);
                }
            });
        }, 50);
    }

    // Function to add activity indicators to calendar cells
    function addActivityIndicators(cellElement, date) {
        // Use local date formatting to avoid timezone issues
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const dateStr = `${year}-${month}-${day}`;
        const activities = window.calendarActivities[dateStr];
        

        
        if (!activities) return;
        
        // Remove existing indicators
        const existing = cellElement.querySelector('.activity-indicators');
        if (existing) existing.remove();
        
        // Create indicators container
        const indicatorsContainer = document.createElement('div');
        indicatorsContainer.className = 'activity-indicators';
        indicatorsContainer.style.cssText = `
            position: absolute;
            top: 3px;
            right: 3px;
            display: flex;
            gap: 3px;
            z-index: 5;
        `;
        
        // Equipment borrowing indicator
        if (activities.equipment_borrowing > 0) {
            const equipmentIndicator = createIndicator('equipment', activities.equipment_borrowing, 'Equipment Requests');
            indicatorsContainer.appendChild(equipmentIndicator);
        }
        
        // Lab reservations indicator
        if (activities.lab_reservations > 0) {
            const labIndicator = createIndicator('lab', activities.lab_reservations, 'Lab Reservations');
            indicatorsContainer.appendChild(labIndicator);
        }
        
        // Overdue items indicator (pulsing red)
        if (activities.overdue_equipment > 0) {
            const overdueIndicator = createIndicator('overdue', activities.overdue_equipment, 'Overdue Items', true);
            indicatorsContainer.appendChild(overdueIndicator);
        }
        
        // Make sure the cell is positioned relatively
        cellElement.style.position = 'relative';
        cellElement.appendChild(indicatorsContainer);
    }

    // Function to create individual activity indicators
    function createIndicator(color, count, tooltip, pulse = false) {
        const indicator = document.createElement('div');
        indicator.className = `activity-indicator ${color}-gradient ${pulse ? 'pulse-animation' : ''}`;
        
        const sizeMap = {
            small: { width: '8px', height: '8px' },
            medium: { width: '10px', height: '10px' },
            large: { width: '12px', height: '12px' }
        };
        
        // Determine size based on count
        let size = 'small';
        if (count > 5) size = 'large';
        else if (count > 2) size = 'medium';
        
        const dimensions = sizeMap[size];
        
        indicator.style.cssText = `
            width: ${dimensions.width};
            height: ${dimensions.height};
            border-radius: 50%;
            position: relative;
            cursor: pointer;
            border: 2px solid rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        // Add count badge if more than 1
        if (count > 1) {
            const badge = document.createElement('span');
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.cssText = `
                position: absolute;
                top: -8px;
                right: -8px;
                background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
                color: white;
                font-size: 9px;
                font-weight: 700;
                padding: 2px 4px;
                border-radius: 8px;
                line-height: 1;
                min-width: 14px;
                text-align: center;
                border: 1px solid rgba(255, 255, 255, 0.2);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            `;
            indicator.appendChild(badge);
        }
        
        // Enhanced tooltip with touch/click interaction
        indicator.title = `${tooltip}: ${count} item${count !== 1 ? 's' : ''}\nTap to view details`;
        
        // Add touch/click handlers for better mobile support
        const showDetails = function(e) {
            e.stopPropagation();
            e.preventDefault();
            showActivityDetails(tooltip, count, e.target);
        };
        
        indicator.addEventListener('click', showDetails);
        indicator.addEventListener('touchend', showDetails);
        
        return indicator;
    }

    // Function to show activity details in a tooltip with mobile optimization
    function showActivityDetails(type, count, element) {
        // Remove existing tooltips
        document.querySelectorAll('.activity-tooltip').forEach(el => el.remove());
        
        const tooltip = document.createElement('div');
        tooltip.className = 'activity-tooltip';
        
        // Mobile-optimized styling
        const isMobile = window.innerWidth <= 768;
        tooltip.style.cssText = `
            position: fixed;
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            color: white;
            padding: ${isMobile ? '10px 14px' : '8px 12px'};
            border-radius: 8px;
            font-size: ${isMobile ? '12px' : '11px'};
            font-weight: 500;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            max-width: ${isMobile ? '250px' : '200px'};
            touch-action: manipulation;
        `;
        
        tooltip.innerHTML = `
            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background: ${getIndicatorColor(type)};"></div>
                <span>${count} ${type}</span>
            </div>
            ${isMobile ? '<div style="font-size: 10px; opacity: 0.8; margin-top: 4px;">Tap anywhere to close</div>' : ''}
        `;
        
        // Smart positioning for mobile
        const rect = element.getBoundingClientRect();
        const tooltipWidth = isMobile ? 250 : 200;
        const tooltipHeight = isMobile ? 50 : 35;
        
        let left = rect.right + 5;
        let top = rect.top - 5;
        
        // Ensure tooltip stays within viewport
        if (left + tooltipWidth > window.innerWidth) {
            left = rect.left - tooltipWidth - 5;
        }
        if (top + tooltipHeight > window.innerHeight) {
            top = rect.bottom - tooltipHeight;
        }
        if (left < 5) left = 5;
        if (top < 5) top = 5;
        
        tooltip.style.left = left + 'px';
        tooltip.style.top = top + 'px';
        
        document.body.appendChild(tooltip);
        
        // Enhanced removal logic for mobile
        const removeTooltip = function() {
            if (tooltip.parentNode) tooltip.remove();
            document.removeEventListener('click', removeTooltip);
            document.removeEventListener('touchstart', removeTooltip);
        };
        
        // Auto-remove after 3 seconds on mobile, 2 on desktop
        setTimeout(() => {
            if (tooltip.parentNode) tooltip.remove();
        }, isMobile ? 3000 : 2000);
        
        // Remove on click/touch elsewhere
        setTimeout(() => {
            document.addEventListener('click', removeTooltip);
            document.addEventListener('touchstart', removeTooltip);
        }, 100);
    }

    // Helper function to get indicator color
    function getIndicatorColor(type) {
        const colorMap = {
            'Equipment Requests': '#3b82f6',  // Blue to match legend
            'Lab Reservations': '#8b5cf6',    // Purple to match legend
            'Overdue Items': '#ef4444'        // Red to match legend
        };
        return colorMap[type] || '#6b7280';
    }
});
</script>
@endpush