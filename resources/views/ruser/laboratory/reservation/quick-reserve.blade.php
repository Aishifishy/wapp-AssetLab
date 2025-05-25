@extends('layouts.ruser')

@section('title', 'Quick Laboratory Reservation')
@section('header', 'Quick Laboratory Reservation')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Quick Reservation Form -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Quick Reservation</h2>
        </div>
        <div class="p-6">
            @if($recentReservations->isEmpty())
                <div class="text-center py-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No recent reservations found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        You need to make at least one reservation before you can use the quick reservation feature.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('ruser.laboratory.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Make Your First Reservation
                        </a>
                    </div>
                </div>
            @else
                <form action="{{ route('ruser.laboratory.reservations.quick-store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="template" class="block text-sm font-medium text-gray-700">Based on Previous Reservation</label>
                            <select id="template" name="template" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                                <option value="">Select a previous reservation</option>
                                @foreach($recentReservations as $prevReservation)
                                    <option value="{{ $prevReservation->id }}">
                                        {{ $prevReservation->laboratory->name }} - 
                                        {{ \Illuminate\Support\Str::limit($prevReservation->purpose, 30) }} - 
                                        {{ $prevReservation->reservation_date->format('M d, Y') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('template')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="reservation_date" class="block text-sm font-medium text-gray-700">Date <span class="text-red-500">*</span></label>
                            <input type="date" name="reservation_date" id="reservation_date" required
                                min="{{ date('Y-m-d') }}"
                                value="{{ old('reservation_date') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('reservation_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time <span class="text-red-500">*</span></label>
                            <input type="time" name="start_time" id="start_time" required
                                value="{{ old('start_time') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('start_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700">End Time <span class="text-red-500">*</span></label>
                            <input type="time" name="end_time" id="end_time" required
                                value="{{ old('end_time') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('end_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="col-span-1 md:col-span-2">
                            <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose <span class="text-red-500">*</span> <span class="text-xs text-gray-500">(Will be pre-filled from template)</span></label>
                            <textarea name="purpose" id="purpose" rows="3" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('ruser.laboratory.reservations.index') }}" class="px-4 py-2 bg-gray-200 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                            Submit Quick Reservation
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const templateSelect = document.getElementById('template');
        const purposeTextarea = document.getElementById('purpose');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const dateInput = document.getElementById('reservation_date');
        
        // Store template data
        const templates = {
            @foreach($recentReservations as $prevReservation)
            '{{ $prevReservation->id }}': {
                purpose: `{{ $prevReservation->purpose }}`,
                start_time: '{{ \Carbon\Carbon::parse($prevReservation->start_time)->format('H:i') }}',
                end_time: '{{ \Carbon\Carbon::parse($prevReservation->end_time)->format('H:i') }}',
                laboratory_id: '{{ $prevReservation->laboratory_id }}',
                laboratory_name: '{{ $prevReservation->laboratory->name }}'
            },
            @endforeach
        };
        
        // Update form fields when template is selected
        templateSelect.addEventListener('change', function() {
            const selectedId = this.value;
            if (selectedId && templates[selectedId]) {
                purposeTextarea.value = templates[selectedId].purpose;
                startTimeInput.value = templates[selectedId].start_time;
                endTimeInput.value = templates[selectedId].end_time;
                
                // Check for conflicts if all required fields have values
                if (dateInput.value) {
                    checkConflicts();
                }
            }
        });
        
        // Function to check for conflicts
        function checkConflicts() {
            const selectedId = templateSelect.value;
            if (!selectedId || !templates[selectedId] || !dateInput.value || !startTimeInput.value || !endTimeInput.value) {
                return; // Not all fields filled
            }
            
            const laboratory_id = templates[selectedId].laboratory_id;
            const laboratory_name = templates[selectedId].laboratory_name;
            
            // Create conflict message area if it doesn't exist
            let conflictArea = document.getElementById('conflict-message');
            if (!conflictArea) {
                conflictArea = document.createElement('div');
                conflictArea.id = 'conflict-message';
                conflictArea.className = 'mt-4 text-sm';
                document.querySelector('.grid').appendChild(conflictArea);
            }
            
            // Show loading message
            conflictArea.innerHTML = '<p class="text-gray-500">Checking for conflicts...</p>';
            
            // Send request to check for conflicts
            fetch('/api/reservation/check-conflict', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    laboratory_id: laboratory_id,
                    date: dateInput.value,
                    start_time: startTimeInput.value,
                    end_time: endTimeInput.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.has_conflict) {
                    conflictArea.innerHTML = `
                        <div class="p-3 bg-red-50 border border-red-200 rounded-md">
                            <p class="font-medium text-red-800">${data.message}</p>
                            <p class="text-red-600">Laboratory ${laboratory_name} is not available at the selected time.</p>
                        </div>
                    `;
                } else {
                    conflictArea.innerHTML = `
                        <div class="p-3 bg-green-50 border border-green-200 rounded-md">
                            <p class="font-medium text-green-800">No conflicts found!</p>
                            <p class="text-green-600">Laboratory ${laboratory_name} is available at the selected time.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error checking conflicts:', error);
                conflictArea.innerHTML = `
                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                        <p class="font-medium text-yellow-800">Could not check for conflicts</p>
                        <p class="text-yellow-600">Please proceed with caution.</p>
                    </div>
                `;
            });
        }
        
        // Add event listeners to check for conflicts when inputs change
        dateInput.addEventListener('change', checkConflicts);
        startTimeInput.addEventListener('change', checkConflicts);
        endTimeInput.addEventListener('change', checkConflicts);
        
        // Also update the reservation date field's min value to today
        dateInput.min = new Date().toISOString().split('T')[0];
    });
</script>
@endpush
@endsection
