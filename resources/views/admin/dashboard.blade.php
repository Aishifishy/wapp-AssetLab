  @extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="row">
    <!-- Equipment Overview -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="d-flex justify-content-between align-items-center mb-6">
                <h3 class="text-lg font-semibold">Equipment Status</h3>
                <i class="fas fa-tools text-blue-500 text-2xl"></i>
            </div>
            <div class="row g-2">
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-blue-50 rounded-lg text-center">
                        <div class="text-gray-600 text-sm">Total Equipment</div>
                        <div class="font-bold text-blue-600 text-xl mt-2">{{ $totalEquipment ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-yellow-50 rounded-lg text-center">
                        <div class="text-gray-600 text-sm">Currently Borrowed</div>
                        <div class="font-bold text-yellow-600 text-xl mt-2">{{ $borrowedEquipment ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-red-50 rounded-lg text-center">
                        <div class="text-gray-600 text-sm">Pending Requests</div>
                        <div class="font-bold text-red-600 text-xl mt-2">{{ $pendingRequests ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.equipment.index') }}" class="mt-4 d-inline-flex align-items-center text-blue-600 hover:text-blue-800 text-sm">
                Manage Equipment <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <!-- Laboratory Overview -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="d-flex justify-content-between align-items-center mb-6">
                <h3 class="text-lg font-semibold">Laboratory Status</h3>
                <i class="fas fa-desktop text-green-500 text-2xl"></i>
            </div>
                        <div class="row g-2">
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-green-50 rounded-lg text-center">
                        <div class="text-gray-600 text-sm">Today's Bookings</div>
                        <div class="font-bold text-green-600 text-xl mt-2">{{ $todaysBookings ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-orange-50 rounded-lg text-center">
                        <div class="text-gray-600 text-sm">Pending Reservation</div>
                        <div class="font-bold text-orange-600 text-xl mt-2">{{ $pendingReservations ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-purple-50 rounded-lg text-center">
                        <div class="text-gray-600 text-sm">Active Classes</div>
                        <div class="font-bold text-purple-600 text-xl mt-2">{{ $activeClasses ?? 0 }}</div>
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
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="text-xl font-semibold">Recent Activities</h3>
                
                <!-- Activity Filter Buttons -->
                <div class="inline-flex rounded-md shadow-sm">
                    <a href="{{ route('admin.dashboard', ['activity_type' => 'all']) }}" 
                       class="px-4 py-2 text-sm font-medium {{ $activityType == 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border border-gray-300 rounded-l-md">
                        All
                    </a>
                    <a href="{{ route('admin.dashboard', ['activity_type' => 'equipment']) }}" 
                       class="px-4 py-2 text-sm font-medium {{ $activityType == 'equipment' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border-t border-b border-r border-gray-300">
                        Equipment
                    </a>
                    <a href="{{ route('admin.dashboard', ['activity_type' => 'laboratory']) }}" 
                       class="px-4 py-2 text-sm font-medium {{ $activityType == 'laboratory' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border-t border-b border-r border-gray-300 rounded-r-md">
                        Laboratory
                    </a>
                </div>
            </div>
        </div>
        
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $activityType == 'laboratory' ? 'Laboratory' : 'Equipment' }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentActivities ?? [] as $activity)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $activity->created_at ? $activity->created_at->diffForHumans() : 'N/A' }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900">
                                {{ $activity->description ?? 'N/A' }}
                                @if(isset($activity->notes))
                                <p class="text-xs text-gray-500 mt-1">{{ Str::limit($activity->notes, 50) }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $activity->user_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                {{ $activity->item_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <x-status-badge :status="$activity->status ?? 'unknown'" :type="$activity->activity_type ?? 'equipment'" />
                            </td>
                            <td class="px-4 py-4 text-sm whitespace-nowrap">
                                <a href="#" class="text-blue-600 hover:text-blue-900">View Details</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                @if($activityType == 'laboratory')
                                    No laboratory activities found
                                @elseif($activityType == 'equipment')
                                    No equipment activities found
                                @else
                                    No recent activities found
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 flex justify-end">
                <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                    View All Activities 
                    <svg class="ml-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

<!-- @push('scripts')
<script>
    // Add any dashboard-specific JavaScript here
</script>
@endpush  -->