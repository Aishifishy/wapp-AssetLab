@extends('layouts.ruser')

@section('title', 'Laboratory Schedule')
@section('header', 'Laboratory Schedule')

@section('content')
<div class="space-y-6">
    <!-- Laboratory Details -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $laboratory->name }}</h2>
                    <p class="text-gray-600">{{ $laboratory->building }}, Room {{ $laboratory->room_number }}</p>
                </div>
                <div>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                        {{ $laboratory->status === 'available' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $laboratory->status === 'in_use' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $laboratory->status === 'under_maintenance' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $laboratory->status === 'reserved' ? 'bg-blue-100 text-blue-800' : '' }}">
                        {{ ucfirst(str_replace('_', ' ', $laboratory->status)) }}
                    </span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-600">Capacity: <span class="font-medium">{{ $laboratory->capacity }} seats</span></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Computers: <span class="font-medium">{{ $laboratory->number_of_computers }} units</span></p>
                </div>
            </div>

            @if(!$currentTerm)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                    <p>There is no active academic term. Laboratory reservations are not available at this time.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Weekly Schedule -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Weekly Schedule</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full border">
                    <thead>
                        <tr>
                            <th class="w-24 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $timeSlots = [];
                            $startTime = strtotime('07:00');
                            $endTime = strtotime('21:00');
                            while($startTime <= $endTime) {
                                $timeSlots[] = date('H:i', $startTime);
                                $startTime = strtotime('+30 minutes', $startTime);
                            }
                        @endphp

                        @foreach($timeSlots as $index => $time)
                            @if($index % 2 == 0)
                                <tr>
                                    <td class="text-center align-middle border-r px-3 py-2" rowspan="2">
                                        {{ date('H:i', strtotime($time)) }} - {{ date('H:i', strtotime('+1 hour', strtotime($time))) }}
                                    </td>
                                    @for($day = 0; $day <= 6; $day++)
                                        <td class="border px-3 py-2 text-xs">
                                            @if($currentTerm)
                                                @foreach($schedules as $schedule)
                                                    @if($schedule->day_of_week == $day && 
                                                      (strtotime($schedule->start_time) <= strtotime($time) && 
                                                       strtotime($schedule->end_time) > strtotime($time)))
                                                        <div class="p-1 mb-1 bg-{{ $schedule->type == 'class' ? 'blue' : 'green' }}-100 rounded text-xs">
                                                            <strong>{{ $schedule->subject_code }}</strong><br>
                                                            {{ $schedule->instructor_name }}<br>
                                                            {{ $schedule->section }}
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @else
                                <tr>
                                    @for($day = 0; $day <= 6; $day++)
                                        <td class="border px-3 py-2 text-xs">
                                            @if($currentTerm)
                                                @foreach($schedules as $schedule)
                                                    @if($schedule->day_of_week == $day && 
                                                      (strtotime($schedule->start_time) <= strtotime($time) && 
                                                       strtotime($schedule->end_time) > strtotime($time)))
                                                        <div class="p-1 mb-1 bg-{{ $schedule->type == 'class' ? 'blue' : 'green' }}-100 rounded text-xs">
                                                            <strong>{{ $schedule->subject_code }}</strong><br>
                                                            {{ $schedule->instructor_name }}<br>
                                                            {{ $schedule->section }}
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Reservation Form -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Make Reservation</h2>
            
            @if(!$currentTerm)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                    <p>Reservations are not available without an active academic term.</p>
                </div>
            @else
                <form action="{{ route('ruser.laboratory.reserve', $laboratory) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose</label>
                            <textarea name="purpose" id="purpose" rows="3" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="date" id="date" required
                                min="{{ date('Y-m-d') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                value="{{ old('date') }}">
                            @error('date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="day_of_week" class="block text-sm font-medium text-gray-700">Day of Week</label>
                            <select name="day_of_week" id="day_of_week" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select day</option>
                                <option value="0" {{ old('day_of_week') == '0' ? 'selected' : '' }}>Sunday</option>
                                <option value="1" {{ old('day_of_week') == '1' ? 'selected' : '' }}>Monday</option>
                                <option value="2" {{ old('day_of_week') == '2' ? 'selected' : '' }}>Tuesday</option>
                                <option value="3" {{ old('day_of_week') == '3' ? 'selected' : '' }}>Wednesday</option>
                                <option value="4" {{ old('day_of_week') == '4' ? 'selected' : '' }}>Thursday</option>
                                <option value="5" {{ old('day_of_week') == '5' ? 'selected' : '' }}>Friday</option>
                                <option value="6" {{ old('day_of_week') == '6' ? 'selected' : '' }}>Saturday</option>
                            </select>
                            @error('day_of_week')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                                <input type="time" name="start_time" id="start_time" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                    value="{{ old('start_time') }}">
                                @error('start_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                                <input type="time" name="end_time" id="end_time" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                    value="{{ old('end_time') }}">
                                @error('end_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('ruser.laboratory.index') }}" class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </a>
                        <button type="submit" class="ml-3 bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Submit Reservation
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Date and day sync
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('date');
        const daySelect = document.getElementById('day_of_week');
        
        if (dateInput && daySelect) {
            dateInput.addEventListener('change', function() {
                const date = new Date(this.value);
                const dayOfWeek = date.getDay();
                daySelect.value = dayOfWeek;
            });
        }
    });
</script>
@endpush
@endsection
