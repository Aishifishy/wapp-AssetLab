@extends('layouts.admin')

@section('title', 'Edit Academic Year')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Edit Academic Year</h1>
        <a href="{{ route('admin.academic.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Calendar
        </a>
    </div>

    <!-- Flash Messages -->
    <x-flash-messages />

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt text-gray-500 mr-2"></i>
                <h3 class="text-lg font-medium text-gray-900">Academic Year Details</h3>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.academic.update', $academicYear) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="md:col-span-1">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Academic Year Name</label>
                        <input type="text" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                            id="name" name="name" value="{{ old('name', $academicYear->name) }}" 
                            placeholder="e.g., 2024-2025" required>
                        <x-form-error field="name" />
                        <p class="mt-1 text-sm text-gray-500">Format: YYYY-YYYY (e.g., 2024-2025)</p>
                    </div>

                    <div class="md:col-span-1">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('start_date') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                            id="start_date" name="start_date" 
                            value="{{ old('start_date', $academicYear->start_date->format('Y-m-d')) }}" 
                            required>
                        <x-form-error field="start_date" />
                    </div>

                    <div class="md:col-span-1">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('end_date') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                            id="end_date" name="end_date" 
                            value="{{ old('end_date', $academicYear->end_date->format('Y-m-d')) }}" 
                            required>
                        <x-form-error field="end_date" />
                    </div>
                </div>

                <!-- Academic Year Info -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Note:</strong> Changing the academic year dates will automatically adjust the associated term dates proportionally to maintain the 3-term structure.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Current Terms Display -->
                @if($academicYear->terms->count() > 0)
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Current Terms</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($academicYear->terms->sortBy('term_number') as $term)
                        <div class="bg-gray-50 rounded-lg p-4 border">
                            <div class="flex items-center justify-between mb-2">
                                <h5 class="font-medium text-gray-800">{{ $term->name }}</h5>
                                @if($term->is_current)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        Active
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600">
                                {{ $term->start_date->format('M d, Y') }} - {{ $term->end_date->format('M d, Y') }}
                            </p>
                            <a href="{{ route('admin.academic.terms.edit', ['academicYear' => $academicYear->id, 'term' => $term->id]) }}" 
                               class="inline-flex items-center mt-2 text-xs text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit mr-1"></i> Edit Term
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.academic.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-200 disabled:opacity-25 transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition">
                        <i class="fas fa-save mr-2"></i> Update Academic Year
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize date validation for edit form
    document.addEventListener('DOMContentLoaded', function() {
        if (window.dateValidationManager) {
            // Set up basic date validation
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            
            if (startDateInput && endDateInput) {
                window.dateValidationManager.initEndDateValidation(startDateInput, endDateInput);
                
                // Add start date validation
                startDateInput.addEventListener('change', function() {
                    const startDate = new Date(startDateInput.value);
                    const endDate = new Date(endDateInput.value);
                    
                    if (endDate && startDate >= endDate) {
                        window.dateValidationManager.showValidationError('Start date must be before end date');
                        startDateInput.value = '';
                    }
                });
            }
        }
    });
</script>
@endpush
@endsection
