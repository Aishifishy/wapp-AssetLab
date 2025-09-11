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
            <form action="{{ route('admin.users.store') }}" method="POST" class="form-stack">
                @csrf
                
                <!-- Name -->
                <div class="form-group">
                    <label for="name" class="form-label form-label-required">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="form-input">
                    <x-form-error field="name" />
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label form-label-required">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="form-input">
                    <x-form-error field="email" />
                </div>

                <!-- Role -->
                <div class="form-group">
                    <label for="role" class="form-label form-label-required">Role</label>
                    <select name="role" id="role" required
                            class="form-select">
                        <option value="">Select a role</option>
                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="faculty" {{ old('role') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                    <x-form-error field="role" />
                </div>

                <!-- Department -->
                <div class="form-group">
                    <label for="department" class="form-label form-label-required">Department</label>
                    <input type="text" name="department" id="department" value="{{ old('department') }}" required
                           placeholder="e.g., Computer Science, Mathematics, Physics"
                           class="form-input">
                    <x-form-error field="department" />
                </div>

                <!-- Contact Number -->
                <div class="form-group">
                    <label for="contact_number" class="form-label-optional">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number') }}"
                           placeholder="Optional contact number"
                           class="form-input">
                    <x-form-error field="contact_number" />
                </div>

                <!-- RFID Tag -->
                <div class="form-group">
                    <label for="rfid_tag" class="form-label-optional">RFID Tag</label>
                    <div class="form-input-group">
                        <input type="text" name="rfid_tag" id="rfid_tag" value="{{ old('rfid_tag') }}"
                               placeholder="Scan or enter RFID tag number"
                               class="form-input-group-input">
                        <button type="button" id="scanRfidBtn"
                                class="form-input-group-button">
                            <i class="fas fa-id-card mr-2"></i> Scan RFID
                        </button>
                    </div>
                    <p class="form-help">Optional. This will enable quick identification for onsite equipment borrowing.</p>
                    <x-form-error field="rfid_tag" />
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label form-label-required">Password</label>
                    <input type="password" name="password" id="password" required
                           class="form-input">
                    <x-form-error field="password" />
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label form-label-required">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="form-input">
                    <x-form-error field="password_confirmation" />
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <button type="button" 
                            onclick="window.history.back()"
                            class="btn-outline">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="btn-primary">
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
