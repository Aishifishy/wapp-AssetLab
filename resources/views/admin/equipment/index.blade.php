@extends('layouts.admin')

@section('title', 'Equipment')

@section('content')
<x-responsive-header title="Equipment Management">
    <x-slot name="actions">
        <x-responsive-button 
            variant="secondary" 
            href="{{ route('admin.equipment.barcode.export') }}"
            icon="print">
            Export Barcodes
        </x-responsive-button>
        <x-responsive-button 
            variant="primary" 
            data-action="open-add-modal"
            icon="plus">
            Add Equipment
        </x-responsive-button>
    </x-slot>
</x-responsive-header>

<div class="space-y-6">
    <!-- Equipment Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-responsive-card title="Available Equipment" class="border-l-4 border-green-500">
            <div class="text-3xl font-bold text-green-600">
                {{ $equipment->where('status', 'available')->count() }}
            </div>
        </x-responsive-card>
        
        <x-responsive-card title="Borrowed Equipment" class="border-l-4 border-yellow-500">
            <div class="text-3xl font-bold text-yellow-600">
                {{ $equipment->where('status', 'borrowed')->count() }}
            </div>
        </x-responsive-card>
        
        <x-responsive-card title="Unavailable Equipment" class="border-l-4 border-red-500">
            <div class="text-3xl font-bold text-red-600">
                {{ $equipment->where('status', 'unavailable')->count() }}
            </div>
        </x-responsive-card>
    </div>

    <!-- Equipment List -->
    <x-responsive-card title="Equipment List">
        <x-slot name="actions">
            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-4 mb-6">
                <div class="flex-1">
                    <input type="text" id="search" placeholder="Search equipment..." 
                        value="{{ request('search') }}"
                        class="input-primary">
                </div>
                <select id="status-filter" class="input-primary">
                    <option value="">All Status</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                    <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                    <option value="unavailable" {{ request('status') === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
            </div>
        </x-slot>

        <x-responsive-table>
            <x-slot name="header">
                <th class="table-header">ID Number</th>
                <th class="table-header">Equipment Type</th>
                <th class="table-header">Barcode</th>
                <th class="table-header">Status</th>
                <th class="table-header">Location</th>
                <th class="table-header">Current Borrower</th>
                <th class="table-header text-right">Actions</th>
            </x-slot>

            @forelse($equipment as $item)
            <tr class="table-row">
                <td class="table-cell">
                    <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                    <div class="text-sm text-gray-500">{{ Str::limit($item->description, 50) }}</div>
                </td>
                <td class="table-cell">
                    {{ $item->category->name }}
                </td>
                <td class="table-cell">
                    {{ $item->barcode ?? ($item->rfid_tag ? $item->rfid_tag . ' (Legacy)' : 'Not Set') }}
                </td>
                <td class="table-cell">
                    <x-status-badge :status="$item->status" type="equipment" />
                </td>
                <td class="table-cell">
                    {{ $item->location ?? 'Not Set' }}
                </td>
                <td class="table-cell">
                    {{ $item->currentBorrower ? $item->currentBorrower->name : 'None' }}
                </td>
                <td class="table-cell text-right">
                    <x-responsive-action-group>
                        @if($item->barcode)
                            <x-responsive-button 
                                variant="info" 
                                size="sm" 
                                href="{{ route('admin.equipment.barcode.single', $item) }}?label_size=standard"
                                target="_blank">
                                <i class="fas fa-print mr-1"></i> Label
                            </x-responsive-button>
                        @endif
                        <x-responsive-button 
                            variant="secondary" 
                            size="sm" 
                            data-action="edit-equipment" 
                            data-equipment-id="{{ $item->id }}">
                            Edit
                        </x-responsive-button>
                        <x-responsive-button 
                            variant="danger" 
                            size="sm" 
                            data-action="delete-equipment" 
                            data-equipment-id="{{ $item->id }}">
                            Delete
                        </x-responsive-button>
                    </x-responsive-action-group>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="table-cell text-center text-gray-500">
                    No equipment found
                </td>
            </tr>
            @endforelse
        </x-responsive-table>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $equipment->links() }}
        </div>
    </x-responsive-card>
</div>

<!-- Add Equipment Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Add New Equipment</h3>
            <form id="addForm" action="{{ route('admin.equipment.store') }}" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">ID Number</label>
                    <input type="text" name="name" id="name" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="category">Equipment Type</label>
                    <input type="text" name="category" id="category" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="barcode">Barcode</label>
                    <div class="flex rounded-md shadow-sm">
                        <input type="text" name="barcode" id="barcode"
                            class="flex-1 shadow appearance-none border rounded-l w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="Leave empty to auto-generate">
                        <button type="button" onclick="generateBarcode('barcode')"
                            class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r bg-gray-50 text-gray-500 text-sm hover:bg-gray-100">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="rfid_tag">RFID Tag (Legacy - Optional)</label>
                    <input type="text" name="rfid_tag" id="rfid_tag"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="RFID tag (optional)">
                    <p class="text-xs text-amber-600 mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        RFID is legacy. Barcode is recommended.
                    </p>
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
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_name">ID Number</label>
                    <input type="text" name="name" id="edit_name" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_description">Description</label>
                    <textarea name="description" id="edit_description" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_category">Equipment Type</label>
                    <input type="text" name="category" id="edit_category" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_barcode">Barcode</label>
                    <div class="flex rounded-md shadow-sm">
                        <input type="text" name="barcode" id="edit_barcode"
                            class="flex-1 shadow appearance-none border rounded-l w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <button type="button" onclick="generateBarcode('edit_barcode')"
                            class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r bg-gray-50 text-gray-500 text-sm hover:bg-gray-100">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_rfid_tag">RFID Tag (Legacy - Optional)</label>
                    <input type="text" name="rfid_tag" id="edit_rfid_tag"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="RFID tag (optional)">
                    <p class="text-xs text-amber-600 mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        RFID is legacy. Barcode is recommended.
                    </p>
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

<script>
function generateBarcode(inputId) {
    const barcode = 'EQP' + String(Math.floor(Math.random() * 900000) + 100000);
    document.getElementById(inputId).value = barcode;
}
</script>

@endsection