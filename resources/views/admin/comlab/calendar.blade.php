@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Laboratory Schedule Calendar</h1>
        <div>
            <span class="text-gray-500">Current Term: {{ $currentTerm->name }}</span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
            <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <span class="sr-only">Close</span>
                <svg class="h-4 w-4 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
            <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <span class="sr-only">Close</span>
                <svg class="h-4 w-4 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    @endif

    @foreach($laboratories as $laboratory)
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <i class="fas fa-desktop text-gray-500 mr-2"></i>
                        <span class="text-lg font-medium text-gray-900">
                            {{ $laboratory->name }} ({{ $laboratory->building }} - {{ $laboratory->room_number }})
                        </span>
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $laboratory->status === 'available' ? 'bg-green-100 text-green-800' : 
                               ($laboratory->status === 'in_use' ? 'bg-blue-100 text-blue-800' : 
                               ($laboratory->status === 'under_maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ str_replace('_', ' ', ucfirst($laboratory->status)) }}
                        </span>
                    </div>
                    <a href="{{ route('admin.comlab.schedule.create', $laboratory) }}" 
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Add Schedule
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
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
                                            {{ date('h:i A', strtotime($time)) }}
                                        </td>
                                        @for($day = 0; $day <= 6; $day++)
                                            <td class="relative border-r h-8 px-3 py-2">
                                                @if(isset($schedules[$laboratory->id]))
                                                    @foreach($schedules[$laboratory->id] as $schedule)
                                                        @if($schedule->day_of_week === $day && strtotime($schedule->start_time) === strtotime($time))
                                                            <div class="absolute inset-0 {{ $schedule->type === 'regular' ? 'bg-blue-50' : 'bg-yellow-50' }} p-2">
                                                                <div class="flex justify-between items-start">
                                                                    <div class="text-sm">
                                                                        <div class="font-medium text-gray-900">{{ $schedule->subject_name }}</div>
                                                                        <div class="text-gray-500">{{ $schedule->instructor_name }}</div>
                                                                        <div class="text-gray-500">{{ $schedule->section }}</div>
                                                                    </div>
                                                                    <div class="flex space-x-1">
                                                                        <a href="{{ route('admin.comlab.schedule.edit', [$laboratory, $schedule]) }}" 
                                                                           class="text-blue-600 hover:text-blue-800">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                        <button type="button" 
                                                                                class="text-red-600 hover:text-red-800"
                                                                                onclick="document.getElementById('deleteForm{{ $schedule->id }}').submit();">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                        <form id="deleteForm{{ $schedule->id }}" 
                                                                              action="{{ route('admin.comlab.schedule.destroy', [$laboratory, $schedule]) }}" 
                                                                              method="POST" 
                                                                              class="hidden">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                        </form>
                                                                    </div>
                                                                </div>
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
                                            <td class="relative border-r h-8 px-3 py-2">
                                                @if(isset($schedules[$laboratory->id]))
                                                    @foreach($schedules[$laboratory->id] as $schedule)
                                                        @if($schedule->day_of_week === $day && 
                                                            strtotime($schedule->start_time) <= strtotime($time) && 
                                                            strtotime($schedule->end_time) > strtotime($time))
                                                            <div class="absolute inset-0 {{ $schedule->type === 'regular' ? 'bg-blue-50' : 'bg-yellow-50' }} opacity-50"></div>
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
    @endforeach
</div>

@push('styles')
<style>
    td {
        position: relative;
        min-width: 120px;
    }
    td:first-child {
        min-width: 80px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize any necessary JavaScript functionality
    });
</script>
@endpush
@endsection 