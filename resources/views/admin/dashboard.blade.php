@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="row">
    <!-- Equipment Overview -->
    <div class="col-md-6 mb-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="d-flex justify-content-between align-items-center mb-6">
                <h3 class="text-lg font-semibold">Equipment Status</h3>
                <i class="fas fa-tools text-blue-500 text-2xl"></i>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <div class="text-gray-600">Total Equipment</div>
                        <div class="font-bold text-blue-600 text-lg text-center mt-2">{{ $totalEquipment ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-yellow-50 rounded-lg">
                        <div class="text-gray-600">Currently Borrowed</div>
                        <div class="font-bold text-yellow-600 text-lg text-center mt-2">{{ $borrowedEquipment ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-red-50 rounded-lg">
                        <div class="text-gray-600">Pending Requests</div>
                        <div class="font-bold text-red-600 text-lg text-center mt-2">{{ $pendingRequests ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.equipment.manage') }}" class="mt-4 d-inline-flex align-items-center text-blue-600 hover:text-blue-800 text-sm">
                Manage Equipment <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <!-- Laboratory Overview -->
    <div class="col-md-6 mb-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="d-flex justify-content-between align-items-center mb-6">
                <h3 class="text-lg font-semibold">Laboratory Status</h3>
                <i class="fas fa-desktop text-green-500 text-2xl"></i>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-3 bg-green-50 rounded-lg">
                        <div class="text-gray-600">Today's Bookings</div>
                        <div class="font-bold text-green-600 text-lg text-center mt-2">{{ $todayBookings ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-yellow-50 rounded-lg">
                        <div class="text-gray-600">Pending Reservations</div>
                        <div class="font-bold text-yellow-600 text-lg text-center mt-2">{{ $pendingReservations ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <div class="text-gray-600">Active Classes</div>
                        <div class="font-bold text-blue-600 text-lg text-center mt-2">{{ $activeClasses ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.comlab.calendar') }}" class="mt-4 d-inline-flex align-items-center text-green-600 hover:text-green-800 text-sm">
                View Calendar <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="mt-8">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-xl font-semibold">Recent Activities</h3>
        <div class="d-flex gap-2">
            <button class="px-3 py-1 text-sm bg-blue-100 text-blue-600 rounded-pill hover:bg-blue-200">All</button>
            <button class="px-3 py-1 text-sm bg-gray-100 text-gray-600 rounded-pill hover:bg-gray-200">Equipment</button>
            <button class="px-3 py-1 text-sm bg-gray-100 text-gray-600 rounded-pill hover:bg-gray-200">Laboratory</button>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="table table-hover mb-0">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Time</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Activity</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentActivities ?? [] as $activity)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $activity->created_at ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $activity->description ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $activity->user_name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 d-inline-flex text-xs font-semibold rounded-pill 
                            {{ $activity->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $activity->status ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="#" class="text-blue-600 hover:text-blue-900">View Details</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No recent activities</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

<!-- @push('scripts')
<script>
    // Add any dashboard-specific JavaScript here
</script>
@endpush  -->