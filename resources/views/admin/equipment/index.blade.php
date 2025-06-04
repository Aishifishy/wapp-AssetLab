@extends('layouts.admin')

@section('title', 'Equipment')
@section('header', 'Equipment Management')

@section('content')
<div class="space-y-6">
    <!-- Equipment Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Available Equipment</h3>
            <div class="text-3xl font-bold text-green-600">
                {{ $equipment->where('status', 'available')->count() }}
            </div>
        </div>
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Borrowed Equipment</h3>
            <div class="text-3xl font-bold text-yellow-600">
                {{ $equipment->where('status', 'borrowed')->count() }}
            </div>
        </div>
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Unavailable Equipment</h3>
            <div class="text-3xl font-bold text-red-600">
                {{ $equipment->where('status', 'unavailable')->count() }}
            </div>
        </div>
    </div>

    <!-- Equipment List -->
    <div class="bg-white shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Equipment List</h2>
                <button data-action="open-add-modal" class="btn-primary px-4 py-2 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Equipment
                </button>
            </div>

            <!-- Filters -->
            <div class="mb-6">
                <div class="flex gap-4">
                    <div class="flex-1">
                        <input type="text" id="search" placeholder="Search equipment..." 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>                    <select id="status-filter" class="px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                        <option value="unavailable" {{ request('status') === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                </div>
            </div>

            <!-- Equipment Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RFID Tag</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Borrower</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($equipment as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($item->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->category->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->rfid_tag ?? 'Not Set' }}
                            </td>                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-status-badge :status="$item->status" type="equipment" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->location ?? 'Not Set' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->currentBorrower ? $item->currentBorrower->name : 'None' }}
                            </td>                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button data-action="edit-equipment" data-equipment-id="{{ $item->id }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                <button data-action="delete-equipment" data-equipment-id="{{ $item->id }}" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No equipment found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $equipment->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Equipment Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Add New Equipment</h3>
            <form id="addForm" action="{{ route('admin.equipment.store') }}" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name</label>
                    <input type="text" name="name" id="name" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="category">Category</label>
                    <input type="text" name="category" id="category" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="rfid_tag">RFID Tag</label>
                    <input type="text" name="rfid_tag" id="rfid_tag"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="location">Location</label>
                    <input type="text" name="location" id="location"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>                <div class="flex justify-end mt-6">
                    <button type="button" data-action="close-modal" data-target="addModal" class="btn-secondary mr-2">Cancel</button>
                    <button type="submit" class="btn-primary">Add Equipment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Equipment Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Edit Equipment</h3>
            <form id="editForm" method="POST" class="mt-4">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_name">Name</label>
                    <input type="text" name="name" id="edit_name" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_description">Description</label>
                    <textarea name="description" id="edit_description" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_category">Category</label>
                    <input type="text" name="category" id="edit_category" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_rfid_tag">RFID Tag</label>
                    <input type="text" name="rfid_tag" id="edit_rfid_tag"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_location">Location</label>
                    <input type="text" name="location" id="edit_location"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_status">Status</label>
                    <select name="status" id="edit_status" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="available">Available</option>
                        <option value="borrowed">Borrowed</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>                <div class="flex justify-end mt-6">
                    <button type="button" data-action="close-modal" data-target="editModal" class="btn-secondary mr-2">Cancel</button>
                    <button type="submit" class="btn-primary">Update Equipment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection