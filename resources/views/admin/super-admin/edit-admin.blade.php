@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Edit Admin</h1>
                <p class="text-gray-600 mt-1">Update administrator information</p>
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
                    <h2 class="text-lg font-medium text-gray-900 flex items-center">
                        @if($admin->isSuperAdmin())
                            <i class="fas fa-crown text-yellow-500 mr-2"></i>
                        @endif
                        Edit {{ $admin->name }}
                    </h2>
                    @if($admin->isSuperAdmin())
                        <span class="mt-2 sm:mt-0 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Super Admin</span>
                    @elseif($admin->isAdmin())
                        <span class="mt-2 sm:mt-0 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Admin</span>
                    @endif
                </div>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.super-admin.admins.update', $admin) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('name') border-red-500 @enderror" 
                                   id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('email') border-red-500 @enderror" 
                                   id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('password') border-red-500 @enderror" 
                                   id="password" name="password">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Leave blank to keep current password</p>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input type="password" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" 
                                   id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Admin Role</label>
                        <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('role') border-red-500 @enderror" 
                                id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="super_admin" {{ old('role', $admin->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="admin" {{ old('role', $admin->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 text-sm text-gray-500">
                            <div class="space-y-1">
                                <div><span class="font-medium">Super Admin:</span> Full system access including admin management</div>
                                <div><span class="font-medium">Admin:</span> Access to all modules except super admin functions</div>
                            </div>
                        </div>
                    </div>

                    @if($admin->id === auth()->guard('admin')->id())
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Note:</strong> You cannot edit your own account from this interface for security reasons.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-6">
                        <a href="{{ route('admin.super-admin.index') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            Cancel
                        </a>
                        @if($admin->id !== auth()->guard('admin')->id())
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <i class="fas fa-save mr-2"></i>Update Admin
                        </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        
        @if($admin->id !== auth()->guard('admin')->id())
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-red-600">Danger Zone</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Once you delete this admin account, there is no going back. Please be certain.</p>
                @if($admin->isSuperAdmin() && \App\Models\Radmin::where('is_super_admin', true)->count() <= 1)
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shield-alt text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                This is the last super admin account and cannot be deleted.
                            </p>
                        </div>
                    </div>
                </div>
                @else
                <button type="button" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out" 
                        onclick="confirmDelete()">
                    <i class="fas fa-trash mr-2"></i>Delete Admin Account
                </button>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if($admin->id !== auth()->guard('admin')->id() && !($admin->isSuperAdmin() && \App\Models\Radmin::where('is_super_admin', true)->count() <= 1))
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900">Confirm Delete</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete admin <strong>{{ $admin->name }}</strong>?
                </p>
                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                This action cannot be undone. The admin will lose access immediately.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-auto mr-2">Cancel</button>
                <form action="{{ route('admin.super-admin.admins.destroy', $admin) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-auto">Delete Admin</button>
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
