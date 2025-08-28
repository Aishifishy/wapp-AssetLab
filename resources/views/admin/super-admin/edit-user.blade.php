@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Edit User</h1>
                <p class="text-gray-600 mt-1">Update user information</p>
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
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-lg font-medium text-gray-900">Edit {{ $user->name }}</h2>
                    @if($user->isFaculty())
                        <span class="mt-2 sm:mt-0 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Faculty</span>
                    @elseif($user->isStudent())
                        <span class="mt-2 sm:mt-0 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Student</span>
                    @elseif($user->isStaff())
                        <span class="mt-2 sm:mt-0 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Staff</span>
                    @else
                        <span class="mt-2 sm:mt-0 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($user->role ?? 'User') }}</span>
                    @endif
                </div>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.super-admin.users.update', $user) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('name') border-red-500 @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('email') border-red-500 @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('password') border-red-500 @enderror" 
                                   id="password" name="password">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Leave blank to keep current password</p>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input type="password" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" 
                                   id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">User Role</label>
                            <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('role') border-red-500 @enderror" 
                                    id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
                                <option value="faculty" {{ old('role', $user->role) == 'faculty' ? 'selected' : '' }}>Faculty</option>
                                <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <input type="text" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('department') border-red-500 @enderror" 
                                   id="department" name="department" value="{{ old('department', $user->department) }}">
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
                                   id="contact_number" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}">
                            @error('contact_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="rfid_tag" class="block text-sm font-medium text-gray-700 mb-2">RFID Tag</label>
                            <input type="text" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('rfid_tag') border-red-500 @enderror" 
                                   id="rfid_tag" name="rfid_tag" value="{{ old('rfid_tag', $user->rfid_tag) }}">
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
                            <i class="fas fa-save mr-2"></i>Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- User Activity Summary -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">User Activity Summary</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $user->equipmentRequests()->count() }}</div>
                        <div class="text-sm text-gray-500 mt-1">Total Equipment Requests</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600">{{ $user->equipmentRequests()->where('status', 'pending')->count() }}</div>
                        <div class="text-sm text-gray-500 mt-1">Pending Requests</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $user->laboratoryReservations()->count() }}</div>
                        <div class="text-sm text-gray-500 mt-1">Lab Reservations</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-indigo-600">{{ $user->laboratoryReservations()->where('status', 'pending')->count() }}</div>
                        <div class="text-sm text-gray-500 mt-1">Pending Reservations</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Danger Zone -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-red-600">Danger Zone</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Once you delete this user account, there is no going back. Please be certain.</p>
                @if($user->equipmentRequests()->where('status', 'pending')->count() > 0 || $user->laboratoryReservations()->where('status', 'pending')->count() > 0)
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                This user has pending requests or reservations and cannot be deleted.
                            </p>
                        </div>
                    </div>
                </div>
                @else
                <button type="button" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out" 
                        onclick="confirmDelete()">
                    <i class="fas fa-trash mr-2"></i>Delete User Account
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if($user->equipmentRequests()->where('status', 'pending')->count() === 0 && $user->laboratoryReservations()->where('status', 'pending')->count() === 0)
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900">Confirm Delete</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete user <strong>{{ $user->name }}</strong>?
                </p>
                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                This action cannot be undone. The user will lose access immediately and all their data will be permanently removed.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-auto mr-2">Cancel</button>
                <form action="{{ route('admin.super-admin.users.destroy', $user) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-auto">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function confirmDelete() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>
@endpush
