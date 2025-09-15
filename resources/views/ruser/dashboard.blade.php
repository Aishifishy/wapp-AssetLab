@extends('layouts.ruser')

@section('title', 'Dashboard')
@section('header', 'Dashboard Overview')

@section('content')
<div class="space-y-4">
    <!-- Enhanced Dashboard Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Pending Requests Card -->
        <div class="glass-card overflow-hidden rounded-2xl">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 shadow-lg">
                                <svg class="h-7 w-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-gray-600 mb-1">Pending Requests</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $pendingRequests }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm">
                                @if($pendingRequests > 0)
                                    <span class="text-orange-600 font-medium">⏳ Awaiting approval</span>
                                @else
                                    <span class="text-green-600 font-medium">✅ All caught up!</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Currently Borrowed Card -->
        <div class="glass-card overflow-hidden rounded-2xl">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-4 shadow-lg">
                                <svg class="h-7 w-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-gray-600 mb-1">Currently Borrowed</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $currentlyBorrowed }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm">
                                @if($currentlyBorrowed > 0)
                                    <span class="text-blue-600 font-medium">🔧 In your possession</span>
                                @else
                                    <span class="text-gray-500 font-medium">📋 No items borrowed</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Returns Card -->
        <div class="glass-card overflow-hidden rounded-2xl">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-4 shadow-lg">
                                <svg class="h-7 w-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-gray-600 mb-1">Upcoming Returns</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $upcomingReturns ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm">
                                @if(($upcomingReturns ?? 0) > 0)
                                    <span class="text-red-600 font-medium">⚠️ Due soon</span>
                                @else
                                    <span class="text-green-600 font-medium">✨ No pending returns</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Enhanced Quick Action Buttons -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Equipment Reservation Button -->
        <a href="{{ route('ruser.equipment.borrow') }}" class="relative overflow-hidden glass-card rounded-2xl p-8 bg-gradient-to-br from-blue-50 to-indigo-50 border-l-4 border-blue-500">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 12l-6-3"/>
                        </svg>
                    </div>
                    <div class="text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Reserve Equipment</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Browse and request available equipment for your academic needs with real-time availability checking.</p>
                
                <div class="mt-4 flex items-center text-sm text-blue-600 font-medium">
                    <span>Get Started</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </a>

        <!-- Laboratory Booking Button -->
        <a href="{{ route('ruser.laboratory.index') }}" class="relative overflow-hidden glass-card rounded-2xl p-8 bg-gradient-to-br from-purple-50 to-pink-50 border-l-4 border-purple-500">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Book Laboratory</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Reserve laboratory spaces with calendar scheduling and conflict prevention for your research activities.</p>
                
                <div class="mt-4 flex items-center text-sm text-purple-600 font-medium">
                    <span>Book Now</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </a>
    </div>    <!-- Enhanced Recent Activities -->
    <div class="glass-card overflow-hidden rounded-2xl shadow-xl">
        <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-5 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Recent Activities</h2>
                        <p class="text-sm text-gray-600">Track your equipment and lab bookings</p>
                    </div>
                </div>
                
                <!-- Enhanced Activity Filter Buttons -->
                <div class="flex bg-white rounded-xl p-1 shadow-sm border border-gray-200">
                    <button onclick="updateActivityType('all')" 
                            data-activity-type="all"
                            class="px-4 py-2 text-sm font-semibold rounded-lg {{ $activityType == 'all' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 bg-gray-50' }}">
                        All
                    </button>
                    <button onclick="updateActivityType('equipment')" 
                            data-activity-type="equipment"
                            class="px-4 py-2 text-sm font-semibold rounded-lg {{ $activityType == 'equipment' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-600 bg-gray-50' }}">
                        Equipment
                    </button>
                    <button onclick="updateActivityType('laboratory')" 
                            data-activity-type="laboratory"
                            class="px-4 py-2 text-sm font-semibold rounded-lg {{ $activityType == 'laboratory' ? 'bg-purple-600 text-white shadow-md' : 'text-gray-600 bg-gray-50' }}">
                        Laboratory
                    </button>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Time
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Activity
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 12l-6-3"/>
                                    </svg>
                                    {{ $activityType == 'laboratory' ? 'Laboratory' : ($activityType == 'equipment' ? 'Equipment' : 'Item') }}
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Status
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                                    </svg>
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">                        @forelse($recentActivities as $activity)
                        <tr class="hover:bg-gray-50 transition-colors {{ isset($activity['is_overdue']) && $activity['is_overdue'] ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex flex-col">
                                    <span>{{ $activity['time']->diffForHumans() }}</span>
                                    <span class="text-xs text-gray-400">{{ $activity['time']->format('M j, g:i A') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="space-y-1">
                                    {!! $activity['description'] !!}
                                    @if($activity['purpose'])
                                        <p class="text-xs text-gray-500">
                                            <svg class="w-3 h-3 inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            {{ Str::limit($activity['purpose'], 50) }}
                                        </p>
                                    @endif
                                    @if($activity['activity_type'] == 'laboratory' && isset($activity['reservation_date']))
                                        <p class="text-xs text-blue-600">
                                            <svg class="w-3 h-3 inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($activity['reservation_date'])->format('M d, Y') }}
                                            @if(isset($activity['start_time']) && isset($activity['end_time']))
                                                • {{ $activity['start_time'] }} - {{ $activity['end_time'] }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                <div class="flex items-center">
                                    @if($activity['activity_type'] == 'laboratory')
                                        <svg class="w-4 h-4 mr-2 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 mr-2 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2" />
                                        </svg>
                                    @endif
                                    <div>
                                        <div class="font-medium">{{ $activity['equipment_name'] }}</div>
                                        @if($activity['category_name'])
                                            <div class="text-xs text-gray-500">{{ $activity['category_name'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col items-center justify-center space-y-1">
                                    <x-status-badge :status="$activity['status']" :type="$activity['activity_type']" />
                                    @if(isset($activity['is_overdue']) && $activity['is_overdue'])
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            OVERDUE
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($activity['activity_type'] == 'request' && $activity['status'] == 'pending')
                                    <form method="POST" action="/equipment/request/{{ $activity['id'] }}/cancel" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to cancel this equipment request? This action cannot be undone.')"
                                                class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Cancel
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                @if($activityType == 'laboratory')
                                    Laboratory booking activities will appear here when you book a laboratory
                                @elseif($activityType == 'equipment')
                                    Equipment borrowing activities will appear here when you borrow equipment
                                @else
                                    No recent activities found. Start by borrowing equipment or booking a laboratory.
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination and Per-Page Controls -->
            <div class="mt-6 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                <!-- Per-page selector -->
                <div class="flex items-center space-x-2">
                    <label for="per-page" class="text-sm text-gray-600">Show:</label>
                    <select id="per-page" name="per_page" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 w-20" onchange="updatePerPage()">
                        <option value="5" {{ request('per_page', 5) == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 5) == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page', 5) == 15 ? 'selected' : '' }}>15</option>
                        <option value="20" {{ request('per_page', 5) == 20 ? 'selected' : '' }}>20</option>
                    </select>
                    <span class="text-sm text-gray-600">per page</span>
                </div>

                <!-- Pagination and View All Button -->
                <div class="flex items-center space-x-4">
                    @if(isset($recentActivities) && method_exists($recentActivities, 'hasPages') && $recentActivities->hasPages())
                        <!-- Pagination Links -->
                        <div class="flex items-center space-x-2">
                            <nav class="flex items-center space-x-1">
                                @if($recentActivities->onFirstPage())
                                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                                @else
                                    <a href="{{ $recentActivities->appends(request()->query())->previousPageUrl() }}" 
                                       class="px-3 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-blue-600 transition-colors">
                                        Previous
                                    </a>
                                @endif

                                <!-- Page Numbers -->
                                @foreach($recentActivities->getUrlRange(1, $recentActivities->lastPage()) as $page => $url)
                                    @if($page == $recentActivities->currentPage())
                                        <span class="px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" 
                                           class="px-3 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-blue-600 transition-colors">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach

                                @if($recentActivities->hasMorePages())
                                    <a href="{{ $recentActivities->appends(request()->query())->nextPageUrl() }}" 
                                       class="px-3 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-blue-600 transition-colors">
                                        Next
                                    </a>
                                @else
                                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                                @endif
                            </nav>
                        </div>
                    @endif

                    <!-- View All Button -->
                    @if($activityType == 'laboratory')
                        <a href="{{ route('ruser.laboratory.index') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-sm font-semibold rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-purple-500/20">
                            View All Laboratories
                            <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @elseif($activityType == 'equipment')
                        <a href="{{ route('ruser.equipment.borrow') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-lg hover:shadow-blue-500/20">
                            Browse Equipment
                            <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Activity Count Info -->
            @if(isset($recentActivities) && method_exists($recentActivities, 'total'))
            <div class="mt-4 text-sm text-gray-500 text-center">
                Showing {{ $recentActivities->firstItem() ?? 0 }} to {{ $recentActivities->lastItem() ?? 0 }} 
                of {{ $recentActivities->total() }} activities
            </div>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function updateActivityType(type) {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('activity_type', type);
        currentUrl.searchParams.delete('page'); // Reset to first page when changing type
        window.location.href = currentUrl.toString();
    }

    function updatePerPage() {
        const perPageSelect = document.getElementById('per-page');
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('per_page', perPageSelect.value);
        currentUrl.searchParams.delete('page'); // Reset to first page when changing per_page
        window.location.href = currentUrl.toString();
    }

    // Initialize activity type buttons
    document.addEventListener('DOMContentLoaded', function() {
        // Set active state for current activity type
        const urlParams = new URLSearchParams(window.location.search);
        const activeType = urlParams.get('activity_type') || 'all';
        
        // Update button states
        document.querySelectorAll('[data-activity-type]').forEach(button => {
            const buttonType = button.getAttribute('data-activity-type');
            if (buttonType === activeType) {
                button.classList.add('bg-blue-600', 'text-white', 'shadow-md');
                button.classList.remove('bg-white', 'text-gray-700', 'hover:bg-gray-50');
            } else {
                button.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
                button.classList.add('bg-white', 'text-gray-700', 'hover:bg-gray-50');
            }
        });
    });
</script>

@endsection
