@extends('layouts.ruser')

@section('title', 'Reservation Submitted')
@section('header', 'Reservation Confirmation')

@push('styles')
<style>
@media print {
    .no-print {
        display: none !important;
    }
    .print-only {
        display: block !important;
    }
    body {
        font-size: 12pt;
        line-height: 1.4;
    }
    .bg-green-50, .bg-blue-50, .bg-amber-50 {
        background: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
    }
    .text-green-900, .text-blue-900, .text-amber-900 {
        color: #333 !important;
    }
    .text-green-800, .text-blue-800, .text-amber-800 {
        color: #555 !important;
    }
}
.print-only {
    display: none;
}
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Print Only Header -->
    <div class="print-only mb-6 text-center border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-900">Laboratory Reservation Confirmation</h1>
        <p class="text-sm text-gray-600 mt-1">wappAssetLab - Resource Management System</p>
        <p class="text-xs text-gray-500 mt-1">Printed on: {{ now()->format('F d, Y \a\t g:i A') }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Success Header -->
        <div class="bg-green-50 border-b border-green-200 px-6 py-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h1 class="text-xl font-semibold text-green-900">Reservation Request Submitted Successfully</h1>
                    <p class="text-sm text-green-700 mt-1">Your laboratory reservation has been submitted for administrator review.</p>
                </div>
            </div>
        </div>

        <!-- Reservation Details -->
        <div class="px-6 py-6">
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Reservation Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Laboratory</h3>
                        <p class="mt-1 text-base text-gray-900">{{ $reservation->laboratory->name }}</p>
                        <p class="text-sm text-gray-600">{{ $reservation->laboratory->building }}, Room {{ $reservation->laboratory->room_number }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Reservation ID</h3>
                        <p class="mt-1 text-base text-gray-900 font-mono">#{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Date</h3>
                        <p class="mt-1 text-base text-gray-900">{{ $reservation->reservation_date->format('F d, Y') }}</p>
                        <p class="text-sm text-gray-600">{{ $reservation->reservation_date->format('l') }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Time</h3>
                        <p class="mt-1 text-base text-gray-900">
                            {{ \Carbon\Carbon::parse($reservation->start_time)->format('g:i A') }} - 
                            {{ \Carbon\Carbon::parse($reservation->end_time)->format('g:i A') }}
                        </p>
                        <p class="text-sm text-gray-600">{{ $reservation->duration }}</p>
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
                        <h3 class="text-sm font-medium text-gray-500">Status</h3>
                        <div class="mt-1">
                            <x-status-badge status="pending" type="reservation" />
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Submitted On</h3>
                        <p class="mt-1 text-base text-gray-900">{{ $reservation->created_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- What Happens Next -->
            <div class="alert-info">
                <h3 class="text-sm font-medium mb-2 flex items-center">
                    <svg class="icon-sm mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    What Happens Next?
                </h3>
                <div class="text-sm space-y-2">
                    <p>• Your reservation request is currently <strong>pending</strong> administrator review.</p>
                    <p>• You will receive an email notification once your request has been approved or if any changes are needed.</p>
                    <p>• You can check the status of your reservation at any time by viewing your reservations list.</p>
                    <p>• If you need to make changes or cancel this reservation, please contact the administrator or cancel it from your reservations list.</p>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="alert-warning">
                <h3 class="text-sm font-medium mb-2 flex items-center">
                    <svg class="icon-sm mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    Important Notes
                </h3>
                <div class="text-sm space-y-2">
                    <p>• Please arrive on time for your scheduled reservation.</p>
                    <p>• Reservations cannot be cancelled within 24 hours of the scheduled time.</p>
                    <p>• Ensure all equipment is properly handled and returned in good condition.</p>
                    <p>• Keep your reservation ID for reference: <strong>#{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex-responsive-between space-responsive no-print">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('ruser.dashboard') }}" class="btn-primary">
                        <svg class="icon-sm mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        View My Reservations
                    </a>
                    
                    <a href="{{ route('ruser.laboratory.reservations.calendar') }}" class="btn-secondary">
                        <svg class="icon-sm mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h3z" />
                        </svg>
                        View Calendar
                    </a>

                    <button onclick="window.print()" class="btn-info">
                        <svg class="icon-sm mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Confirmation
                    </button>
                </div>
                
                <a href="{{ route('ruser.laboratory.index') }}" class="btn-success">
                    <svg class="icon-sm mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Make Another Reservation
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
