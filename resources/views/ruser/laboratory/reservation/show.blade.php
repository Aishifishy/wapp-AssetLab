@extends('layouts.ruser')

@section('title', 'Reservation Details')
@section('header', 'Laboratory Reservation Details')

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

    <!-- Reservation Details Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-900">Reservation Information</h2>
            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                @if($reservation->status == 'pending')
                    bg-yellow-100 text-yellow-800
                @elseif($reservation->status == 'approved')
                    bg-green-100 text-green-800
                @elseif($reservation->status == 'rejected')
                    bg-red-100 text-red-800
                @elseif($reservation->status == 'cancelled')
                    bg-gray-100 text-gray-800
                @endif">
                {{ ucfirst($reservation->status) }}
            </span>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Laboratory</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->laboratory->name }}</p>
                    <p class="text-sm text-gray-600">{{ $reservation->laboratory->building }}, Room {{ $reservation->laboratory->room_number }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Reservation ID</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->id }}</p>
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

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Submitted On</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->created_at->format('M d, Y - H:i') }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Last Updated</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $reservation->updated_at->format('M d, Y - H:i') }}</p>
                </div>

                @if($reservation->status === 'rejected' && $reservation->rejection_reason)
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-sm font-medium text-red-500">Rejection Reason</h3>
                    <p class="mt-1 text-base text-red-600">{{ $reservation->rejection_reason }}</p>
                </div>
                @endif
            </div>

            <div class="mt-8 flex justify-between">
                <a href="{{ route('ruser.laboratory.reservations.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Back to Reservations
                </a>
                
                @php
                    $canCancel = ($reservation->status === 'pending') || 
                                ($reservation->status === 'approved' && 
                                \Carbon\Carbon::parse($reservation->reservation_date . ' ' . $reservation->start_time)
                                ->diffInHours(now()) >= 24);
                @endphp
                
                @if($canCancel)
                <form action="{{ route('ruser.laboratory.reservations.cancel', $reservation) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Are you sure you want to cancel this reservation?')"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Cancel Reservation
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
