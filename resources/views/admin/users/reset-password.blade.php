@extends('layouts.admin')

@section('title', 'Reset User Password')
@section('page-title', 'Reset Password')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a></li>
    <li class="breadcrumb-item active">Reset Password</li>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <x-flash-messages />

    <!-- Header Section -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-orange-50">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-key text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-semibold text-gray-900">Reset Password</h2>
                    <p class="text-sm text-gray-600">Update the password for {{ $user->name }}</p>
                </div>
            </div>
        </div>
        
        <!-- User Information -->
        <div class="px-6 py-4 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center">
                    <i class="fas fa-user text-gray-400 mr-3"></i>
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-user-tag text-gray-400 mr-3"></i>
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ ucfirst($user->role) }}</div>
                        <div class="text-sm text-gray-500">{{ $user->department }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Form -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-lock mr-2 text-gray-600"></i>
                New Password
            </h3>
            <p class="mt-1 text-sm text-gray-600">
                Enter a new password for this user. The user will be able to use this password immediately.
            </p>
        </div>
        
        <form action="{{ route('admin.users.reset-password.store', $user) }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <!-- Security Notice -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-yellow-800">Security Notice</h4>
                        <div class="mt-1 text-sm text-yellow-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>The new password will take effect immediately</li>
                                <li>The user will not be notified of the password change via email</li>
                                <li>Please inform the user of their new password securely</li>
                                <li>Consider requiring the user to change the password on their next login</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-key mr-1 text-gray-500"></i>
                    New Password *
                </label>
                <div class="relative">
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('password') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                           placeholder="Enter new password">
                    <button type="button" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            onclick="togglePasswordVisibility('password', this)">
                        <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="password-icon"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Password Confirmation Field -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-1 text-gray-500"></i>
                    Confirm New Password *
                </label>
                <div class="relative">
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           required
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('password_confirmation') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                           placeholder="Confirm new password">
                    <button type="button" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            onclick="togglePasswordVisibility('password_confirmation', this)">
                        <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="password_confirmation-icon"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Password Requirements -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-blue-800 mb-2 flex items-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    Password Requirements
                </h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2 text-xs"></i>
                        At least 8 characters long
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2 text-xs"></i>
                        Mix of uppercase and lowercase letters recommended
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2 text-xs"></i>
                        Include numbers and special characters for better security
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="flex space-x-3">
                    <a href="{{ route('admin.users.show', $user) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to User
                    </a>
                    <a href="{{ route('admin.users.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <i class="fas fa-list mr-2"></i>
                        Users List
                    </a>
                </div>
                
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-yellow-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200 shadow-sm">
                    <i class="fas fa-key mr-2"></i>
                    Reset Password
                </button>
            </div>
        </form>
    </div>

    <!-- Additional Actions -->
    <div class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-tools mr-2 text-gray-600"></i>
                Additional Actions
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('admin.users.edit', $user) }}" 
                   class="inline-flex items-center px-4 py-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 hover:bg-blue-100 transition-colors duration-200">
                    <i class="fas fa-edit mr-3 text-blue-600"></i>
                    <div>
                        <div class="font-medium">Edit User Details</div>
                        <div class="text-sm text-blue-600">Update name, email, role, etc.</div>
                    </div>
                </a>
                <a href="{{ route('admin.users.show', $user) }}" 
                   class="inline-flex items-center px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-green-700 hover:bg-green-100 transition-colors duration-200">
                    <i class="fas fa-eye mr-3 text-green-600"></i>
                    <div>
                        <div class="font-medium">View User Profile</div>
                        <div class="text-sm text-green-600">See full user details and activity</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePasswordVisibility(fieldId, button) {
    const field = document.getElementById(fieldId);
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Real-time password validation feedback
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirmation');
    
    function validatePasswords() {
        const password = passwordField.value;
        const confirm = confirmField.value;
        
        // Remove existing validation classes
        confirmField.classList.remove('border-red-300', 'border-green-300');
        
        if (confirm && password !== confirm) {
            confirmField.classList.add('border-red-300');
        } else if (confirm && password === confirm) {
            confirmField.classList.add('border-green-300');
        }
    }
    
    passwordField.addEventListener('input', validatePasswords);
    confirmField.addEventListener('input', validatePasswords);
    
    // Form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = passwordField.value;
        const confirm = confirmField.value;
        
        if (password !== confirm) {
            e.preventDefault();
            alert('Passwords do not match. Please check and try again.');
            confirmField.focus();
        }
    });
});
</script>
@endpush