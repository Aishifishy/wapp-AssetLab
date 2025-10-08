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
            <button type="button" 
                    onclick="openResetPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}')"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                <i class="fas fa-key mr-2"></i> Reset Password
            </button>
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

<!-- Reset Password Modal -->
<div id="resetPasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900 flex items-center">
                    <i class="fas fa-key text-yellow-600 mr-2"></i>
                    Reset User Password
                </h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeResetPasswordModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- User Info -->
            <div id="resetUserInfo" class="mb-4 p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900" id="resetUserName"></div>
                        <div class="text-sm text-gray-500" id="resetUserEmail"></div>
                    </div>
                </div>
            </div>
            
            <!-- Security Notice -->
            <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-yellow-800">Security Notice</h4>
                        <p class="mt-1 text-sm text-yellow-700">
                            The new password will take effect immediately. Please inform the user securely.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Reset Password Form -->
            <form id="resetPasswordForm" method="POST">
                @csrf
                
                <!-- New Password -->
                <div class="mb-4">
                    <label for="modal_password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1 text-gray-500"></i>
                        New Password
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="password" 
                               id="modal_password" 
                               required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter new password">
                        <button type="button" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                onclick="toggleModalPasswordVisibility('modal_password', this)">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="modal_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1 text-gray-500"></i>
                        Confirm Password
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="password_confirmation" 
                               id="modal_password_confirmation" 
                               required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Confirm new password">
                        <button type="button" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                onclick="toggleModalPasswordVisibility('modal_password_confirmation', this)">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Password Requirements -->
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <h4 class="text-xs font-medium text-blue-800 mb-2 flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Password Requirements
                    </h4>
                    <ul class="text-xs text-blue-700 space-y-1">
                        <li>• At least 8 characters long</li>
                        <li>• Mix of letters, numbers recommended</li>
                        <li>• Special characters for better security</li>
                    </ul>
                </div>
                
                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-3 border-t border-gray-200">
                    <button type="button" 
                            onclick="closeResetPasswordModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 border border-transparent rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200">
                        <i class="fas fa-key mr-1"></i>
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Reset Password Modal Functions
function openResetPasswordModal(userId, userName, userEmail) {
    document.getElementById('resetUserName').textContent = userName;
    document.getElementById('resetUserEmail').textContent = userEmail;
    document.getElementById('resetPasswordForm').action = `/admin/users/${userId}/reset-password`;
    document.getElementById('resetPasswordModal').classList.remove('hidden');
    document.getElementById('modal_password').focus();
}

function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.add('hidden');
    document.getElementById('resetPasswordForm').reset();
    // Clear any validation states
    document.getElementById('modal_password').classList.remove('border-red-300', 'border-green-300');
    document.getElementById('modal_password_confirmation').classList.remove('border-red-300', 'border-green-300');
}

function toggleModalPasswordVisibility(fieldId, button) {
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

// Handle reset password form submission
document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const password = document.getElementById('modal_password').value;
    const confirmPassword = document.getElementById('modal_password_confirmation').value;
    
    if (password !== confirmPassword) {
        alert('Passwords do not match. Please check and try again.');
        document.getElementById('modal_password_confirmation').focus();
        return;
    }
    
    if (password.length < 8) {
        alert('Password must be at least 8 characters long.');
        document.getElementById('modal_password').focus();
        return;
    }
    
    // Submit the form
    this.submit();
});

// Real-time password validation for modal
document.addEventListener('DOMContentLoaded', function() {
    const modalPassword = document.getElementById('modal_password');
    const modalConfirm = document.getElementById('modal_password_confirmation');
    
    function validateModalPasswords() {
        const password = modalPassword.value;
        const confirm = modalConfirm.value;
        
        // Remove existing validation classes
        modalConfirm.classList.remove('border-red-300', 'border-green-300');
        
        if (confirm && password !== confirm) {
            modalConfirm.classList.add('border-red-300');
        } else if (confirm && password === confirm) {
            modalConfirm.classList.add('border-green-300');
        }
    }
    
    modalPassword.addEventListener('input', validateModalPasswords);
    modalConfirm.addEventListener('input', validateModalPasswords);
    
    // Close modal when clicking outside
    document.getElementById('resetPasswordModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeResetPasswordModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('resetPasswordModal').classList.contains('hidden')) {
            closeResetPasswordModal();
        }
    });
});
</script>
@endpush
