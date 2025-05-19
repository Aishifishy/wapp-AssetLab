@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Add New Academic Term</h1>
        <a href="{{ route('admin.academic.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt text-gray-500 mr-2"></i>
                <h3 class="text-lg font-medium text-gray-900">Term Details</h3>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.academic.terms.store', ['academicYear' => $academicYear->id]) }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-6">
                        <div>
                            <p class="block text-sm font-medium text-gray-700">Academic Year</p>
                            <p class="mt-1 text-sm text-gray-600">{{ $academicYear->name }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $academicYear->start_date->format('M d, Y') }} - {{ $academicYear->end_date->format('M d, Y') }}</p>
                        </div>

                        <div>
                            <label for="term_number" class="block text-sm font-medium text-gray-700">Term</label>
                            <select name="term_number" 
                                    id="term_number" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('term_number') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror"
                                    required>
                                <option value="">Select Term</option>
                                <option value="1" {{ old('term_number') == 1 ? 'selected' : '' }}>First Term</option>
                                <option value="2" {{ old('term_number') == 2 ? 'selected' : '' }}>Second Term</option>
                                <option value="3" {{ old('term_number') == 3 ? 'selected' : '' }}>Third Term</option>
                            </select>
                            @error('term_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <input type="hidden" name="name" id="term_name">
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('start_date') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ old('start_date') }}" 
                                   required>
                            @error('start_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('end_date') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ old('end_date') }}" 
                                   required>
                            @error('end_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                                       id="is_current" 
                                       name="is_current" 
                                       value="1"
                                       {{ old('is_current') ? 'checked' : '' }}>
                                <label for="is_current" class="ml-2 block text-sm text-gray-700">
                                    Set as Current Term
                                </label>
                            </div>
                            @error('is_current')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i> Create Term
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Set academic year date constraints
        const startDate = '{{ $academicYear->start_date->format('Y-m-d') }}';
        const endDate = '{{ $academicYear->end_date->format('Y-m-d') }}';
        
        $('#start_date').attr('min', startDate);
        $('#start_date').attr('max', endDate);
        $('#end_date').attr('min', startDate);
        $('#end_date').attr('max', endDate);

        // Set term name based on selected term number
        $('#term_number').change(function() {
            const termNames = {
                '1': 'First Term',
                '2': 'Second Term',
                '3': 'Third Term'
            };
            $('#term_name').val(termNames[$(this).val()] || '');
        });
        // Set initial term name if term number is pre-selected
        $('#term_number').trigger('change');

        // Validate end date is after start date
        $('#end_date').change(function() {
            const startDate = new Date($('#start_date').val());
            const endDate = new Date($(this).val());
            
            if (endDate <= startDate) {
                const errorDiv = $('<div>')
                    .addClass('fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50')
                    .html(`
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>End date must be after start date</span>
                            <button type="button" class="ml-4 text-red-700 hover:text-red-900" onclick="this.parentElement.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `);
                $('body').append(errorDiv);
                setTimeout(() => errorDiv.remove(), 5000);
                $(this).val('');
            }
        });
    });
</script>
@endpush
@endsection 