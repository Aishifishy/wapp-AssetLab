@extends('layouts.ruser')

@section('title', 'Create Laboratory Reservation')
@section('header', 'Create Laboratory Reservation')

@section('content')
<div class="space-y-6">
    <x-flash-messages />

    <!-- Laboratory Information -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                Laboratory Information
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Laboratory</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $laboratory->name }}</p>
                    <p class="text-sm text-gray-600">{{ $laboratory->building }}, Room {{ $laboratory->room_number }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Capacity</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $laboratory->capacity }} seats</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Computers</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $laboratory->number_of_computers }} units</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Status</h3>
                    <p class="mt-1">
                        <x-status-badge :status="$laboratory->status" type="laboratory" />
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation Form -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h3z" />
                </svg>
                Reservation Form
            </h2>
        </div>
        <div class="p-6">
            @if(!$currentTerm)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <p>Reservations are not available without an active academic term.</p>
                    </div>
                </div>
            @else
                <form id="reservation-form" action="{{ route('ruser.laboratory.reservations.store', $laboratory) }}" method="POST">
                    @csrf
                    <input type="hidden" name="laboratory_id" value="{{ $laboratory->id }}">
                    
                    <!-- Conflict Alert -->
                    <div id="conflict-alert" class="hidden mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <div>
                                <p class="font-medium">Time Conflict Detected</p>
                                <p class="text-sm">The following reservations conflict with your selected time:</p>
                                <div id="conflict-list" class="mt-2 space-y-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="purpose" class="block text-sm font-medium text-gray-700">
                                Purpose <span class="text-red-500">*</span>
                            </label>
                            <textarea name="purpose" id="purpose" rows="3" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Describe the purpose of this reservation">{{ old('purpose') }}</textarea>
                            <x-form-error field="purpose" />
                        </div>
                        
                        <div>
                            <label for="reservation_date" class="block text-sm font-medium text-gray-700">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="reservation_date" id="reservation_date" required
                                min="{{ date('Y-m-d') }}"
                                value="{{ old('reservation_date') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="reservation_date" />
                        </div>
                        
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">
                                Start Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="start_time" id="start_time" required
                                value="{{ old('start_time') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="start_time" />
                        </div>
                        
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700">
                                End Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="end_time" id="end_time" required
                                value="{{ old('end_time') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="end_time" />
                        </div>
                        
                        <div>
                            <label for="num_students" class="block text-sm font-medium text-gray-700">
                                Number of Students <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="num_students" id="num_students" required
                                min="1" max="{{ $laboratory->capacity }}"
                                value="{{ old('num_students') }}"
                                placeholder="Max: {{ $laboratory->capacity }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="num_students" />
                        </div>
                        
                        <div>
                            <label for="course_code" class="block text-sm font-medium text-gray-700">Course Code</label>
                            <input type="text" name="course_code" id="course_code"
                                value="{{ old('course_code') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="course_code" />
                        </div>
                        
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                            <input type="text" name="subject" id="subject"
                                value="{{ old('subject') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="subject" />
                        </div>
                        
                        <div>
                            <label for="section" class="block text-sm font-medium text-gray-700">Section</label>
                            <input type="text" name="section" id="section"
                                value="{{ old('section') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="section" />
                        </div>
                        
                        <div class="col-span-1 md:col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_recurring" id="is_recurring" value="1"
                                    {{ old('is_recurring') ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_recurring" class="ml-2 block text-sm text-gray-700">
                                    Make this a recurring reservation
                                </label>
                            </div>
                        </div>
                        
                        <div id="recurring-details" class="hidden md:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="recurring_pattern" class="block text-sm font-medium text-gray-700">
                                    Recurrence Pattern
                                </label>
                                <select name="recurring_pattern" id="recurring_pattern"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="weekly" {{ old('recurring_pattern') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ old('recurring_pattern') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                                <x-form-error field="recurring_pattern" />
                            </div>
                            
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">
                                    End Date
                                </label>
                                <input type="date" name="end_date" id="end_date"
                                    min="{{ date('Y-m-d', strtotime('+1 week')) }}"
                                    value="{{ old('end_date') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <x-form-error field="end_date" />
                            </div>
                            </div>
                        </div>
                    </div>

                    @if($schedules->count() > 0)
                        <div class="mt-8">
                            <h3 class="text-base font-medium text-gray-800 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Regular Schedule Information
                            </h3>
                            <p class="text-sm text-gray-600 mb-4">
                                This laboratory has regular classes scheduled. Please check for conflicts before submitting.
                            </p>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full border border-gray-200 rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Day</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Time</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Subject</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Instructor</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($schedules as $schedule)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 text-sm text-gray-900 border-b">{{ $schedule->day }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900 border-b">
                                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-900 border-b">{{ $schedule->subject }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900 border-b">{{ $schedule->instructor }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="mt-8 flex justify-end space-x-4">
                        <a href="{{ route('ruser.laboratory.reservations.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Submit Reservation
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
