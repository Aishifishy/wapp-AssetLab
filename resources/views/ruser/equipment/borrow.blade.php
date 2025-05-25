@extends('layouts.ruser')

@section('title', 'Borrow Equipment')
@section('header', 'Borrow Equipment')

@section('content')
<div class="space-y-4">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Available Equipment</h2>

            <!-- Filters -->
            <div class="mb-6">
                <div class="flex gap-4">
                    <div class="flex-1">
                        <input type="text" id="search" placeholder="Search equipment..." 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>                    <select id="category-filter" class="px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Equipment Grid -->            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($equipment as $item)
                <div class="bg-white border rounded-lg overflow-hidden hover:shadow-md transition-shadow" data-category-id="{{ $item->category_id }}">
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $item->name }}</h3>
                                <p class="text-sm text-gray-600">
                                    @if(isset($categories))
                                        {{ $categories->firstWhere('id', $item->category_id)->name ?? 'Uncategorized' }}
                                    @endif
                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Available
                            </span>
                        </div>
                        <p class="mt-2 text-sm text-gray-600">{{ Str::limit($item->description, 100) }}</p>
                        <div class="mt-4">
                            <button data-equipment-id="{{ $item->id }}" 
                                    class="borrow-btn btn-primary w-full py-2 rounded-lg">
                                Borrow Equipment
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12 text-gray-500">
                    No equipment available for borrowing at the moment.
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $equipment->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Borrow Modal -->
<div id="borrowModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Borrow Equipment</h3>
            <form id="borrowForm" action="{{ route('ruser.equipment.request') }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="equipment_id" id="equipment_id">
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="purpose">Purpose</label>
                    <textarea name="purpose" id="purpose" required rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="Please describe why you need this equipment..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="requested_from">From Date</label>
                    <input type="datetime-local" name="requested_from" id="requested_from" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="requested_until">Until Date</label>
                    <input type="datetime-local" name="requested_until" id="requested_until" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="flex justify-end mt-6">
                    <button type="button" onclick="closeBorrowModal()" class="btn-secondary mr-2">Cancel</button>
                    <button type="submit" class="btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Add event listeners for borrow buttons and filters
    document.addEventListener('DOMContentLoaded', function() {
        // Borrow button event listeners
        document.querySelectorAll('.borrow-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-equipment-id');
                openBorrowModal(id);
            });
        });
        
        // Search filter
        const searchInput = document.getElementById('search');
        const categoryFilter = document.getElementById('category-filter');
        const equipmentCards = document.querySelectorAll('.grid > div:not(.col-span-full)');
        
        function filterEquipment() {
            const searchTerm = searchInput.value.toLowerCase();
            const categoryId = categoryFilter.value;
            
            equipmentCards.forEach(card => {
                const name = card.querySelector('h3').textContent.toLowerCase();
                const description = card.querySelector('p.text-sm').textContent.toLowerCase();
                const cardCategoryId = card.getAttribute('data-category-id');
                
                const matchesSearch = name.includes(searchTerm) || description.includes(searchTerm);
                const matchesCategory = categoryId === '' || cardCategoryId === categoryId;
                
                card.style.display = (matchesSearch && matchesCategory) ? 'block' : 'none';
            });
        }
        
        searchInput.addEventListener('input', filterEquipment);
        categoryFilter.addEventListener('change', filterEquipment);
    });

    function openBorrowModal(equipmentId) {
        document.getElementById('equipment_id').value = equipmentId;
        document.getElementById('borrowModal').classList.remove('hidden');
        
        // Set minimum dates for the datetime inputs
        const now = new Date();
        const nowString = now.toISOString().slice(0, 16);
        document.getElementById('requested_from').min = nowString;
        document.getElementById('requested_until').min = nowString;
    }

    function closeBorrowModal() {
        document.getElementById('borrowModal').classList.add('hidden');
        document.getElementById('borrowForm').reset();
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('borrowModal');
        if (event.target == modal) {
            closeBorrowModal();
        }
    }

    // Validate dates when they change
    document.getElementById('requested_from').addEventListener('change', validateDates);
    document.getElementById('requested_until').addEventListener('change', validateDates);

    function validateDates() {
        const fromDate = new Date(document.getElementById('requested_from').value);
        const untilDate = new Date(document.getElementById('requested_until').value);
        
        if (fromDate >= untilDate) {
            alert('The return date must be after the borrow date');
            document.getElementById('requested_until').value = '';
        }
    }
</script>
@endpush
@endsection
