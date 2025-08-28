@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">User Details</h1>
                <p class="text-gray-600 mt-1">View user information and activity</p>
            </div>
            <div class="flex flex-wrap gap-2 mt-4 sm:mt-0">
                <a href="{{ route('admin.super-admin.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('admin.super-admin.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main User Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="text-lg font-medium text-gray-900">{{ $user->name }}</h2>
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <div class="text-gray-900">{{ $user->name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <div class="text-gray-900">{{ $user->email }}</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <div class="text-gray-900">{{ ucfirst($user->role ?? 'User') }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <div class="text-gray-900">{{ $user->department ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                            <div class="text-gray-900">{{ $user->contact_number ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">RFID Tag</label>
                            <div>
                                @if($user->rfid_tag)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ $user->rfid_tag }}</span>
                                @else
                                    <span class="text-gray-500">Not assigned</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Created At</label>
                            <div class="text-gray-900">{{ $user->created_at->format('F d, Y \a\t g:i A') }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Updated</label>
                            <div class="text-gray-900">{{ $user->updated_at->format('F d, Y \a\t g:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipment Requests -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Equipment Requests ({{ $user->equipmentRequests->count() }})</h3>
                </div>
                <div class="overflow-x-auto">
                    @if($user->equipmentRequests->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested At</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($user->equipmentRequests->take(5) as $request)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->equipment->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($request->status === 'pending')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @elseif($request->status === 'approved')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                                    @elseif($request->status === 'rejected')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                    @elseif($request->status === 'borrowed')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Borrowed</span>
                                    @elseif($request->status === 'returned')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Returned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($user->equipmentRequests->count() > 5)
                    <div class="px-6 py-3 text-center text-sm text-gray-500 bg-gray-50">
                        Showing 5 of {{ $user->equipmentRequests->count() }} requests
                    </div>
                    @endif
                    @else
                    <div class="px-6 py-8 text-center text-gray-500">
                        No equipment requests found.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Laboratory Reservations -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Laboratory Reservations ({{ $user->laboratoryReservations->count() }})</h3>
                </div>
                <div class="overflow-x-auto">
                    @if($user->laboratoryReservations->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reserved At</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($user->laboratoryReservations->take(5) as $reservation)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reservation->laboratory->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($reservation->status === 'pending')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @elseif($reservation->status === 'approved')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                                    @elseif($reservation->status === 'rejected')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                    @elseif($reservation->status === 'completed')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Completed</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reservation->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($user->laboratoryReservations->count() > 5)
                    <div class="px-6 py-3 text-center text-sm text-gray-500 bg-gray-50">
                        Showing 5 of {{ $user->laboratoryReservations->count() }} reservations
                    </div>
                    @endif
                    @else
                    <div class="px-6 py-8 text-center text-gray-500">
                        No laboratory reservations found.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- User Actions -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">User Actions</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <a href="{{ route('admin.super-admin.users.edit', $user) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <i class="fas fa-edit mr-2"></i>Edit User
                        </a>
                        <form method="POST" action="{{ route('admin.super-admin.users.destroy', $user) }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out" 
                                    onclick="confirmDelete('{{ $user->name }}', this.form)">
                                <i class="fas fa-trash mr-2"></i>Delete User
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Activity Summary -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Activity Summary</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ $user->equipmentRequests->count() }}</div>
                            <div class="text-sm text-gray-500 mt-1">Equipment Requests</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">{{ $user->laboratoryReservations->count() }}</div>
                            <div class="text-sm text-gray-500 mt-1">Lab Reservations</div>
                        </div>
                    </div>
                </div>
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
                    Are you sure you want to delete user <strong>{{ $user->name }}</strong>?
                </p>
                @if($user->equipmentRequests()->where('status', 'pending')->count() > 0 || $user->laboratoryReservations()->where('status', 'pending')->count() > 0)
                <div class="mt-4 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                This user has pending requests or reservations and cannot be deleted.
                            </p>
                        </div>
                    </div>
                </div>
                @else
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
                @endif
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-auto mr-2">Cancel</button>
                @if($user->equipmentRequests()->where('status', 'pending')->count() === 0 && $user->laboratoryReservations()->where('status', 'pending')->count() === 0)
                <button onclick="submitDelete()" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-auto">Delete</button>
                @endif
            </div>
        </div>
    </div>
</div>

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
