@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Computer Laboratories</h1>
        <a href="{{ route('admin.laboratory.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-plus mr-2"></i> Add New Laboratory
        </a>
    </div>

    <x-flash-messages />

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-table text-gray-500 mr-2"></i>
                    <h3 class="text-lg font-medium text-gray-900">Laboratory List ({{ $laboratories->total() }} total)</h3>
                </div>
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
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="laboratoriesTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Computers</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($laboratories as $lab)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $lab->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $lab->building }} - {{ $lab->room_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $lab->capacity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $lab->number_of_computers }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center">
                                        <x-status-badge :status="$lab->status" type="laboratory" />
                                        <button type="button" class="ml-2 text-blue-600 hover:text-blue-900" data-modal-target="statusModal{{ $lab->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.laboratory.edit', $lab) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <button type="button" 
                                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200"
                                                data-modal-target="deleteModal{{ $lab->id }}">
                                            <i class="fas fa-trash mr-1"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Status Update Modal -->
                            <div id="statusModal{{ $lab->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <form action="{{ route('admin.laboratory.update-status', $lab) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="sm:flex sm:items-start">
                                                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                            Update Status - {{ $lab->name }}
                                                        </h3>
                                                        <div class="mt-4">
                                                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                                            <select name="status" id="status" class="mt-1 block w-full px-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                                                <option value="available" {{ $lab->status === 'available' ? 'selected' : '' }}>Available</option>
                                                                <option value="in_use" {{ $lab->status === 'in_use' ? 'selected' : '' }}>In Use</option>
                                                                <option value="under_maintenance" {{ $lab->status === 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                                                <option value="reserved" {{ $lab->status === 'reserved' ? 'selected' : '' }}>Reserved</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Update Status
                                                </button>
                                                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" data-modal-close>
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Confirmation Modal -->
                            <x-delete-confirmation-modal 
                                modal-id="deleteModal{{ $lab->id }}"
                                title="Delete Laboratory" 
                                message="Are you sure you want to delete this laboratory? This action cannot be undone."
                                item-name="{{ $lab->name }}"
                                delete-route="{{ route('admin.laboratory.destroy', $lab) }}" />
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $laboratories->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
</script>
@endsection