@extends('layouts.ruser')

@section('title', 'Quick Laboratory Reservation')
@section('header', 'Quick Laboratory Reservation')

@section('content')
<div class="space-y-6">
    <x-flash-messages />

    <!-- Quick Reservation Form -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Quick Reservation</h2>
        </div>
        <div class="p-6">
            @if($recentReservations->isEmpty())
                <div class="text-center py-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No recent reservations found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        You need to make at least one reservation before you can use the quick reservation feature.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('ruser.laboratory.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Make Your First Reservation
                        </a>
                    </div>
                </div>
            @else
                <form action="{{ route('ruser.laboratory.reservations.quick-store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="template" class="block text-sm font-medium text-gray-700">Based on Previous Reservation</label>
                            <select id="template" name="template" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                                <option value="">Select a previous reservation</option>
                                @foreach($recentReservations as $prevReservation)
                                    <option value="{{ $prevReservation->id }}">
                                        {{ $prevReservation->laboratory->name }} - 
                                        {{ \Illuminate\Support\Str::limit($prevReservation->purpose, 30) }} - 
                                        {{ $prevReservation->reservation_date->format('M d, Y') }}
                                    </option>
                                @endforeach                            </select>
                            <x-form-error field="template" />
                        </div>
                        
                        <div>
                            <label for="reservation_date" class="block text-sm font-medium text-gray-700">Date <span class="text-red-500">*</span></label>
                            <input type="date" name="reservation_date" id="reservation_date" required
                                min="{{ date('Y-m-d') }}"
                                value="{{ old('reservation_date') }}"                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="reservation_date" />
                        </div>
                        
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time <span class="text-red-500">*</span></label>
                            <input type="time" name="start_time" id="start_time" required
                                value="{{ old('start_time') }}"                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="start_time" />
                        </div>
                        
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700">End Time <span class="text-red-500">*</span></label>
                            <input type="time" name="end_time" id="end_time" required
                                value="{{ old('end_time') }}"                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <x-form-error field="end_time" />
                        </div>
                        
                        <div class="col-span-1 md:col-span-2">
                            <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose <span class="text-red-500">*</span> <span class="text-xs text-gray-500">(Will be pre-filled from template)</span></label>
                            <textarea name="purpose" id="purpose" rows="3" required                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('purpose') }}</textarea>
                            <x-form-error field="purpose" />
                        </div>
                    </div>
                    
                    <div class="mt-6 flex flex-col sm:flex-row sm:justify-between space-y-3 sm:space-y-0 sm:space-x-3">
                        <a href="{{ route('ruser.laboratory.reservations.index') }}" class="px-4 py-2 bg-gray-200 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 text-center">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                            Submit Quick Reservation
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Expose template data for reservation manager
    window.reservationTemplates = {
        @foreach($recentReservations as $prevReservation)
        '{{ $prevReservation->id }}': {
            purpose: `{{ $prevReservation->purpose }}`,
            start_time: '{{ \Carbon\Carbon::parse($prevReservation->start_time)->format('H:i') }}',
            end_time: '{{ \Carbon\Carbon::parse($prevReservation->end_time)->format('H:i') }}',
            laboratory_id: '{{ $prevReservation->laboratory_id }}',
            laboratory_name: '{{ $prevReservation->laboratory->name }}'
        },
        @endforeach
    };
</script>
@endpush
@endsection
