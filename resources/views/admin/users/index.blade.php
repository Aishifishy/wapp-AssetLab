@extends('layouts.admin')

@section('title', 'User Management')
@section('header', 'User Management')

@section('content')
<div class="space-y-6">
    <x-flash-messages />

    <!-- Header with action buttons -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Users</h1>
        <a href="{{ route('admin.users.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-plus mr-2"></i> Add New User
        </a>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-filter mr-2 text-blue-600"></i>
                Filters & Search
            </h2>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.users.index') }}" method="GET" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-1 text-gray-500"></i>
                        Search Users
                    </label>
                    <input type="text" 
                           name="search" 
                           id="search" 
                           value="{{ request('search') }}" 
                           placeholder="Name, email, department, or RFID..."
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                </div>

                <!-- Role Filter -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-tag mr-1 text-gray-500"></i>
                        Role
                    </label>
                    <select name="role" 
                            id="role" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-white">
                        <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Department Filter -->
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building mr-1 text-gray-500"></i>
                        Department
                    </label>
                    <select name="department" 
                            id="department" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-white">
                        <option value="all" {{ request('department') == 'all' ? 'selected' : '' }}>All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex flex-col justify-end space-y-2">
                    <button type="button" 
                            onclick="applyFilters()" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i> Apply Filters
                    </button>
                    <button type="button" 
                            onclick="clearFilters()" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i> Clear
                    </button>
                </div>
            </div>

            <!-- Additional Filter Options -->
            <div class="pt-4 border-t border-gray-200">
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="showOnlyRfid" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Show only users with RFID tags</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="showActiveOnly" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Show only active users</span>
                    </label>
                </div>
            </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-users mr-2 text-gray-600"></i>
                        Users ({{ $users->total() }} total)
                    </h2>
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
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <i class="fas fa-info-circle"></i>
                    <span>Click on any user row for details</span>
                </div>
            </div>
        </div>
        
        @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-user mr-1"></i> User
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-user-tag mr-1"></i> Role
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-building mr-1"></i> Department
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-id-card mr-1"></i> RFID Tag
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-box mr-1"></i> Requests
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-calendar mr-1"></i> Joined
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-cogs mr-1"></i> Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-150 cursor-pointer" onclick="window.location.href='{{ route('admin.users.show', $user) }}'">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full {{ $user->role === 'student' ? 'bg-blue-100' : ($user->role === 'faculty' ? 'bg-green-100' : 'bg-purple-100') }} flex items-center justify-center">
                                                <i class="fas fa-user {{ $user->role === 'student' ? 'text-blue-600' : ($user->role === 'faculty' ? 'text-green-600' : 'text-purple-600') }}"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $user->role === 'student' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $user->role === 'faculty' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $user->role === 'staff' ? 'bg-purple-100 text-purple-800' : '' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $user->department ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($user->rfid_tag)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-id-card mr-1"></i>
                                            {{ $user->rfid_tag }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            <i class="fas fa-times mr-1"></i>
                                            Not set
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $user->equipment_requests_count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
                                        <i class="fas fa-box mr-1"></i>
                                        {{ $user->equipment_requests_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $user->created_at->format('M d, Y') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-3" onclick="event.stopPropagation();">
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors duration-150" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="text-green-600 hover:text-green-900 transition-colors duration-150" 
                                           title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="text-yellow-600 hover:text-yellow-900 transition-colors duration-150" 
                                                title="Reset Password"
                                                onclick="openResetPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}')">
                                            <i class="fas fa-key"></i>
                                        </button>
                                        <button type="button" 
                                                class="text-red-600 hover:text-red-900 transition-colors duration-150" 
                                                title="Delete User"
                                                data-modal-target="deleteModal{{ $user->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->appends(request()->query())->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="max-w-sm mx-auto">
                    <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
                    <p class="text-gray-500 mb-6">No users match the current filters. Try adjusting your search criteria or add a new user.</p>
                    <div class="flex justify-center space-x-3">
                        <button type="button" 
                                onclick="clearFilters()" 
                                class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 transition-colors duration-200">
                            <i class="fas fa-filter mr-2"></i>
                            Clear Filters
                        </button>
                        <a href="{{ route('admin.users.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Add New User
                        </a>
                    </div>
                </div>
            </div>
        @endif
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

<!-- Delete Confirmation Modals -->
@foreach($users as $user)
    <x-delete-confirmation-modal 
        modal-id="deleteModal{{ $user->id }}"
        title="Delete User"
        message="Are you sure you want to delete {{ $user->name }}? This action cannot be undone and will remove all associated data."
        item-name="{{ $user->name }}"
        delete-route="{{ route('admin.users.destroy', $user) }}" />
@endforeach

@endsection

@push('scripts')
<script>
function applyFilters() {
    document.getElementById('filterForm').submit();
}

function clearFilters() {
    // Clear all form inputs
    document.getElementById('search').value = '';
    document.getElementById('role').value = 'all';
    document.getElementById('department').value = 'all';
    document.getElementById('showOnlyRfid').checked = false;
    document.getElementById('showActiveOnly').checked = false;
    
    // Submit the form to apply cleared filters
    document.getElementById('filterForm').submit();
}

// Real-time search functionality
let searchTimeout;
document.getElementById('search').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        if (e.target.value.length === 0 || e.target.value.length >= 3) {
            applyFilters();
        }
    }, 500);
});

// Apply filters when dropdowns change
document.getElementById('role').addEventListener('change', applyFilters);
document.getElementById('department').addEventListener('change', applyFilters);

// Apply filters when checkboxes change
document.getElementById('showOnlyRfid').addEventListener('change', function() {
    if (this.checked) {
        // Add hidden input to form for RFID filter
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'rfid_only';
        hiddenInput.value = '1';
        hiddenInput.id = 'rfidOnlyInput';
        document.getElementById('filterForm').appendChild(hiddenInput);
    } else {
        // Remove hidden input
        const existingInput = document.getElementById('rfidOnlyInput');
        if (existingInput) {
            existingInput.remove();
        }
    }
    applyFilters();
});

document.getElementById('showActiveOnly').addEventListener('change', function() {
    if (this.checked) {
        // Add hidden input to form for active filter
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'active_only';
        hiddenInput.value = '1';
        hiddenInput.id = 'activeOnlyInput';
        document.getElementById('filterForm').appendChild(hiddenInput);
    } else {
        // Remove hidden input
        const existingInput = document.getElementById('activeOnlyInput');
        if (existingInput) {
            existingInput.remove();
        }
    }
    applyFilters();
});

// Set checkbox states based on URL parameters on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('rfid_only') === '1') {
        document.getElementById('showOnlyRfid').checked = true;
    }
    if (urlParams.get('active_only') === '1') {
        document.getElementById('showActiveOnly').checked = true;
    }

    // Per page functionality
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
