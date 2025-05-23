@extends('layouts.ruser')

@section('title', 'Dashboard')
@section('header', 'Dashboard Overview')

@section('content')
<div class="space-y-4">
    <!-- Dashboard Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
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
        </div>
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Available Equipment
                            </dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">
                                    {{ $availableEquipment }}
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
                    <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Available Labs
                            </dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">
                                    {{ $availableLabs }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Action Buttons -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <a href="{{ route('ruser.equipment.borrow') }}" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg shadow flex items-center justify-center gap-2 hover:from-blue-700 hover:to-blue-800 transition">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span class="font-medium">Reserve Equipment</span>
        </a>
        <a href="{{ route('ruser.laboratory.index') }}" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg shadow flex items-center justify-center gap-2 hover:from-purple-700 hover:to-purple-800 transition">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="font-medium">Book Laboratory</span>
        </a>
    </div>
    
    <!-- Recent Activities -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="px-4 py-3 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Recent Activities
            </h2>
        </div>
        
        <div class="p-3">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Time
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Activity
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Equipment
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentActivities as $activity)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $activity['time']->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                {{ $activity['description'] }}
                                <p class="text-xs text-gray-500 mt-1">{{ Str::limit($activity['purpose'], 50) }}</p>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $activity['equipment_name'] }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($activity['status_class'] === 'green') bg-green-100 text-green-800
                                    @elseif($activity['status_class'] === 'yellow') bg-yellow-100 text-yellow-800
                                    @elseif($activity['status_class'] === 'blue') bg-blue-100 text-blue-800
                                    @elseif($activity['status_class'] === 'red') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($activity['status']) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-center text-gray-500">
                                No recent activities
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 flex justify-end">
                <a href="{{ route('ruser.equipment.history') }}" class="text-sm text-blue-600 hover:text-blue-900">View Full History →</a>
            </div>
        </div>
    </div>
</div>
@endsection
