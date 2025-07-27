@extends('layouts.admin')

@section('title', 'User Details')
@section('header', 'User Details')

@section('content')
<div class="space-y-6">
    <x-flash-messages />

    <!-- Header with actions -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800">{{ $user->name }}</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.users.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Users
            </a>
            <a href="{{ route('admin.users.edit', $user) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i> Edit User
            </a>
            <a href="{{ route('admin.users.reset-password', $user) }}" 
               class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                <i class="fas fa-key mr-2"></i> Reset Password
            </a>
        </div>
    </div>

    <!-- User Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Profile -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="text-center">
                    <div class="mx-auto h-32 w-32 rounded-full bg-gray-300 flex items-center justify-center mb-4">
                        <i class="fas fa-user text-gray-500 text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ $user->email }}</p>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Role:</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $user->role === 'student' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $user->role === 'faculty' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $user->role === 'staff' ? 'bg-purple-100 text-purple-800' : '' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Department:</span>
                            <span class="text-sm text-gray-900">{{ $user->department ?? 'N/A' }}</span>
                        </div>
                        
                        @if($user->contact_number)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Contact:</span>
                            <span class="text-sm text-gray-900">{{ $user->contact_number }}</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">RFID Tag:</span>
                            @if($user->rfid_tag)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-id-card mr-1"></i>
                                    {{ $user->rfid_tag }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">Not set</span>
                            @endif
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Member Since:</span>
                            <span class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics and Activity -->
        <div class="lg:col-span-2">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-box text-blue-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Equipment Requests</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_equipment_requests'] }}</div>
                            <div class="text-xs text-green-600">{{ $stats['approved_equipment_requests'] }} approved</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-hand-holding text-yellow-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Currently Borrowed</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['currently_borrowed'] }}</div>
                            <div class="text-xs text-gray-500">equipment items</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if($user->rfid_tag)
                                <i class="fas fa-id-card text-green-500 text-2xl"></i>
                            @else
                                <i class="fas fa-id-card text-gray-400 text-2xl"></i>
                            @endif
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">RFID Status</div>
                            <div class="text-lg font-bold {{ $user->rfid_tag ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $user->rfid_tag ? 'Configured' : 'Not Set' }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $user->rfid_tag ? 'Ready for onsite borrowing' : 'Manual entry required' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Equipment Requests -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Equipment Requests</h3>
                </div>
                <div class="p-6">
                    @if($user->equipmentRequests->count() > 0)
                        <div class="space-y-4">
                            @foreach($user->equipmentRequests as $request)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $request->equipment->name ?? 'Equipment N/A' }}</div>
                                            <div class="text-xs text-gray-500">{{ $request->created_at->format('M d, Y H:i') }}</div>
                                            @if($request->purpose)
                                                <div class="text-xs text-gray-600 mt-1">{{ Str::limit($request->purpose, 50) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $request->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                        @if($request->status === 'approved' && $request->returned_at)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Returned
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($user->equipmentRequests()->count() > 10)
                            <div class="mt-4 text-center">
                                <p class="text-sm text-gray-500">Showing latest 10 requests</p>
                            </div>
                        @endif
                    @else
                        <p class="text-gray-500 text-center py-4">No equipment requests found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
