@extends('layouts.ruser')

@section('title', 'Laboratory Reservation')
@section('header', 'Computer Laboratory Reservation')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Available Laboratories</h2>
                @if($currentTerm)
                    <p class="text-sm text-gray-600">Current Term: {{ $currentTerm->name }}</p>
                @else
                    <p class="text-sm text-red-500">No active academic term set</p>
                @endif
            </div>

            @if(!$currentTerm)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                    <p>There is no active academic term. Laboratory reservations are not available at this time.</p>
                </div>
            @endif

            <!-- Laboratory List -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Computers</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($laboratories as $lab)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $lab->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ $lab->building }}, Room {{ $lab->room_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ $lab->capacity }} seats</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ $lab->number_of_computers }} units</div>
                                </td>                                <td class="px-6 py-4">
                                    <x-status-badge :status="$lab->status" type="laboratory" />
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('ruser.laboratory.show', $lab) }}" 
                                       class="text-blue-600 hover:text-blue-900 font-medium">
                                        View Schedule
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No laboratories available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
