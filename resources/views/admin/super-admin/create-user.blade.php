@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Create New User</h1>
                <p class="text-gray-600 mt-1">Add a new user to the system</p>
            </div>
            <a href="{{ route('admin.super-admin.index') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">User Information</h2>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.super-admin.users.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('name') border-red-500 @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('email') border-red-500 @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="password" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('password') border-red-500 @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" 
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">User Role</label>
                            <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('role') border-red-500 @enderror" 
                                    id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                                <option value="faculty" {{ old('role') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <input type="text" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('department') border-red-500 @enderror" 
                                   id="department" name="department" value="{{ old('department') }}">
                            @error('department')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                            <input type="text" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('contact_number') border-red-500 @enderror" 
                                   id="contact_number" name="contact_number" value="{{ old('contact_number') }}">
                            @error('contact_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="rfid_tag" class="block text-sm font-medium text-gray-700 mb-2">RFID Tag</label>
                            <input type="text" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('rfid_tag') border-red-500 @enderror" 
                                   id="rfid_tag" name="rfid_tag" value="{{ old('rfid_tag') }}">
                            @error('rfid_tag')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Optional: For equipment checkout via RFID</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-6">
                        <a href="{{ route('admin.super-admin.index') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <i class="fas fa-save mr-2"></i>Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
