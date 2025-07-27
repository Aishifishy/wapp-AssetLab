@extends('layouts.admin')

@section('title', 'Add New User')
@section('header', 'Add New User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Create New User</h2>
                <a href="{{ route('admin.users.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Users
                </a>
            </div>
        </div>
        
        <div class="p-6">
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <x-form-error field="name" />
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <x-form-error field="email" />
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" id="role" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Select a role</option>
                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="faculty" {{ old('role') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                    <x-form-error field="role" />
                </div>

                <!-- Department -->
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                    <input type="text" name="department" id="department" value="{{ old('department') }}" required
                           placeholder="e.g., Computer Science, Mathematics, Physics"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <x-form-error field="department" />
                </div>

                <!-- Contact Number -->
                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number') }}"
                           placeholder="Optional contact number"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <x-form-error field="contact_number" />
                </div>

                <!-- RFID Tag -->
                <div>
                    <label for="rfid_tag" class="block text-sm font-medium text-gray-700">RFID Tag</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <input type="text" name="rfid_tag" id="rfid_tag" value="{{ old('rfid_tag') }}"
                               placeholder="Scan or enter RFID tag number"
                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <button type="button" id="scanRfidBtn"
                                class="ml-3 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-id-card mr-2"></i> Scan RFID
                        </button>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Optional. This will enable quick identification for onsite equipment borrowing.</p>
                    <x-form-error field="rfid_tag" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <x-form-error field="password" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <x-form-error field="password_confirmation" />
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
                        <i class="fas fa-save mr-2"></i> Create User
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
</script>
@endpush
@endsection
