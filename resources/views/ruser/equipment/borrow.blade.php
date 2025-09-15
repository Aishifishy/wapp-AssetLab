@extends('layouts.ruser')

@section('title', 'Borrow Equipment')
@section('header', 'Borrow Equipment')

@section('content')
<div class="space-y-4">
    <!-- Breadcrumb Navigation -->
    @if(isset($selectedCategory))
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('ruser.equipment.borrow') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z" />
                    </svg>
                    Equipment Categories
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $selectedCategory->name }}</span>
                </div>
            </li>
        </ol>
    </nav>
    @endif

    <div class="bg-white shadow-sm rounded-lg">
        <div class="p-6">
            @if(isset($selectedCategory))
                <!-- Category Header -->
                <div class="mb-6 pb-6 border-b">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">{{ $selectedCategory->name }}</h2>
                            @if($selectedCategory->description)
                                <p class="text-gray-600 mt-1">{{ $selectedCategory->description }}</p>
                            @endif
                            <p class="text-sm text-gray-500 mt-2">
                                {{ $equipment->total() }} {{ Str::plural('item', $equipment->total()) }} in this category
                            </p>
                        </div>
                        <a href="{{ route('ruser.equipment.borrow') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Categories
                        </a>
                    </div>
                </div>

                <!-- Search Filter -->
                <div class="mb-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" id="search" placeholder="Search equipment in {{ $selectedCategory->name }}..." 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <select id="status-filter" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Status</option>
                            <option value="available">Available</option>
                            <option value="borrowed">Currently Borrowed</option>
                        </select>
                    </div>
                </div>
            @else
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Equipment Categories</h2>
                <p class="text-gray-600 mb-6">Select a category to browse available equipment for borrowing.</p>
                
                <!-- Category Selection Message -->
                <div class="text-center py-8">
                    <svg class="mx-auto h-16 w-16 text-blue-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14-7H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Browse Equipment by Category</h3>
                    <p class="text-sm text-gray-500 mb-4">Choose from our organized equipment categories to find what you need quickly.</p>
                    <a href="{{ route('ruser.equipment.borrow') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                        View Categories
                    </a>
                </div>
            @endif

            <!-- Equipment Grid -->
            @if(isset($selectedCategory) && $equipment->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($equipment as $item)
                <div class="bg-white border rounded-lg overflow-hidden hover:shadow-md transition-shadow" data-equipment-name="{{ strtolower($item->name) }}" data-status="{{ $item->status ?? 'available' }}">
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $item->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $selectedCategory->name }}</p>
                                @if($item->model)
                                    <p class="text-xs text-gray-500">Model: {{ $item->model }}</p>
                                @endif
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full status-badge
                                {{ ($item->status ?? 'available') === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}"
                                data-equipment-id="{{ $item->id }}">
                                {{ ucfirst($item->status ?? 'Available') }}
                            </span>
                        </div>
                        
                        @if($item->description)
                            <p class="mt-2 text-sm text-gray-600">{{ Str::limit($item->description, 100) }}</p>
                        @endif
                        
                        <div class="mt-4 flex items-center justify-between">
                            <div class="text-xs text-gray-500">
                                @if($item->brand)
                                    <span>{{ $item->brand }}</span>
                                @endif
                                @if($item->serial_number)
                                    <span class="ml-2">SN: {{ Str::limit($item->serial_number, 10) }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-4 space-y-2">
                            <!-- Always show the borrow button, but with different states -->
                            <button data-equipment-id="{{ $item->id }}" 
                                    data-equipment-name="{{ $item->name }}"
                                    data-equipment-status="{{ $item->status ?? 'available' }}"
                                    class="borrow-btn btn-primary w-full py-2 rounded-lg">
                                @if(($item->status ?? 'available') === 'available')
                                    Borrow Equipment
                                @else
                                    Schedule / Advance Book
                                @endif
                            </button>
                            
                            <!-- Quick availability info -->
                            <div class="text-xs text-center text-gray-500">
                                <span class="availability-status" data-equipment-id="{{ $item->id }}">
                                    Click to check availability
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $equipment->appends(request()->query())->links() }}
            </div>
            @elseif(isset($selectedCategory))
                <!-- No Equipment in Category -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14-7H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No Equipment Found</h3>
                    <p class="mt-2 text-sm text-gray-500">There are currently no equipment items available in the {{ $selectedCategory->name }} category.</p>
                    <div class="mt-4">
                        <a href="{{ route('ruser.equipment.borrow') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                            Browse Other Categories
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Borrow Modal -->
<div id="borrowModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white max-h-screen overflow-y-auto">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modalTitle">Borrow Equipment</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" data-action="close-modal" data-target="borrowModal">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Equipment Info -->
            <div id="equipmentInfo" class="mb-4 p-3 bg-gray-50 rounded-lg hidden">
                <h4 class="font-medium text-gray-900" id="equipmentName"></h4>
                <p class="text-sm text-gray-600" id="equipmentStatus"></p>
            </div>
            
            <form id="borrowForm" action="{{ route('ruser.equipment.request') }}" method="POST">
                @csrf
                <input type="hidden" name="equipment_id" id="equipment_id">
                
                <!-- Purpose Field -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="purpose">Purpose *</label>
                    <textarea name="purpose" id="purpose" required rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="Please describe why you need this equipment..."></textarea>
                </div>

                <!-- Date and Time Selection -->
                @php
                    $defaultFromTime = now()->addMinutes(1)->format('Y-m-d\TH:i');
                    $minTime = now()->addMinutes(1)->format('Y-m-d\TH:i');
                @endphp
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Borrowing Period *</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1" for="requested_from">From</label>
                            <input type="datetime-local" name="requested_from" id="requested_from" required
                                   min="{{ $minTime }}"
                                   value="{{ $defaultFromTime }}"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1" for="requested_until">Until</label>
                            <input type="datetime-local" name="requested_until" id="requested_until" required
                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        You can book equipment for immediate use or schedule it for future dates
                    </p>
                    <p class="text-xs text-blue-500 mt-1">
                        *If conflict checker has error please manually adjust the date and time by 1 minute of the current time.
                    </p>
                </div>
                
                <!-- Availability Check Result -->
                <div id="availabilityStatus" class="mb-4 hidden">
                    <!-- Will be populated by JavaScript -->
                </div>
                
                <!-- Conflict Information -->
                <div id="conflictInfo" class="mb-4 hidden">
                    <!-- Will be populated by JavaScript -->
                </div>
                
                <!-- Alternative Suggestions -->
                <div id="suggestions" class="mb-4 hidden">
                    <!-- Will be populated by JavaScript -->
                </div>

                <div class="flex justify-end mt-6 space-x-2">
                    <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" 
                            data-action="close-modal" data-target="borrowModal">
                        Cancel
                    </button>
                    <button type="button" id="checkAvailabilityBtn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Check Availability
                    </button>
                    <button type="submit" id="submitBookingBtn" class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 hidden">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // State variables
    let currentEquipmentId = null;
    let currentEquipmentStatus = null;
    let availabilityData = null;
    
    // Search functionality
    const searchInput = document.getElementById('search');
    const statusFilter = document.getElementById('status-filter');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterEquipment);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterEquipment);
    }
    
    function filterEquipment() {
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const statusValue = statusFilter?.value || '';
        const equipmentCards = document.querySelectorAll('[data-equipment-name]');
        
        equipmentCards.forEach(card => {
            const name = card.getAttribute('data-equipment-name');
            const status = card.getAttribute('data-status');
            
            const matchesSearch = name.includes(searchTerm);
            const matchesStatus = !statusValue || status === statusValue;
            
            if (matchesSearch && matchesStatus) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    // Modal controls
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-action="close-modal"]')) {
            const targetModal = document.getElementById(e.target.getAttribute('data-target'));
            if (targetModal) {
                targetModal.classList.add('hidden');
                resetForm();
            }
        }
    });
    
    // Borrow button handlers
    document.addEventListener('click', function(e) {
        if (e.target.matches('.borrow-btn')) {
            currentEquipmentId = e.target.getAttribute('data-equipment-id');
            currentEquipmentStatus = e.target.getAttribute('data-equipment-status');
            const equipmentName = e.target.getAttribute('data-equipment-name');
            
            openBorrowModal(currentEquipmentId, equipmentName, currentEquipmentStatus);
        }
    });
    
    function openBorrowModal(equipmentId, equipmentName, status) {
        // Set form values
        document.getElementById('equipment_id').value = equipmentId;
        document.getElementById('equipmentName').textContent = equipmentName;
        document.getElementById('equipmentStatus').textContent = `Status: ${status}`;
        
        // Show equipment info
        document.getElementById('equipmentInfo').classList.remove('hidden');
        
        // Set default dates (from now to 3 hours later)
        const now = new Date();
        const defaultEnd = new Date(now.getTime() + (3 * 60 * 60 * 1000)); // 3 hours later
        
        document.getElementById('requested_from').value = formatDateTimeLocal(now);
        document.getElementById('requested_until').value = formatDateTimeLocal(defaultEnd);
        
        // Show modal
        document.getElementById('borrowModal').classList.remove('hidden');
        
        // Focus on purpose field
        document.getElementById('purpose').focus();
    }
    
    function formatDateTimeLocal(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }
    
    // Check Availability button
    document.getElementById('checkAvailabilityBtn').addEventListener('click', checkAvailability);
    
    // Auto-check availability when dates change
    document.getElementById('requested_from').addEventListener('change', autoCheckAvailability);
    document.getElementById('requested_until').addEventListener('change', autoCheckAvailability);
    
    function autoCheckAvailability() {
        const fromDate = document.getElementById('requested_from').value;
        const untilDate = document.getElementById('requested_until').value;
        
        if (fromDate && untilDate && currentEquipmentId) {
            // Add a small delay to avoid excessive API calls
            clearTimeout(window.availabilityTimeout);
            window.availabilityTimeout = setTimeout(checkAvailability, 1000);
        }
    }
    
    async function checkAvailability() {
        const equipmentId = currentEquipmentId;
        const fromDate = document.getElementById('requested_from').value;
        const untilDate = document.getElementById('requested_until').value;
        
        if (!equipmentId || !fromDate || !untilDate) {
            showError('Please fill in all required fields.');
            return;
        }
        
        if (new Date(fromDate) >= new Date(untilDate)) {
            showError('End date must be after start date.');
            return;
        }
        
        // Show loading state
        const checkBtn = document.getElementById('checkAvailabilityBtn');
        const originalText = checkBtn.textContent;
        checkBtn.textContent = 'Checking...';
        checkBtn.disabled = true;
        
        try {
            const response = await fetch('{{ route("ruser.equipment.check-availability") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    equipment_id: equipmentId,
                    requested_from: fromDate,
                    requested_until: untilDate
                })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                displayAvailabilityResult(data);
            } else {
                showError(data.message || 'Failed to check availability.');
            }
        } catch (error) {
            console.error('Error checking availability:', error);
            showError('An error occurred while checking availability. Please try again.');
        } finally {
            // Restore button state
            checkBtn.textContent = originalText;
            checkBtn.disabled = false;
        }
    }
    
    function displayAvailabilityResult(data) {
        const statusDiv = document.getElementById('availabilityStatus');
        const conflictDiv = document.getElementById('conflictInfo');
        const suggestionsDiv = document.getElementById('suggestions');
        const submitBtn = document.getElementById('submitBookingBtn');
        
        // Clear previous results
        statusDiv.innerHTML = '';
        conflictDiv.innerHTML = '';
        suggestionsDiv.innerHTML = '';
        
        if (data.available) {
            // Equipment is available
            statusDiv.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Equipment Available</h3>
                            <div class="mt-1 text-sm text-green-700">
                                The equipment is available for your requested time period.
                            </div>
                        </div>
                    </div>
                </div>
            `;
            submitBtn.classList.remove('hidden');
        } else {
            // Equipment has conflicts
            statusDiv.innerHTML = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Scheduling Conflict</h3>
                            <div class="mt-1 text-sm text-yellow-700">
                                The equipment is not available for your requested time period.
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Show conflict details
            if (data.conflicts && data.conflicts.length > 0) {
                let conflictHtml = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-red-800 mb-2">Conflicts:</h4>
                        <ul class="text-sm text-red-700 space-y-1">
                `;
                
                data.conflicts.forEach(conflict => {
                    const fromDate = new Date(conflict.requested_from).toLocaleString();
                    const untilDate = new Date(conflict.requested_until).toLocaleString();
                    conflictHtml += `
                        <li class="flex items-start">
                            <span class="inline-block w-2 h-2 bg-red-400 rounded-full mt-1.5 mr-2 flex-shrink-0"></span>
                            <span>Already reserved from ${fromDate} to ${untilDate}</span>
                        </li>
                    `;
                });
                
                conflictHtml += '</ul></div>';
                conflictDiv.innerHTML = conflictHtml;
            }
            
            // Show alternative suggestions
            if (data.suggestions && data.suggestions.length > 0) {
                let suggestionsHtml = `
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Available Alternatives:</h4>
                        <div class="space-y-2">
                `;
                
                data.suggestions.forEach((suggestion, index) => {
                    const fromDate = new Date(suggestion.from).toLocaleString();
                    const untilDate = new Date(suggestion.until).toLocaleString();
                    suggestionsHtml += `
                        <button type="button" 
                                class="suggestion-btn w-full text-left p-3 bg-white border border-blue-200 rounded hover:bg-blue-50 transition-colors"
                                data-from="${suggestion.from}" 
                                data-until="${suggestion.until}">
                            <div class="text-sm font-medium text-blue-900">Option ${index + 1}</div>
                            <div class="text-sm text-blue-700">From: ${fromDate}</div>
                            <div class="text-sm text-blue-700">Until: ${untilDate}</div>
                        </button>
                    `;
                });
                
                suggestionsHtml += '</div></div>';
                suggestionsDiv.innerHTML = suggestionsHtml;
                
                // Add event listeners for suggestion buttons
                document.querySelectorAll('.suggestion-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const fromDate = this.getAttribute('data-from');
                        const untilDate = this.getAttribute('data-until');
                        
                        document.getElementById('requested_from').value = fromDate;
                        document.getElementById('requested_until').value = untilDate;
                        
                        // Re-check availability with new dates
                        checkAvailability();
                    });
                });
            }
            
            // Allow advanced booking (queue system)
            if (data.can_queue) {
                submitBtn.classList.remove('hidden');
                submitBtn.textContent = 'Join Queue';
                submitBtn.className = submitBtn.className.replace('bg-green-600 hover:bg-green-700', 'bg-purple-600 hover:bg-purple-700');
            } else {
                submitBtn.classList.add('hidden');
            }
        }
        
        // Show all result sections
        statusDiv.classList.remove('hidden');
        if (conflictDiv.innerHTML) conflictDiv.classList.remove('hidden');
        if (suggestionsDiv.innerHTML) suggestionsDiv.classList.remove('hidden');
    }
    
    function showError(message) {
        const statusDiv = document.getElementById('availabilityStatus');
        statusDiv.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Error</h3>
                        <div class="mt-1 text-sm text-red-700">${message}</div>
                    </div>
                </div>
            </div>
        `;
        statusDiv.classList.remove('hidden');
    }
    
    function resetForm() {
        // Reset form values
        document.getElementById('borrowForm').reset();
        document.getElementById('equipment_id').value = '';
        
        // Hide all status sections
        document.getElementById('availabilityStatus').classList.add('hidden');
        document.getElementById('conflictInfo').classList.add('hidden');
        document.getElementById('suggestions').classList.add('hidden');
        document.getElementById('equipmentInfo').classList.add('hidden');
        document.getElementById('submitBookingBtn').classList.add('hidden');
        
        // Reset button state
        const submitBtn = document.getElementById('submitBookingBtn');
        submitBtn.textContent = 'Submit Request';
        submitBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 hidden';
        
        // Reset state
        currentEquipmentId = null;
        currentEquipmentStatus = null;
        availabilityData = null;
    }
    
    // Real-time status polling functionality
    let statusPollingInterval = null;
    let pollingEnabled = true;
    const POLLING_INTERVAL = 30000; // 30 seconds
    
    function startStatusPolling() {
        if (statusPollingInterval) {
            clearInterval(statusPollingInterval);
        }
        
        statusPollingInterval = setInterval(async () => {
            if (!pollingEnabled) return;
            
            try {
                await updateEquipmentStatuses();
            } catch (error) {
                console.error('Error updating equipment statuses:', error);
            }
        }, POLLING_INTERVAL);
        
        console.log('Real-time status polling started');
    }
    
    function stopStatusPolling() {
        if (statusPollingInterval) {
            clearInterval(statusPollingInterval);
            statusPollingInterval = null;
            console.log('Real-time status polling stopped');
        }
    }
    
    async function updateEquipmentStatuses() {
        try {
            const response = await fetch('{{ route("ruser.equipment.status-update") }}', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.equipment && Array.isArray(data.equipment)) {
                updateStatusBadges(data.equipment);
                updateAvailabilityStatuses(data.equipment);
            }
        } catch (error) {
            console.error('Failed to fetch equipment statuses:', error);
        }
    }
    
    function updateStatusBadges(equipmentData) {
        equipmentData.forEach(equipment => {
            const statusBadge = document.querySelector(`[data-equipment-id="${equipment.id}"].status-badge`);
            const card = statusBadge?.closest('[data-equipment-name]');
            
            if (statusBadge) {
                const statusClasses = getStatusBadgeClasses(equipment.status);
                const statusText = getStatusDisplayText(equipment.status);
                
                statusBadge.className = `px-2 py-1 text-xs font-semibold rounded-full status-badge ${statusClasses}`;
                statusBadge.textContent = statusText;
                
                if (card) {
                    card.setAttribute('data-status', equipment.status);
                }
            }
        });
    }
    
    function updateAvailabilityStatuses(equipmentData) {
        equipmentData.forEach(equipment => {
            const availabilityStatus = document.querySelector(`[data-equipment-id="${equipment.id}"].availability-status`);
            
            if (availabilityStatus) {
                availabilityStatus.textContent = getAvailabilityStatusText(equipment.status);
            }
        });
    }
    
    function getStatusBadgeClasses(status) {
        const statusMap = {
            'available': 'bg-green-100 text-green-800',
            'borrowed': 'bg-yellow-100 text-yellow-800',
            'unavailable': 'bg-red-100 text-red-800',
            'maintenance': 'bg-red-100 text-red-800'
        };
        
        return statusMap[status] || 'bg-gray-100 text-gray-800';
    }
    
    function getStatusDisplayText(status) {
        const statusMap = {
            'available': 'Available',
            'borrowed': 'Borrowed',
            'unavailable': 'Unavailable',
            'maintenance': 'Under Maintenance'
        };
        
        return statusMap[status] || status.charAt(0).toUpperCase() + status.slice(1);
    }
    
    function getAvailabilityStatusText(status) {
        const statusMap = {
            'available': 'Click to check availability',
            'borrowed': 'Currently borrowed',
            'unavailable': 'Currently unavailable',
            'maintenance': 'Under maintenance'
        };
        
        return statusMap[status] || 'Status unknown';
    }
    
    // Pause polling when user is actively interacting
    function pausePolling() {
        pollingEnabled = false;
    }
    
    function resumePolling() {
        pollingEnabled = true;
    }
    
    // Pause polling during user interactions
    document.addEventListener('focus', pausePolling);
    document.addEventListener('blur', resumePolling);
    
    // Pause polling when modal is open
    document.addEventListener('click', function(e) {
        if (e.target.matches('.borrow-btn')) {
            pausePolling();
        }
    });
    
    // Resume polling when modal is closed
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-action="close-modal"]')) {
            setTimeout(resumePolling, 1000);
        }
    });
    
    // Start polling when page loads
    startStatusPolling();
    
    // Clean up polling when page unloads
    window.addEventListener('beforeunload', stopStatusPolling);
    
    // Add visual indicator for real-time updates
    function addRealTimeIndicator() {
        const indicator = document.createElement('div');
        indicator.id = 'realtime-indicator';
        indicator.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-xs flex items-center space-x-1 z-50';
        indicator.innerHTML = `
            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
            <span>Live</span>
        `;
        document.body.appendChild(indicator);
        
        // Update indicator every 30 seconds
        setInterval(() => {
            const dot = indicator.querySelector('.w-2');
            if (dot) {
                dot.classList.add('animate-pulse');
                setTimeout(() => dot.classList.remove('animate-pulse'), 1000);
            }
        }, POLLING_INTERVAL);
    }
    
    // Add the real-time indicator
    addRealTimeIndicator();
</script>
@endpush