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
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h3 class="text-xl font-semibold">Recent Activities</h3>
                
                <div class="d-flex align-items-center space-x-4">
                    <!-- Per Page Selector -->
                    <div class="flex items-center space-x-2">
                        <label for="perPageSelect" class="text-sm text-gray-600">Show:</label>
                        <select id="perPageSelect" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 w-20">
                            <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page') == 10 || !request('per_page') ? 'selected' : '' }}>10</option>
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-sm text-gray-600">per page</span>
                    </div>
                    
                    <!-- Activity Filter Buttons -->
                    <div class="inline-flex rounded-md shadow-sm">
                        <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['activity_type' => 'all', 'page' => 1])) }}" 
                           class="px-4 py-2 text-sm font-medium {{ $activityType == 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border border-gray-300 rounded-l-md">
                            All
                        </a>
                        <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['activity_type' => 'equipment', 'page' => 1])) }}" 
                           class="px-4 py-2 text-sm font-medium {{ $activityType == 'equipment' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border-t border-b border-r border-gray-300">
                            Equipment
                        </a>
                        <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['activity_type' => 'laboratory', 'page' => 1])) }}" 
                           class="px-4 py-2 text-sm font-medium {{ $activityType == 'laboratory' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border-t border-b border-r border-gray-300 rounded-r-md">
                            Laboratory
                        </a>
                    </div>
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
                                @if(isset($activity->notes) && $activity->notes)
                                <p class="text-xs text-gray-500 mt-1">{{ Str::limit($activity->notes, 50) }}</p>
                                @endif
                                @if(isset($activity->admin_info) && $activity->admin_info)
                                <p class="text-xs text-blue-600 mt-1 font-medium">{{ $activity->admin_info }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $activity->user_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                {{ $activity->item_name ?? 'N/A' }} <br>
                                <small>{{ $activity->item_num ?? '' }}</small>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <x-status-badge :status="$activity->status ?? 'unknown'" :type="$activity->activity_type === 'equipment' ? 'request' : 'reservation'" />
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm whitespace-nowrap">
                                @if($activity->activity_type === 'equipment')
                                    <a href="{{ route('admin.equipment.borrow-requests') }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                        View Details
                                    </a>
                                @else
                                    <a href="{{ route('admin.laboratory.reservations') }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                        View Details
                                    </a>
                                @endif
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
            
            <!-- Pagination -->
            @if($recentActivities instanceof \Illuminate\Pagination\LengthAwarePaginator && $recentActivities->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-sm text-gray-600">
                    Showing {{ $recentActivities->firstItem() ?? 0 }} to {{ $recentActivities->lastItem() ?? 0 }} of {{ $recentActivities->total() }} results
                </div>
                <div>
                    {{ $recentActivities->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Add any dashboard-specific JavaScript here
    // Per page functionality
    document.addEventListener('DOMContentLoaded', function() {
        const perPageSelect = document.getElementById('perPageSelect');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                const url = new URL(window.location);
                url.searchParams.set('per_page', this.value);
                url.searchParams.delete('page'); // Reset to first page when changing per_page
                window.location.href = url.toString();
            });
        }
    });
</script>
@endpush