@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Add Schedule</h1>
        <a href="{{ route('admin.comlab.calendar') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            <i class="fas fa-arrow-left mr-2"></i> Back to Calendar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <i class="fas fa-calendar-plus text-gray-500 mr-2"></i>
                <h3 class="text-lg font-medium text-gray-900">New Schedule for {{ $laboratory->name }} ({{ $laboratory->building }} - Room {{ $laboratory->room_number }})</h3>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.comlab.schedule.store', $laboratory) }}" method="POST">
                @csrf
                
                <!-- Hidden field for current academic term -->
                <input type="hidden" name="academic_term_id" value="{{ $currentTerm->id }}">
                
                <x-form-error field="time_conflict" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-6">
                        <div>
                            <label for="subject_code" class="block text-sm font-medium text-gray-700">Subject Code</label>
                            <input type="text" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                   id="subject_code" 
                                   name="subject_code" 
                                   value="{{ old('subject_code') }}" 
                                   maxlength="20"
                                   placeholder="e.g., CS101">
                            <x-form-error field="subject_code" />
                        </div>

                        <div>
                            <label for="subject_name" class="block text-sm font-medium text-gray-700">Subject Name</label>
                            <input type="text" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                   id="subject_name" 
                                   name="subject_name" 
                                   value="{{ old('subject_name') }}" 
                                   required 
                                   maxlength="100"
                                   placeholder="e.g., Introduction to Programming">
                            <x-form-error field="subject_name" />
                        </div>

                        <div>
                            <label for="instructor_name" class="block text-sm font-medium text-gray-700">Instructor Name</label>
                            <input type="text" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                   id="instructor_name" 
                                   name="instructor_name" 
                                   value="{{ old('instructor_name') }}" 
                                   required 
                                   maxlength="100"
                                   placeholder="e.g., Dr. John Smith">
                            <x-form-error field="instructor_name" />
                        </div>

                        <div>
                            <label for="section" class="block text-sm font-medium text-gray-700">Section</label>
                            <input type="text" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                   id="section" 
                                   name="section" 
                                   value="{{ old('section') }}" 
                                   required 
                                   maxlength="20"
                                   placeholder="e.g., BSCS-1A">
                            <x-form-error field="section" />
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="day_of_week" class="block text-sm font-medium text-gray-700">Day of Week</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                    id="day_of_week" 
                                    name="day_of_week" 
                                    required>
                                <option value="">Select Day</option>
                                <option value="0" {{ old('day_of_week') === '0' ? 'selected' : '' }}>Sunday</option>
                                <option value="1" {{ old('day_of_week') === '1' ? 'selected' : '' }}>Monday</option>
                                <option value="2" {{ old('day_of_week') === '2' ? 'selected' : '' }}>Tuesday</option>
                                <option value="3" {{ old('day_of_week') === '3' ? 'selected' : '' }}>Wednesday</option>
                                <option value="4" {{ old('day_of_week') === '4' ? 'selected' : '' }}>Thursday</option>
                                <option value="5" {{ old('day_of_week') === '5' ? 'selected' : '' }}>Friday</option>
                                <option value="6" {{ old('day_of_week') === '6' ? 'selected' : '' }}>Saturday</option>
                            </select>
                            <x-form-error field="day_of_week" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                                <input type="time" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                       id="start_time" 
                                       name="start_time" 
                                       value="{{ old('start_time') }}" 
                                       required>
                                <x-form-error field="start_time" />
                            </div>

                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                                <input type="time" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                       id="end_time" 
                                       name="end_time" 
                                       value="{{ old('end_time') }}" 
                                       required>
                                <x-form-error field="end_time" />
                            </div>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Schedule Type</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                    id="type" 
                                    name="type" 
                                    required>
                                <option value="regular" {{ old('type') === 'regular' ? 'selected' : '' }}>Regular</option>
                                <option value="special" {{ old('type') === 'special' ? 'selected' : '' }}>Special</option>
                            </select>
                            <x-form-error field="type" />
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3"
                                      placeholder="Optional notes or additional information about this schedule">{{ old('notes') }}</textarea>
                            <x-form-error field="notes" />
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row sm:justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                    <button type="button" 
                            onclick="window.history.back()"
                            class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i> Create Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection