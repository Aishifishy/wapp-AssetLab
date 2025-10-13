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

                <!-- Enhanced Search & Filter -->
                <div class="mb-8">
                    <div class="glass-card rounded-2xl p-6">
                        <div class="flex flex-col lg:flex-row gap-4">
                            <!-- Search Input -->
                            <div class="flex-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" id="search" placeholder="Search equipment in {{ $selectedCategory->name }}..." 
                                    class="w-full pl-12 pr-4 py-3 bg-white/70 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400">
                            </div>
                            
                            <!-- Status Filter -->
                            <div class="lg:w-64">
                                <select id="status-filter" class="w-full px-4 py-3 bg-white/70 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">All Status</option>
                                    <option value="available">🟢 Available</option>
                                    <option value="borrowed">🟡 Currently Borrowed</option>
                                    <option value="maintenance">🔴 Under Maintenance</option>
                                </select>
                            </div>
                            

                            

                        </div>
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

            <!-- Enhanced Equipment Grid -->
            @if(isset($selectedCategory) && $equipment->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @foreach($equipment as $item)
                <div class="glass-card rounded-2xl overflow-hidden border-0" data-equipment-name="{{ strtolower($item->name) }}" data-status="{{ $item->status ?? 'available' }}">
                    <!-- Status Indicator Bar -->
                    <div class="h-1 bg-gradient-to-r {{ ($item->status ?? 'available') === 'available' ? 'from-emerald-400 to-emerald-600' : (($item->status ?? 'available') === 'borrowed' ? 'from-amber-400 to-amber-600' : 'from-red-400 to-red-600') }}"></div>
                    
                    <div class="p-6">
                        <!-- Header Section -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 12l-6-3"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold text-gray-900 truncate">{{ $item->name }}</h3>
                                        <p class="text-sm text-gray-600 font-medium">{{ $selectedCategory->name }}</p>
                                        @if($item->model)
                                            <p class="text-xs text-gray-500 mt-1 bg-gray-50 px-2 py-1 rounded-md inline-block">{{ $item->model }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Enhanced Status Badge -->
                            <div class="flex flex-col items-end">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold status-badge
                                    {{ ($item->status ?? 'available') === 'available' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 
                                       (($item->status ?? 'available') === 'borrowed' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-red-100 text-red-800 border border-red-200') }}"
                                    data-equipment-id="{{ $item->id }}">
                                    @if(($item->status ?? 'available') === 'available')
                                        🟢 Available Now
                                    @elseif(($item->status ?? 'available') === 'borrowed')
                                        🟡 Currently Borrowed
                                    @else
                                        🔴 Unavailable
                                    @endif
                                </span>
                                @if(($item->status ?? 'available') === 'borrowed')
                                    <div class="text-xs text-blue-600 mt-1 font-medium">Available for advance booking</div>
                                @else
                                    <div class="text-xs text-gray-400 mt-1">ID: {{ $item->id }}</div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Description -->
                        @if($item->description)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 leading-relaxed bg-gray-50 p-3 rounded-xl">{{ Str::limit($item->description, 120) }}</p>
                            </div>
                        @endif
                        
                        <!-- Equipment Details -->
                        <div class="mb-6 space-y-2">
                            @if($item->brand)
                                <div class="flex items-center text-xs text-gray-500">
                                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span class="font-medium">Brand:</span> {{ $item->brand }}
                                </div>
                            @endif
                            @if($item->serial_number)
                                <div class="flex items-center text-xs text-gray-500">
                                    <svg class="w-4 h-4 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="font-medium">S/N:</span> {{ Str::limit($item->serial_number, 15) }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Action Section -->
                        <div class="space-y-3">
                            <!-- Enhanced Borrow Button -->
                            <button data-equipment-id="{{ $item->id }}" 
                                    data-equipment-name="{{ $item->name }}"
                                    data-equipment-status="{{ $item->status ?? 'available' }}"
                                    class="borrow-btn w-full py-3 px-4 rounded-xl font-semibold transition-all duration-200 hover:scale-105
                                        {{ ($item->status ?? 'available') === 'available' ? 
                                           'bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white shadow-lg' : 
                                           (($item->status ?? 'available') === 'borrowed' ?
                                           'bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white shadow-lg' :
                                           'bg-gradient-to-r from-gray-400 to-gray-500 text-white shadow-lg cursor-not-allowed opacity-60') }}"
                                    {{ ($item->status ?? 'available') !== 'available' && ($item->status ?? 'available') !== 'borrowed' ? 'disabled' : '' }}>
                                
                                <div class="flex items-center justify-center space-x-2">
                                    @if(($item->status ?? 'available') === 'available')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        <span>Borrow Now</span>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Schedule Booking</span>
                                    @endif
                                </div>
                            </button>
                            
                            <!-- Availability Status -->
                            <div class="text-center">
                                <span class="availability-status inline-flex items-center text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full transition-all duration-300 hover:bg-blue-50 hover:text-blue-600" data-equipment-id="{{ $item->id }}">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Click to check real-time availability
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
            <div id="equipmentInfo" class="mb-4 p-4 bg-gray-50 rounded-lg hidden">
                <h4 class="font-medium text-gray-900" id="equipmentName"></h4>
                <p class="text-sm text-gray-600" id="equipmentStatus"></p>
                <div id="bookingTypeInfo" class="hidden mt-2 p-3 rounded-lg border-l-4">
                    <p class="text-sm font-medium" id="bookingMessage"></p>
                </div>
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
                    $now = now();
                    $currentDate = $now->format('Y-m-d');
                    $currentTime = $now->format('H:i');
                    $currentHour = $now->hour;
                    $currentMinute = $now->minute;
                    
                    // Calculate minimum allowed time for today (current time rounded down to nearest 30-min slot)
                    $currentMinuteSlot = floor($currentMinute / 30) * 30;
                    $minTimeToday = sprintf('%02d:%02d', $currentHour, $currentMinuteSlot);
                    
                    // If current time is past 9 PM, no slots available today
                    if ($currentHour >= 21) {
                        $minTimeToday = '22:00'; // This will make all slots unavailable for today
                    }
                    
                    // Calculate default date and time
                    if ($currentHour < 7) {
                        $defaultDate = $currentDate;
                        $defaultTime = '07:00';
                    } elseif ($currentHour >= 21) {
                        $defaultDate = $now->copy()->addDay()->format('Y-m-d');
                        $defaultTime = '07:00';
                    } else {
                        $defaultDate = $currentDate;
                        // Round up to next available 30-minute slot
                        $nextMinuteSlot = ceil($currentMinute / 30) * 30;
                        if ($nextMinuteSlot >= 60) {
                            $defaultHour = $currentHour + 1;
                            $nextMinuteSlot = 0;
                        } else {
                            $defaultHour = $currentHour;
                        }
                        
                        // Ensure within office hours
                        if ($defaultHour > 21) {
                            $defaultDate = $now->copy()->addDay()->format('Y-m-d');
                            $defaultTime = '07:00';
                        } else {
                            $defaultTime = sprintf('%02d:%02d', $defaultHour, $nextMinuteSlot);
                        }
                    }
                @endphp
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Borrowing Period *</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1" for="requested_from">From Date & Time</label>
                            <div class="space-y-2">
                                <input type="date" name="requested_from_date" id="requested_from_date" required
                                       min="{{ $currentDate }}"
                                       value="{{ $defaultDate }}"
                                       data-current-date="{{ $currentDate }}"
                                       data-min-time-today="{{ $minTimeToday }}"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <select name="requested_from_time" id="requested_from_time" required
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Select time</option>
                                    @for($hour = 7; $hour <= 21; $hour++)
                                        @for($minute = 0; $minute < 60; $minute += 30)
                                            @php
                                                $timeValue = sprintf('%02d:%02d', $hour, $minute);
                                                $timeDisplay = date('g:i A', strtotime($timeValue));
                                            @endphp
                                            <option value="{{ $timeValue }}" {{ $timeValue === $defaultTime ? 'selected' : '' }}>{{ $timeDisplay }}</option>
                                        @endfor
                                    @endfor
                                </select>
                            </div>
                            <input type="hidden" name="requested_from" id="requested_from">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1" for="requested_until">Until Date & Time</label>
                            <div class="space-y-2">
                                <input type="date" name="requested_until_date" id="requested_until_date" required
                                       min="{{ $currentDate }}"
                                       value="{{ $defaultDate }}"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <select name="requested_until_time" id="requested_until_time" required
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Select time</option>
                                    @for($hour = 7; $hour <= 21; $hour++)
                                        @for($minute = 0; $minute < 60; $minute += 30)
                                            @php
                                                $timeValue = sprintf('%02d:%02d', $hour, $minute);
                                                $timeDisplay = date('g:i A', strtotime($timeValue));
                                            @endphp
                                            <option value="{{ $timeValue }}">{{ $timeDisplay }}</option>
                                        @endfor
                                    @endfor
                                </select>
                            </div>
                            <input type="hidden" name="requested_until" id="requested_until">
                        </div>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-2">
                        <div class="flex items-start">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-xs text-blue-700">
                                <p class="font-medium mb-1">📅 Office Hours: 7:00 AM - 9:00 PM</p>
                                <p>Equipment can only be borrowed and returned during office hours to ensure proper handling and security.</p>
                            </div>
                        </div>
                    </div>
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
        if (e.target.matches('.borrow-btn') || e.target.closest('.borrow-btn')) {
            const button = e.target.matches('.borrow-btn') ? e.target : e.target.closest('.borrow-btn');
            
            currentEquipmentId = button.getAttribute('data-equipment-id');
            currentEquipmentStatus = button.getAttribute('data-equipment-status');
            const equipmentName = button.getAttribute('data-equipment-name');
            
            console.log('Borrow button clicked for equipment:', equipmentName);
            console.log('Equipment ID set to:', currentEquipmentId);
            console.log('Equipment Status:', currentEquipmentStatus);
            console.log('Button element:', button);
            console.log('All button attributes:', {
                id: button.getAttribute('data-equipment-id'),
                name: button.getAttribute('data-equipment-name'),
                status: button.getAttribute('data-equipment-status')
            });
            
            if (!currentEquipmentId) {
                console.error('Equipment ID is missing from button attributes!');
                showError('Equipment ID is missing. Please try again.');
                return;
            }
            
            openBorrowModal(currentEquipmentId, equipmentName, currentEquipmentStatus);
        }
    });
    
    function openBorrowModal(equipmentId, equipmentName, status) {
        console.log('openBorrowModal called with:', { equipmentId, equipmentName, status });
        
        // Set form values
        document.getElementById('equipment_id').value = equipmentId;
        document.getElementById('equipmentName').textContent = equipmentName;
        
        // Update status display based on equipment status
        const statusElement = document.getElementById('equipmentStatus');
        const bookingTypeInfo = document.getElementById('bookingTypeInfo');
        const bookingMessage = document.getElementById('bookingMessage');
        
        if (status === 'borrowed') {
            statusElement.textContent = 'Status: Currently Borrowed';
            statusElement.className = 'text-sm text-amber-600 font-medium';
            
            bookingTypeInfo.className = 'mt-2 p-3 rounded-lg border-l-4 border-blue-400 bg-blue-50';
            bookingMessage.textContent = '📅 This equipment is currently borrowed. You can schedule it for future use (advance booking).';
            bookingMessage.className = 'text-sm font-medium text-blue-700';
            bookingTypeInfo.classList.remove('hidden');
        } else if (status === 'available') {
            statusElement.textContent = 'Status: Available';
            statusElement.className = 'text-sm text-emerald-600 font-medium';
            
            bookingTypeInfo.className = 'mt-2 p-3 rounded-lg border-l-4 border-green-400 bg-green-50';
            bookingMessage.textContent = '✅ This equipment is available for immediate borrowing.';
            bookingMessage.className = 'text-sm font-medium text-green-700';
            bookingTypeInfo.classList.remove('hidden');
        } else {
            statusElement.textContent = `Status: ${status}`;
            statusElement.className = 'text-sm text-gray-600';
            bookingTypeInfo.classList.add('hidden');
        }
        
        // Verify the hidden input was set correctly
        console.log('Hidden input value after setting:', document.getElementById('equipment_id').value);
        
        // Show equipment info
        document.getElementById('equipmentInfo').classList.remove('hidden');
        
        // Update current time data attributes to ensure accurate constraints
        updateCurrentTimeData();
        
        // Update available time slots based on current time
        updateAvailableTimeSlots();
        
        // Combine the initial date/time inputs
        combineDateTimeInputs();
        
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
    document.getElementById('checkAvailabilityBtn').addEventListener('click', function() {
        console.log('Check availability button clicked');
        console.log('Current state before check:', {
            currentEquipmentId: currentEquipmentId,
            formEquipmentId: document.getElementById('equipment_id').value
        });
        checkAvailability();
    });

    // Simple notification function
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg ${
            type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
            type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
            'bg-blue-100 text-blue-800 border border-blue-200'
        }`;
        notification.innerHTML = `
            <div class="flex items-start">
                <div class="flex-1 text-sm">${message}</div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    // Initialize default values on page load
    initializeDefaultTimes();

    // Auto-check availability when dates change
    document.getElementById('requested_from_date').addEventListener('change', handleTimeChange);
    document.getElementById('requested_from_time').addEventListener('change', handleTimeChange);
    document.getElementById('requested_until_date').addEventListener('change', handleTimeChange);
    document.getElementById('requested_until_time').addEventListener('change', handleTimeChange);
    
    function initializeDefaultTimes() {
        // Initialize with default values and update time slots based on current constraints
        updateAvailableTimeSlots();
        combineDateTimeInputs();
    }

    // Function to update current time data for accurate time constraints
    function updateCurrentTimeData() {
        const now = new Date();
        const currentDate = now.toISOString().split('T')[0];
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();
        
        // Calculate minimum time (current exact time for precise comparison)
        const exactCurrentTime = String(currentHour).padStart(2, '0') + ':' + String(currentMinute).padStart(2, '0');
        
        // Also calculate rounded down time for 30-minute slot comparison
        const currentMinuteSlot = Math.floor(currentMinute / 30) * 30;
        const roundedMinTime = String(currentHour).padStart(2, '0') + ':' + String(currentMinuteSlot).padStart(2, '0');
        
        // Update data attributes
        const fromDateInput = document.getElementById('requested_from_date');
        fromDateInput.setAttribute('data-current-date', currentDate);
        fromDateInput.setAttribute('data-min-time-today', currentHour >= 21 ? '22:00' : roundedMinTime);
        fromDateInput.setAttribute('data-exact-current-time', exactCurrentTime);
    }

    // Function to update available time slots based on selected date
    function updateAvailableTimeSlots() {
        updateFromTimeSlots();
        updateUntilTimeSlots();
    }

    // Function to update "from" time slots
    function updateFromTimeSlots() {
        const fromDateInput = document.getElementById('requested_from_date');
        const fromTimeSelect = document.getElementById('requested_from_time');
        const selectedDate = fromDateInput.value;
        const currentDate = fromDateInput.getAttribute('data-current-date');
        const minTimeToday = fromDateInput.getAttribute('data-min-time-today');
        
        // Store current selection
        const previousSelection = fromTimeSelect.value;
        
        // Clear all options except the placeholder
        fromTimeSelect.innerHTML = '<option value="">Select time</option>';
        
        const isToday = selectedDate === currentDate;
        
        // Add current exact time option for today
        if (isToday) {
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();
            
            // Only add current time if it's within office hours
            if (currentHour >= 7 && currentHour < 21) {
                const currentTimeValue = String(currentHour).padStart(2, '0') + ':' + String(currentMinute).padStart(2, '0');
                const currentTimeDisplay = now.toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                }) + ' (current time)';
                
                const currentOption = new Option(currentTimeDisplay, currentTimeValue);
                fromTimeSelect.appendChild(currentOption);
            }
        }
        
        // Generate regular 30-minute interval slots
        for (let hour = 7; hour <= 21; hour++) {
            for (let minute = 0; minute < 60; minute += 30) {
                const timeValue = String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');
                const timeDisplay = new Date('2000-01-01 ' + timeValue).toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
                
                // For today, only include current and future time slots
                if (isToday) {
                    // Get current exact time for precise comparison
                    const now = new Date();
                    const currentExactTime = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                    
                    // Only include 30-minute slots that are in the future compared to current exact time
                    if (timeValue > currentExactTime) {
                        const option = new Option(timeDisplay, timeValue);
                        fromTimeSelect.appendChild(option);
                    }
                } else {
                    // For future dates, include all office hour slots
                    const option = new Option(timeDisplay, timeValue);
                    fromTimeSelect.appendChild(option);
                }
            }
        }
        
        // Try to restore previous selection if it's still available
        if (previousSelection && Array.from(fromTimeSelect.options).some(opt => opt.value === previousSelection)) {
            fromTimeSelect.value = previousSelection;
        } else {
            // Select the current time option if it exists (index 1), otherwise first regular slot
            if (fromTimeSelect.options.length > 1) {
                fromTimeSelect.selectedIndex = 1;
            }
        }
    }

    // Function to update "until" time slots
    function updateUntilTimeSlots() {
        const fromDateInput = document.getElementById('requested_from_date');
        const untilDateInput = document.getElementById('requested_until_date');
        const untilTimeSelect = document.getElementById('requested_until_time');
        const fromTimeSelect = document.getElementById('requested_from_time');
        
        const selectedUntilDate = untilDateInput.value;
        const selectedFromDate = fromDateInput.value;
        const currentDate = fromDateInput.getAttribute('data-current-date');
        const minTimeToday = fromDateInput.getAttribute('data-min-time-today');
        const fromTime = fromTimeSelect.value;
        
        // Store current selection
        const previousSelection = untilTimeSelect.value;
        
        // Clear all options except the placeholder
        untilTimeSelect.innerHTML = '<option value="">Select time</option>';
        
        const isToday = selectedUntilDate === currentDate;
        const isSameDayAsFrom = selectedUntilDate === selectedFromDate;
        
        // Determine minimum time for until slot
        let minUntilTime = '07:00';
        
        if (isToday) {
            // For today, use current time as minimum
            minUntilTime = minTimeToday;
        }
        
        if (isSameDayAsFrom && fromTime) {
            // If same day as from date and from time is selected, until time must be after from time
            const [fromHour, fromMinute] = fromTime.split(':').map(Number);
            
            // Calculate minimum time: from time + 30 minutes
            const fromDateTime = new Date();
            fromDateTime.setHours(fromHour, fromMinute, 0, 0);
            const minDateTime = new Date(fromDateTime.getTime() + 30 * 60000); // Add 30 minutes
            
            // Round up to next 30-minute slot if not already on one
            let minHour = minDateTime.getHours();
            let minMinute = minDateTime.getMinutes();
            
            if (minMinute > 0 && minMinute <= 30) {
                minMinute = 30;
            } else if (minMinute > 30) {
                minHour += 1;
                minMinute = 0;
            }
            
            // Ensure within office hours
            if (minHour > 21) {
                minHour = 21;
                minMinute = 30;
            }
            
            const calculatedMinTime = String(minHour).padStart(2, '0') + ':' + String(minMinute).padStart(2, '0');
            minUntilTime = calculatedMinTime > minUntilTime ? calculatedMinTime : minUntilTime;
        }
        
        // Generate time slots based on constraints
        for (let hour = 7; hour <= 21; hour++) {
            for (let minute = 0; minute < 60; minute += 30) {
                const timeValue = String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');
                const timeDisplay = new Date('2000-01-01 ' + timeValue).toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
                
                // Only include time slots that meet the minimum time requirement
                if (timeValue >= minUntilTime) {
                    const option = new Option(timeDisplay, timeValue);
                    untilTimeSelect.appendChild(option);
                }
            }
        }
        
        // Try to restore previous selection if it's still available
        if (previousSelection && Array.from(untilTimeSelect.options).some(opt => opt.value === previousSelection)) {
            untilTimeSelect.value = previousSelection;
        } else {
            // Select the first available time slot (after placeholder)
            if (untilTimeSelect.options.length > 1) {
                untilTimeSelect.selectedIndex = 1;
            }
        }
        
        // Update until date minimum
        untilDateInput.min = selectedFromDate;
        
        // If until date is before from date, update it
        if (untilDateInput.value < selectedFromDate) {
            untilDateInput.value = selectedFromDate;
        }
    }

    // Function to combine date and time inputs
    function combineDateTimeInputs() {
        const fromDate = document.getElementById('requested_from_date').value;
        const fromTime = document.getElementById('requested_from_time').value;
        const untilDate = document.getElementById('requested_until_date').value;
        const untilTime = document.getElementById('requested_until_time').value;
        
        if (fromDate && fromTime) {
            document.getElementById('requested_from').value = fromDate + 'T' + fromTime;
        }
        
        if (untilDate && untilTime) {
            document.getElementById('requested_until').value = untilDate + 'T' + untilTime;
        }
    }

    // Function to validate that until time is after from time
    function validateTimeOrder() {
        const fromDate = document.getElementById('requested_from_date').value;
        const fromTime = document.getElementById('requested_from_time').value;
        const untilDate = document.getElementById('requested_until_date').value;
        const untilTime = document.getElementById('requested_until_time').value;
        
        if (fromDate && fromTime && untilDate && untilTime) {
            const fromDateTime = new Date(fromDate + 'T' + fromTime);
            const untilDateTime = new Date(untilDate + 'T' + untilTime);
            
            if (untilDateTime <= fromDateTime) {
                // Auto-adjust until time to be at least 30 minutes after from time
                const suggestedUntil = new Date(fromDateTime.getTime() + 30 * 60000);
                
                // If suggested time is beyond office hours, set to next day 7 AM
                if (suggestedUntil.getHours() > 21 || (suggestedUntil.getHours() === 21 && suggestedUntil.getMinutes() > 0)) {
                    const nextDay = new Date(suggestedUntil);
                    nextDay.setDate(nextDay.getDate() + 1);
                    nextDay.setHours(7, 0, 0, 0);
                    
                    document.getElementById('requested_until_date').value = nextDay.toISOString().split('T')[0];
                    document.getElementById('requested_until_time').value = '07:00';
                } else {
                    document.getElementById('requested_until_date').value = suggestedUntil.toISOString().split('T')[0];
                    
                    // Round to nearest 30-minute slot
                    const minutes = suggestedUntil.getMinutes();
                    const roundedMinutes = Math.ceil(minutes / 30) * 30;
                    const adjustedTime = new Date(suggestedUntil.setMinutes(roundedMinutes, 0, 0));
                    document.getElementById('requested_until_time').value = adjustedTime.toTimeString().slice(0, 5);
                }
                
                combineDateTimeInputs();
                showNotification('Until time has been automatically adjusted to be after the from time.', 'info');
            }
        }
    }

    function handleTimeChange(event) {
        // Update time slots based on what changed
        if (event && event.target) {
            if (event.target.id === 'requested_from_date') {
                // From date changed - update both from and until time slots
                updateAvailableTimeSlots();
            } else if (event.target.id === 'requested_until_date') {
                // Until date changed - update until time slots
                updateUntilTimeSlots();
            } else if (event.target.id === 'requested_from_time') {
                // From time changed - update until time slots to ensure proper minimum
                updateUntilTimeSlots();
            }
        }
        
        combineDateTimeInputs();
        autoCheckAvailability();
    }
    
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
        // Get equipment ID from multiple sources as backup
        const equipmentId = currentEquipmentId || document.getElementById('equipment_id').value;
        const fromDate = document.getElementById('requested_from').value;
        const untilDate = document.getElementById('requested_until').value;
        
        console.log('checkAvailability debug:', {
            currentEquipmentId: currentEquipmentId,
            formEquipmentId: document.getElementById('equipment_id').value,
            finalEquipmentId: equipmentId,
            fromDate: fromDate,
            untilDate: untilDate
        });
        
        if (!equipmentId || !fromDate || !untilDate) {
            console.error('Missing required fields:', { equipmentId, fromDate, untilDate });
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
            console.log('Button state restored');
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
        console.log('resetForm called');
        
        // Reset form values
        document.getElementById('borrowForm').reset();
        document.getElementById('equipment_id').value = '';
        
        // Reset state variables
        currentEquipmentId = null;
        currentEquipmentStatus = null;
        availabilityData = null;
        
        console.log('Form reset complete, currentEquipmentId:', currentEquipmentId);
        
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
    

    

    
    // Form submission validation
    document.getElementById('borrowForm').addEventListener('submit', function(e) {
        // Ensure hidden datetime inputs are populated before submission
        combineDateTimeInputs();
        
        const equipmentId = document.getElementById('equipment_id').value;
        const purpose = document.getElementById('purpose').value.trim();
        const fromDate = document.getElementById('requested_from').value;
        const untilDate = document.getElementById('requested_until').value;
        
        console.log('Form submission validation:', {
            equipmentId: equipmentId,
            purpose: purpose,
            fromDate: fromDate,
            untilDate: untilDate
        });
        
        if (!equipmentId) {
            e.preventDefault();
            console.error('Form submission blocked: Equipment ID is missing');
            showError('Equipment ID is missing. Please close the modal and try selecting the equipment again.');
            return false;
        }
        
        // Check individual date/time inputs as well
        const fromDateInput = document.getElementById('requested_from_date').value;
        const fromTimeInput = document.getElementById('requested_from_time').value;
        const untilDateInput = document.getElementById('requested_until_date').value;
        const untilTimeInput = document.getElementById('requested_until_time').value;

        if (!purpose || !fromDateInput || !fromTimeInput || !untilDateInput || !untilTimeInput || !fromDate || !untilDate) {
            e.preventDefault();
            console.error('Form submission blocked: Missing required fields');
            showError('Please fill in all required fields including dates and times.');
            return false;
        }

        // Validate that from time is not in the past for today's date
        const currentDateTime = new Date();
        const fromDateTime = new Date(fromDate);
        const currentDate = currentDateTime.toISOString().split('T')[0];
        
        // Allow current time (within the same minute) but not past times
        if (fromDateInput === currentDate) {
            const timeDifference = fromDateTime.getTime() - currentDateTime.getTime();
            const oneMinuteInMs = 60 * 1000;
            
            if (timeDifference < -oneMinuteInMs) { // Allow 1 minute tolerance for current time
                e.preventDefault();
                console.error('Form submission blocked: From time cannot be in the past');
                showError('The borrowing start time cannot be in the past. Please select the current time or later.');
                return false;
            }
        }
        
        if (new Date(fromDate) >= new Date(untilDate)) {
            e.preventDefault();
            console.error('Form submission blocked: Invalid date range');
            showError('End date must be after start date.');
            return false;
        }
        
        console.log('Form validation passed, submitting...');
    });


});
</script>
@endpush