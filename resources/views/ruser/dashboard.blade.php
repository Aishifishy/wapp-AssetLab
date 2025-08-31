@extends('layouts.ruser')

@section('title', 'Dashboard')
@section('header', 'Dashboard Overview')

@section('content')
<div class="space-y-4">
    <!-- Dashboard Stats Overview -->    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Pending Requests
                            </dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">
                                    {{ $pendingRequests }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Currently Borrowed
                            </dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">
                                    {{ $currentlyBorrowed }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Upcoming Returns
                            </dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">
                                    {{ $upcomingReturns ?? 0 }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
      <!-- Quick Action Buttons -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ route('ruser.equipment.borrow') }}" class="px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg shadow-md flex items-center justify-center gap-3 hover:from-blue-700 hover:to-blue-900 transition transform hover:scale-105">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span class="font-medium text-lg">Reserve Equipment</span>
        </a>
        <a href="{{ route('ruser.laboratory.index') }}" class="px-5 py-3 bg-gradient-to-r from-purple-600 to-purple-800 text-white rounded-lg shadow-md flex items-center justify-center gap-3 hover:from-purple-700 hover:to-purple-900 transition transform hover:scale-105">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="font-medium text-lg">Book Laboratory</span>
        </a>
    </div>    <!-- Recent Activities -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="px-4 py-3 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <h2 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Recent Activities
                </h2>
                
                <!-- Activity Filter Buttons -->
                <div class="inline-flex rounded-md shadow-sm w-full sm:w-auto">
                    <a href="{{ route('dashboard', ['activity_type' => 'all']) }}" 
                       class="px-4 py-2 text-sm font-medium flex-1 sm:flex-none text-center {{ $activityType == 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border border-gray-300 rounded-l-md">
                        All
                    </a>
                    <a href="{{ route('dashboard', ['activity_type' => 'equipment']) }}" 
                       class="px-4 py-2 text-sm font-medium flex-1 sm:flex-none text-center {{ $activityType == 'equipment' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border-t border-b border-r border-gray-300">
                        Equipment
                    </a>
                    <a href="{{ route('dashboard', ['activity_type' => 'laboratory']) }}" 
                       class="px-4 py-2 text-sm font-medium flex-1 sm:flex-none text-center {{ $activityType == 'laboratory' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border-t border-b border-r border-gray-300 rounded-r-md">
                        Laboratory
                    </a>
                </div>
            </div>
        </div>
          <div class="p-4">
            <div class="overflow-x-auto">                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Activity
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $activityType == 'laboratory' ? 'Laboratory' : ($activityType == 'equipment' ? 'Equipment' : 'Item') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
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
              <div class="mt-4 flex justify-end">
                @if($activityType == 'laboratory')
                <a href="{{ route('ruser.laboratory.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                    View All Laboratories
                    <svg class="ml-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>                </a>
                @else
                <!-- Removed history link -->
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
