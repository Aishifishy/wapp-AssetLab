<?php $__env->startSection('title', 'Academic Calendar'); ?>

<?php $__env->startPush('styles'); ?>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<!-- Styles moved to app.css -->
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Academic Calendar</h1>
        <div class="flex space-x-3">
            <form action="<?php echo e(route('admin.academic.set-current-by-date')); ?>" method="POST" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition">
                    <i class="fas fa-magic mr-2"></i> Auto-Set Current
                </button>
            </form>
            <a href="<?php echo e(route('admin.academic.create')); ?>" 
               class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition">
                <i class="fas fa-plus mr-2"></i> Add New Academic Year
            </a>
        </div>
    </div>    <!-- Flash Messages -->
    <?php if (isset($component)) { $__componentOriginalcca61bfded94b5a7635453a4dc55dd1d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d = $attributes; } ?>
<?php $component = App\View\Components\FlashMessages::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flash-messages'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\FlashMessages::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d)): ?>
<?php $attributes = $__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d; ?>
<?php unset($__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcca61bfded94b5a7635453a4dc55dd1d)): ?>
<?php $component = $__componentOriginalcca61bfded94b5a7635453a4dc55dd1d; ?>
<?php unset($__componentOriginalcca61bfded94b5a7635453a4dc55dd1d); ?>
<?php endif; ?>

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
                        <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($year->id); ?>" 
                                    data-current="<?php echo e($year->terms->contains('is_current', true) ? 'true' : 'false'); ?>"
                                    <?php echo e($year->terms->contains('is_current', true) ? 'selected' : ''); ?>>
                                <?php echo e($year->name); ?> (<?php echo e($year->start_date->format('M Y')); ?> - <?php echo e($year->end_date->format('M Y')); ?>)
                                <?php if($year->terms->contains('is_current', true)): ?> - Current <?php endif; ?>
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <i class="fas fa-calendar-alt text-gray-500 mr-3"></i>
                    Academic Calendar Overview
                </h3>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-blue-500 rounded"></div>
                        <span class="text-sm text-gray-600">Academic Terms</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-green-500 rounded"></div>
                        <span class="text-sm text-gray-600">Current Term</span>
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
                        <div id="labs-tab" class="modal-tab-content hidden">
                            <div class="space-y-6" id="labsList">
                                <!-- Lab schedules will be populated here -->
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
// Make calendar events and academic years data available to JavaScript
window.calendarEvents = <?php echo json_encode($calendarEvents); ?>;
window.academicYearsData = <?php echo json_encode($academicYears->map(function($year) {
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
})->values()); ?>;

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
        
        if (labs.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center py-8">No computer lab schedules found for this day.</p>';
            return;
        }
        
        labs.forEach(lab => {
            const labCard = document.createElement('div');
            labCard.className = 'bg-white border border-gray-200 rounded-lg p-6';
            
            let schedulesHtml = '';
            if (lab.schedules.length === 0) {
                schedulesHtml = '<p class="text-gray-500 text-sm">No scheduled classes</p>';
            } else {
                lab.schedules.forEach(schedule => {
                    schedulesHtml += `
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mb-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h6 class="font-medium text-gray-900">${schedule.subject_code} - ${schedule.subject_name}</h6>
                                    <p class="text-sm text-gray-600">Instructor: ${schedule.instructor}</p>
                                    <p class="text-sm text-gray-600">Section: ${schedule.section}</p>
                                </div>
                                <span class="text-sm font-medium text-blue-600">${schedule.time_range}</span>
                            </div>
                        </div>
                    `;
                });
            }
            
            let availableSlotsHtml = '<div class="mt-4"><h6 class="font-medium text-gray-700 mb-2">Available Time Slots:</h6><div class="grid grid-cols-4 gap-2">';
            lab.available_slots.forEach(slot => {
                const slotClass = slot.available ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200';
                availableSlotsHtml += `
                    <div class="px-2 py-1 text-xs border rounded ${slotClass}">
                        ${slot.time}
                    </div>
                `;
            });
            availableSlotsHtml += '</div></div>';
            
            labCard.innerHTML = `
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h5 class="text-lg font-medium text-gray-900">${lab.lab_name}</h5>
                        <p class="text-sm text-gray-600">Capacity: ${lab.capacity} students | Computers: ${lab.computers}</p>
                    </div>
                </div>
                <div>
                    <h6 class="font-medium text-gray-700 mb-3">Today's Schedule:</h6>
                    ${schedulesHtml}
                </div>
                ${availableSlotsHtml}
            `;
            
            container.appendChild(labCard);
        });
    }

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
                }
            });
            calendar.render();
        }
    }, 500);
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views/admin/academic/index.blade.php ENDPATH**/ ?>