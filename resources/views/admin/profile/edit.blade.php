@extends('layouts.admin')

@section('title', 'Admin Profile Settings')
@section('header', 'Admin Profile Settings')

@section('content')
<div class="space-y-4">
    <!-- Profile Information Section -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="px-4 py-3 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Admin Profile Information
            </h2>
        </div>
        
        <div class="p-4">
            <form method="post" action="{{ route('admin.profile.update') }}" class="space-y-6">
                @csrf
                @method('patch')

                <div>
                    <label for="name" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-colors">
                    <x-form-error field="name" />
                </div>

                <div>
                    <label for="email" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-colors">
                    <x-form-error field="email" />
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div class="flex items-center gap-4">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Changes
                        </button>

                        @if (session('status') === 'profile-updated')
                            <div class="flex items-center text-green-600">
                                <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm font-medium">Profile updated successfully!</span>
                            </div>
                        @endif
                    </div>
                    
                    <a href="{{ route('admin.dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Security Section -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="px-4 py-3 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Account Security
            </h2>
        </div>
        
        <div class="p-4">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Warning: Admin Account Deletion</h3>
                        <p class="mt-1 text-sm text-red-700">
                            Once your admin account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm account deletion.
                        </p>
                    </div>
                </div>
            </div>

            <form method="post" action="{{ route('admin.profile.destroy') }}" class="space-y-6">
                @csrf
                @method('delete')

                <div>
                    <label for="password" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Confirm Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your current password"
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-200 focus:outline-none transition-colors">
                    <x-form-error field="password" bag="adminDeletion" />
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition"
                            onclick="return confirm('Are you absolutely sure you want to delete your admin account? This action cannot be undone.')">
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Admin Account
                    </button>
                    
                    <p class="text-xs text-gray-500">
                        This action is permanent and cannot be undone
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection