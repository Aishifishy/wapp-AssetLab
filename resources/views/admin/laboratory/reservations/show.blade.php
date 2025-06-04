@extends('layouts.admin')

@section('title', 'Reservation Details')
@section('header', 'Laboratory Reservation Details')

@section('content')
<div class="space-y-6">
    <x-flash-messages />

    <!-- Action Buttons -->
    <div class="flex justify-end">
        <a href="{{ route('admin.laboratory.reservations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 mr-2">
            Back to List
        </a>
        
        @if($reservation->status == 'pending')
            <form action="{{ route('admin.laboratory.reservations.approve', $reservation) }}" method="POST" class="inline mr-2">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    Approve
                </button>
            </form>
            
            <button onclick="openRejectModal()" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                Reject
            </button>
        @endif
          @if(in_array($reservation->status, ['rejected', 'cancelled']))
            <button type="button" 
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                    data-modal-target="deleteReservationModal">
                Delete
            </button>
        @endif
    </div>

    <!-- Reservation Details Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-900">Reservation #{{ $reservation->id }}</h2>
            <x-status-badge :status="$reservation->status" type="reservation" class="px-3 py-1 text-sm" />
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Requested By</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->user->name }}</p>
                    <p class="text-sm text-gray-600">{{ $reservation->user->email }} ({{ ucfirst($reservation->user->role) }})</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Submitted On</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->created_at->format('F d, Y - H:i') }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Laboratory</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->laboratory->name }}</p>
                    <p class="text-sm text-gray-600">{{ $reservation->laboratory->building }}, Room {{ $reservation->laboratory->room_number }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Laboratory Capacity</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->laboratory->capacity }} seats / {{ $reservation->laboratory->number_of_computers }} computers</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Date</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->reservation_date->format('F d, Y') }} ({{ $reservation->reservation_date->format('l') }})</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Time</h3>
                    <p class="mt-1 text-base text-gray-900">
                        {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                        <span class="text-sm text-gray-600">({{ $reservation->duration }})</span>
                    </p>
                </div>

                @if($reservation->is_recurring)
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Recurrence Pattern</h3>
                    <p class="mt-1 text-base text-gray-900">
                        {{ ucfirst($reservation->recurrence_pattern) }} until {{ $reservation->recurrence_end_date->format('F d, Y') }}
                    </p>
                </div>
                @endif

                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Purpose</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->purpose }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Number of Students</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->num_students }}</p>
                </div>

                @if($reservation->course_code || $reservation->subject || $reservation->section)
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Course Information</h3>
                    <p class="mt-1 text-base text-gray-900">
                        @if($reservation->course_code)
                            {{ $reservation->course_code }}:
                        @endif
                        {{ $reservation->subject ?? 'N/A' }}
                        @if($reservation->section)
                            ({{ $reservation->section }})
                        @endif
                    </p>
                </div>
                @endif

                @if($reservation->status === 'rejected' && $reservation->rejection_reason)
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-sm font-medium text-red-500">Rejection Reason</h3>
                    <p class="mt-1 text-base text-red-600">{{ $reservation->rejection_reason }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Potential Conflicts Section -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Potential Conflicts</h2>
        </div>
        <div class="p-6">
            <h3 class="text-base font-medium text-gray-800 mb-3">Regular Class Schedule Conflicts</h3>
            
            @php
                $dayOfWeek = \Carbon\Carbon::parse($reservation->reservation_date)->dayOfWeek;
                $conflictingSchedules = \App\Models\LaboratorySchedule::where('laboratory_id', $reservation->laboratory_id)
                    ->where('day_of_week', $dayOfWeek)
                    ->where(function($query) use ($reservation) {
                        $query->where(function($q) use ($reservation) {
                            $q->where('start_time', '<=', $reservation->start_time)
                              ->where('end_time', '>', $reservation->start_time);
                        })->orWhere(function($q) use ($reservation) {
                            $q->where('start_time', '<', $reservation->end_time)
                              ->where('end_time', '>=', $reservation->end_time);
                        })->orWhere(function($q) use ($reservation) {
                            $q->where('start_time', '>=', $reservation->start_time)
                              ->where('end_time', '<=', $reservation->end_time);
                        });
                    })
                    ->with('academicTerm')
                    ->get();
            @endphp
            
            @if($conflictingSchedules->count() > 0)
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    <p class="font-bold">Warning: Schedule Conflicts Detected</p>
                    <p>This reservation conflicts with {{ $conflictingSchedules->count() }} regular class schedule(s)!</p>
                </div>
                
                <div class="overflow-x-auto mt-3">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($conflictingSchedules as $schedule)
                            <tr class="bg-red-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    @php
                                        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    @endphp
                                    {{ $days[$schedule->day_of_week] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $schedule->subject_code }}</div>
                                    <div class="text-sm text-gray-500">{{ $schedule->subject_name }} ({{ $schedule->section }})</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $schedule->instructor_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $schedule->academicTerm->name }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-green-600">No conflicts with regular class schedules.</p>
            @endif
            
            <h3 class="text-base font-medium text-gray-800 mt-6 mb-3">Other Reservation Conflicts</h3>
            
            @php
                $conflictingReservations = \App\Models\LaboratoryReservation::where('laboratory_id', $reservation->laboratory_id)
                    ->where('reservation_date', $reservation->reservation_date)
                    ->where('status', \App\Models\LaboratoryReservation::STATUS_APPROVED)
                    ->where('id', '!=', $reservation->id)
                    ->where(function($query) use ($reservation) {
                        $query->where(function($q) use ($reservation) {
                            $q->where('start_time', '<=', $reservation->start_time)
                              ->where('end_time', '>', $reservation->start_time);
                        })->orWhere(function($q) use ($reservation) {
                            $q->where('start_time', '<', $reservation->end_time)
                              ->where('end_time', '>=', $reservation->end_time);
                        })->orWhere(function($q) use ($reservation) {
                            $q->where('start_time', '>=', $reservation->start_time)
                              ->where('end_time', '<=', $reservation->end_time);
                        });
                    })
                    ->with('user')
                    ->get();
            @endphp
            
            @if($conflictingReservations->count() > 0)
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    <p class="font-bold">Warning: Reservation Conflicts Detected</p>
                    <p>This reservation conflicts with {{ $conflictingReservations->count() }} existing approved reservation(s)!</p>
                </div>
                
                <div class="overflow-x-auto mt-3">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($conflictingReservations as $conflictReservation)
                            <tr class="bg-red-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $conflictReservation->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($conflictReservation->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($conflictReservation->end_time)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $conflictReservation->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $conflictReservation->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Illuminate\Support\Str::limit($conflictReservation->purpose, 30) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.laboratory.reservations.show', $conflictReservation) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-green-600">No conflicts with other approved reservations.</p>
            @endif
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<x-rejection-modal :action-url="route('admin.laboratory.reservations.reject', $reservation)" />

<!-- Delete Confirmation Modal -->
@if(in_array($reservation->status, ['rejected', 'cancelled']))
<x-delete-confirmation-modal 
    modal-id="deleteReservationModal"
    title="Delete Reservation"
    message="Are you sure you want to delete this reservation? This action cannot be undone."
    delete-route="{{ route('admin.laboratory.reservations.destroy', $reservation) }}" />
@endif

@push('scripts')
<!-- Reservation rejection functionality is now handled by reservation-manager.js module -->
@endpush
@endsection
