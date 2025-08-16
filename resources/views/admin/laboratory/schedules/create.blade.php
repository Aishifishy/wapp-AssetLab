@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Add Class Schedule</h1>
        <a href="{{ route('admin.comlab.calendar') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            <i class="fas fa-arrow-left mr-2"></i> Back to Calendar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <i class="fas fa-clock text-gray-500 mr-2"></i>
                <h3 class="text-lg font-medium text-gray-900">Schedule Details</h3>
            </div>
        </div>
        <div class="p-6">            <form action="{{ route('admin.comlab.schedule.store-generic') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-6">
                        <div>
                            <label for="laboratory_id" class="block text-sm font-medium text-gray-700">Computer Laboratory</label>
                            <select name="laboratory_id" 
                                    id="laboratory_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    required>
                                <option value="">Select Laboratory</option>
                                @foreach($laboratories as $lab)
                                    <option value="{{ $lab->id }}" 
                                            {{ old('laboratory_id') == $lab->id ? 'selected' : '' }}
                                            data-capacity="{{ $lab->capacity }}">
                                        {{ $lab->name }} ({{ $lab->building }} - Room {{ $lab->room_number }})
                                    </option>
                                @endforeach                            </select>
                            <x-form-error field="laboratory_id" />
                            <div id="laboratory-info" class="mt-2 text-sm text-gray-600 hidden">
                                <p>Capacity: <span id="lab-capacity"></span> computers</p>
                            </div>
                        </div>

                        <div>
                            <label for="academic_term_id" class="block text-sm font-medium text-gray-700">Academic Term</label>
                            <select name="academic_term_id" 
                                    id="academic_term_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    required>
                                <option value="">Select Term</option>
                                @foreach($academicTerms as $term)
                                    <option value="{{ $term->id }}" {{ old('academic_term_id') == $term->id ? 'selected' : '' }}>
                                        {{ $term->academicYear->name }} - {{ $term->name }}
                                    </option>
                                @endforeach                            </select>
                            <x-form-error field="academic_term_id" />
                        </div>

                        <div>
                            <label for="subject_code" class="block text-sm font-medium text-gray-700">Subject Code</label>
                            <input type="text" 
                                   id="subject_code" 
                                   name="subject_code" 
                                   value="{{ old('subject_code') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"                                   placeholder="e.g., CS101">
                            <x-form-error field="subject_code" />
                        </div>

                        <div>
                            <label for="subject_name" class="block text-sm font-medium text-gray-700">Subject Name</label>
                            <input type="text" 
                                   id="subject_name" 
                                   name="subject_name" 
                                   value="{{ old('subject_name') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   required                                   placeholder="e.g., Introduction to Computing">
                            <x-form-error field="subject_name" />
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="instructor_name" class="block text-sm font-medium text-gray-700">Instructor Name</label>
                            <input type="text" 
                                   id="instructor_name" 
                                   name="instructor_name" 
                                   value="{{ old('instructor_name') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   required                                   placeholder="e.g., John Doe">
                            <x-form-error field="instructor_name" />
                        </div>

                        <div>
                            <label for="section" class="block text-sm font-medium text-gray-700">Section</label>
                            <input type="text" 
                                   id="section" 
                                   name="section" 
                                   value="{{ old('section') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   required                                   placeholder="e.g., BSCS-1A">
                            <x-form-error field="section" />
                        </div>

                        <div>
                            <label for="day_of_week" class="block text-sm font-medium text-gray-700">Day of Week</label>
                            <select name="day_of_week" 
                                    id="day_of_week" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    required>
                                <option value="">Select Day</option>
                                <option value="1" {{ old('day_of_week') == 1 ? 'selected' : '' }}>Monday</option>
                                <option value="2" {{ old('day_of_week') == 2 ? 'selected' : '' }}>Tuesday</option>
                                <option value="3" {{ old('day_of_week') == 3 ? 'selected' : '' }}>Wednesday</option>
                                <option value="4" {{ old('day_of_week') == 4 ? 'selected' : '' }}>Thursday</option>
                                <option value="5" {{ old('day_of_week') == 5 ? 'selected' : '' }}>Friday</option>
                                <option value="6" {{ old('day_of_week') == 6 ? 'selected' : '' }}>Saturday</option>                            </select>
                            <x-form-error field="day_of_week" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                                <input type="time" 
                                       id="start_time" 
                                       name="start_time" 
                                       value="{{ old('start_time') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"                                       required>
                                <x-form-error field="start_time" />
                            </div>

                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                                <input type="time" 
                                       id="end_time" 
                                       name="end_time" 
                                       value="{{ old('end_time') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"                                       required>
                                <x-form-error field="end_time" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"                              placeholder="Any additional information about this schedule">{{ old('notes') }}</textarea>
                    <x-form-error field="notes" />
                </div>

                <div class="mt-6 flex justify-end space-x-3">                    <button type="button" 
                            data-action="go-back"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i> Create Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection