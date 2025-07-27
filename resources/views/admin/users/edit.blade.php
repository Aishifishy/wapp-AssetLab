@extends('layouts.admin')

@section('title', 'Edit User')
@section('header', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Edit User: {{ $user->name }}</h2>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.users.show', $user) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i> Back to User
                    </a>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <x-form-error field="name" />
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <x-form-error field="email" />
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" id="role" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Select a role</option>
                        <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="faculty" {{ old('role', $user->role) == 'faculty' ? 'selected' : '' }}>Faculty</option>
                        <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                    <x-form-error field="role" />
                </div>

                <!-- Department -->
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                    <input type="text" name="department" id="department" value="{{ old('department', $user->department) }}" required
                           placeholder="e.g., Computer Science, Mathematics, Physics"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <x-form-error field="department" />
                </div>

                <!-- Contact Number -->
                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number', $user->contact_number) }}"
                           placeholder="Optional contact number"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <x-form-error field="contact_number" />
                </div>

                <!-- RFID Tag -->
                <div>
                    <label for="rfid_tag" class="block text-sm font-medium text-gray-700">RFID Tag</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <input type="text" name="rfid_tag" id="rfid_tag" value="{{ old('rfid_tag', $user->rfid_tag) }}"
                               placeholder="Scan or enter RFID tag number"
                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <button type="button" id="scanRfidBtn"
                                class="ml-3 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-id-card mr-2"></i> Scan RFID
                        </button>
                        @if($user->rfid_tag)
                            <button type="button" id="clearRfidBtn"
                                    class="ml-2 inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-times mr-2"></i> Clear
                            </button>
                        @endif
                    </div>
                    <p class="mt-1 text-sm text-gray-500">This enables quick identification for onsite equipment borrowing.</p>
                    <x-form-error field="rfid_tag" />
                </div>

                <!-- Password Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Password Management</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>To change the user's password, use the "Reset Password" button from the user details page.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-6">
                    <button type="button" 
                            onclick="window.history.back()"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('scanRfidBtn').addEventListener('click', function() {
    const rfidInput = document.getElementById('rfid_tag');
    const btn = this;
    
    // Change button state to indicate scanning
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Listening for RFID...';
    btn.disabled = true;
    
    // Focus on the RFID input to capture the scan
    rfidInput.focus();
    rfidInput.placeholder = 'Please scan RFID card...';
    
    // Reset button after 10 seconds if no input
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-id-card mr-2"></i> Scan RFID';
        btn.disabled = false;
        rfidInput.placeholder = 'Scan or enter RFID tag number';
    }, 10000);
    
    // Listen for input (RFID scanners typically input data quickly)
    const handleInput = () => {
        if (rfidInput.value.length > 0) {
            btn.innerHTML = '<i class="fas fa-check mr-2"></i> RFID Captured';
            btn.disabled = false;
            rfidInput.placeholder = 'RFID tag captured';
            rfidInput.removeEventListener('input', handleInput);
        }
    };
    
    rfidInput.addEventListener('input', handleInput);
});

// Clear RFID functionality
const clearBtn = document.getElementById('clearRfidBtn');
if (clearBtn) {
    clearBtn.addEventListener('click', function() {
        const rfidInput = document.getElementById('rfid_tag');
        rfidInput.value = '';
        rfidInput.focus();
    });
}
</script>
@endpush
@endsection
