@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Super Admin - User Management</h1>
                <p class="text-gray-600 mt-1">Manage system administrators and users</p>
            </div>
            <div class="flex flex-wrap gap-2 mt-4 sm:mt-0">
                <a href="{{ route('admin.super-admin.reports') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-chart-bar mr-1"></i>Reports
                </a>
                <a href="{{ route('admin.super-admin.export.users') }}" class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-download mr-1"></i>Export
                </a>
                <a href="{{ route('admin.super-admin.admins.create') }}" class="inline-flex items-center px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-plus mr-1"></i>Add Admin
                </a>
                <a href="{{ route('admin.super-admin.users.create') }}" class="inline-flex items-center px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-plus mr-1"></i>Add User
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-blue-50 rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-blue-900">Total Admins</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_admins'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-shield text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-green-50 rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-green-900">Total Users</h3>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['total_users'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-yellow-900">Super Admins</h3>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['super_admins'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-crown text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-indigo-50 rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-indigo-900">Faculty Members</h3>
                    <p class="text-3xl font-bold text-indigo-600">{{ $stats['faculty_users'] }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-indigo-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Admins Management -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-medium text-gray-900">Admin Accounts</h2>
            <a href="{{ route('admin.super-admin.admins.create') }}" class="mt-2 sm:mt-0 inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                <i class="fas fa-plus mr-2"></i>Add New Admin
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($admins as $admin)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($admin->isSuperAdmin())
                                    <i class="fas fa-crown text-yellow-500 mr-2" title="Super Admin"></i>
                                @endif
                                <div class="text-sm font-medium text-gray-900">{{ $admin->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($admin->isSuperAdmin())
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Super Admin</span>
                            @elseif($admin->isAdmin())
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Admin</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($admin->role) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.super-admin.admins.show', $admin) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($admin->id !== auth()->guard('admin')->id())
                                <a href="{{ route('admin.super-admin.admins.edit', $admin) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!($admin->isSuperAdmin() && \App\Models\Radmin::where('is_super_admin', true)->count() <= 1))
                                <form method="POST" action="{{ route('admin.super-admin.admins.destroy', $admin) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="text-red-600 hover:text-red-900" onclick="confirmDeleteAdmin('{{ $admin->name }}', this.form)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-user-shield text-4xl mb-3"></i>
                                <p>No admin accounts found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Users Management -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-medium text-gray-900">User Accounts</h2>
            <div class="flex flex-wrap gap-2 mt-2 sm:mt-0">
                <button type="button" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out" id="bulkDeleteBtn" style="display: none;" onclick="confirmBulkDelete()">
                    <i class="fas fa-trash mr-2"></i>Delete Selected
                </button>
                <a href="{{ route('admin.super-admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-plus mr-2"></i>Add New User
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <form id="bulkForm" action="{{ route('admin.super-admin.bulk-delete-users') }}" method="POST">
                @csrf
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 user-checkbox">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->isFaculty())
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Faculty</span>
                                @elseif($user->isStudent())
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Student</span>
                                @elseif($user->isStaff())
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Staff</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($user->role ?? 'User') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->department ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.super-admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.super-admin.users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.super-admin.users.destroy', $user) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-red-600 hover:text-red-900" onclick="confirmDeleteUser('{{ $user->name }}', this.form)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-users text-4xl mb-3"></i>
                                    <p>No user accounts found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div id="bulkDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900">Confirm Bulk Delete</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete <strong id="selectedCount">0</strong> selected users?
                </p>
                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                This action cannot be undone. Users with pending requests or reservations will be skipped.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeBulkModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-auto mr-2">Cancel</button>
                <button onclick="submitBulkDelete()" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-auto">Delete Selected</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900">Confirm Delete</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete <strong id="deleteName"></strong>?
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

@endsection

@push('scripts')
<script>
// Bulk operations
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    toggleBulkActions();
});

document.querySelectorAll('.user-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        toggleBulkActions();
        
        // Update select all checkbox
        const total = document.querySelectorAll('.user-checkbox').length;
        const checked = document.querySelectorAll('.user-checkbox:checked').length;
        const selectAll = document.getElementById('selectAll');
        
        if (checked === 0) {
            selectAll.indeterminate = false;
            selectAll.checked = false;
        } else if (checked === total) {
            selectAll.indeterminate = false;
            selectAll.checked = true;
        } else {
            selectAll.indeterminate = true;
        }
    });
});

function toggleBulkActions() {
    const checked = document.querySelectorAll('.user-checkbox:checked').length;
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    if (checked > 0) {
        bulkDeleteBtn.style.display = 'inline-flex';
    } else {
        bulkDeleteBtn.style.display = 'none';
    }
}

function confirmBulkDelete() {
    const checked = document.querySelectorAll('.user-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = checked;
    document.getElementById('bulkDeleteModal').classList.remove('hidden');
}

function closeBulkModal() {
    document.getElementById('bulkDeleteModal').classList.add('hidden');
}

function submitBulkDelete() {
    document.getElementById('bulkForm').submit();
}

let currentForm = null;

function confirmDeleteAdmin(name, form) {
    document.getElementById('deleteName').textContent = name;
    currentForm = form;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function confirmDeleteUser(name, form) {
    document.getElementById('deleteName').textContent = name;
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
