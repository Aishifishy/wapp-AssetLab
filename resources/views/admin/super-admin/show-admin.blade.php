@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Admin Details</h1>
                <p class="text-gray-600 mt-1">View administrator information</p>
            </div>
            <div class="flex flex-wrap gap-2 mt-4 sm:mt-0">
                @if($admin->id !== auth()->guard('admin')->id())
                <a href="{{ route('admin.super-admin.admins.edit', $admin) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                @endif
                <a href="{{ route('admin.super-admin.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Admin Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="text-lg font-medium text-gray-900 flex items-center">
                            @if($admin->isSuperAdmin())
                                <i class="fas fa-crown text-yellow-500 mr-2"></i>
                            @endif
                            {{ $admin->name }}
                        </h2>
                        @if($admin->isSuperAdmin())
                            <span class="mt-2 sm:mt-0 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Super Admin</span>
                        @elseif($admin->isAdmin())
                            <span class="mt-2 sm:mt-0 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Admin</span>
                        @else
                            <span class="mt-2 sm:mt-0 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($admin->role) }}</span>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <div class="text-gray-900">{{ $admin->name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <div class="text-gray-900">{{ $admin->email }}</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <div class="text-gray-900">
                                @if($admin->isSuperAdmin())
                                    Super Admin
                                    <div class="text-sm text-gray-500">Full system access including admin management</div>
                                @elseif($admin->isAdmin())
                                    Admin
                                    <div class="text-sm text-gray-500">Access to all modules except super admin functions</div>
                                @else
                                    {{ ucfirst($admin->role) }}
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Created At</label>
                            <div class="text-gray-900">{{ $admin->created_at->format('F d, Y \a\t g:i A') }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Updated</label>
                            <div class="text-gray-900">{{ $admin->updated_at->format('F d, Y \a\t g:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Admin Actions -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Admin Actions</h3>
                </div>
                <div class="p-6">
                    @if($admin->id !== auth()->guard('admin')->id())
                    <div class="space-y-3">
                        <a href="{{ route('admin.super-admin.admins.edit', $admin) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <i class="fas fa-edit mr-2"></i>Edit Admin
                        </a>
                        @if(!($admin->isSuperAdmin() && \App\Models\Radmin::where('is_super_admin', true)->count() <= 1))
                        <form method="POST" action="{{ route('admin.super-admin.admins.destroy', $admin) }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out" 
                                    onclick="confirmDelete('{{ $admin->name }}', this.form)">
                                <i class="fas fa-trash mr-2"></i>Delete Admin
                            </button>
                        </form>
                        @endif
                    </div>
                    @else
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    This is your own account. You cannot modify it from here.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($admin->isSuperAdmin())
            <!-- Super Admin Privileges -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Super Admin Privileges</h3>
                </div>
                <div class="p-6">
                    <ul class="space-y-2">
                        <li class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Manage all administrators</span>
                        </li>
                        <li class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Manage all users</span>
                        </li>
                        <li class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Full system access</span>
                        </li>
                        <li class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Role assignment</span>
                        </li>
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if($admin->id !== auth()->guard('admin')->id())
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
                                This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-auto mr-2">Cancel</button>
                <button onclick="submitDelete()" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-auto">Delete</button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
let currentForm = null;

function confirmDelete(name, form) {
    currentForm = form;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

function submitDelete() {
    if (currentForm) {
        currentForm.submit();
    }
}
</script>
@endpush
