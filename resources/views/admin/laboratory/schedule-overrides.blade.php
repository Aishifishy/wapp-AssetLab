@extends('layouts.admin')

@section('title', 'Schedule Overrides')
@section('header', 'Schedule Overrides')

@section('content')
<div class="p-6">
    <x-flash-messages />

    <!-- Navigation Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Laboratory Management</h1>
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 mt-4">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('admin.laboratory.reservations') }}" 
                       class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-calendar-check mr-2"></i>
                        Reservations
                    </a>
                    <span class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Schedule Overrides
                    </span>
                </nav>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.laboratory.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <i class="fas fa-arrow-left mr-2"></i> Back to Laboratories
            </a>
            <a href="{{ route('admin.laboratory.create-override') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i>
                Create Override
            </a>
        </div>
    </div>

    <!-- Schedule Overrides List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Schedule Overrides</h2>
                    @if(request('sort'))
                        <p class="text-sm text-gray-500 mt-1">
                            Sorted by {{ ucfirst(str_replace('_', ' ', request('sort'))) }}
                            ({{ request('direction') == 'asc' ? 'Ascending' : 'Descending' }})
                        </p>
                    @endif
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Per Page Selector -->
                    <div class="flex items-center space-x-2">
                        <label for="perPageSelect" class="text-sm text-gray-600">Show:</label>
                        <select id="perPageSelect" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 w-20">
                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-sm text-gray-600">per page</span>
                    </div>
                    <!-- Search Function -->
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search overrides..."
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm w-80">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6">
            @if($overrides->count() > 0)
                <div class="overflow-x-auto">
                    <table id="overridesTable" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" data-sort="id">
                                    <div class="flex items-center">
                                        ID
                                        <i class="fas fa-sort ml-2 text-gray-400"></i>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" data-sort="laboratory">
                                    <div class="flex items-center">
                                        Laboratory
                                        <i class="fas fa-sort ml-2 text-gray-400"></i>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" data-sort="override_date">
                                    <div class="flex items-center">
                                        Override Date
                                        <i class="fas fa-sort ml-2 text-gray-400"></i>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" data-sort="override_type">
                                    <div class="flex items-center">
                                        Type
                                        <i class="fas fa-sort ml-2 text-gray-400"></i>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule Details</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($overrides as $override)
                                <tr class="override-row" 
                                    data-override-id="{{ $override->id }}"
                                    data-laboratory="{{ strtolower($override->laboratory->name) }}" 
                                    data-override-type="{{ $override->override_type }}"
                                    data-status="{{ $override->isCurrentlyActive() ? 'active' : 'inactive' }}"
                                    data-override-date="{{ $override->override_date->format('Y-m-d') }}"
                                    data-search="{{ strtolower($override->laboratory->name . ' ' . $override->override_type . ' ' . ($override->originalSchedule ? $override->originalSchedule->subject_name : '') . ' ' . ($override->createdBy ? $override->createdBy->name : '')) }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">{{ $override->id }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $override->laboratory->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $override->laboratory->building }}, Room {{ $override->laboratory->room_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $override->override_date->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $override->override_date->format('l') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-status-badge :status="$override->override_type" type="override" />
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($override->override_type === 'cancel')
                                            <div class="text-sm text-red-600 font-medium">Class Cancelled</div>
                                            @if($override->originalSchedule)
                                                <div class="text-sm text-gray-500">Original: {{ $override->originalSchedule->subject_code }}</div>
                                            @endif
                                        @else
                                            @php $effectiveSchedule = $override->getEffectiveSchedule(); @endphp
                                            @if($effectiveSchedule)
                                                <div class="text-sm font-medium text-gray-900">{{ $effectiveSchedule['subject_code'] ?? 'N/A' }} - {{ $effectiveSchedule['subject_name'] ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ $effectiveSchedule['instructor_name'] ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ $override->time_range ?? 'N/A' }}</div>
                                            @endif
                                        @endif
                                        @if($override->reason)
                                            <div class="text-xs text-gray-400 mt-1" title="{{ $override->reason }}">{{ Str::limit($override->reason, 30) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($override->isCurrentlyActive())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $override->createdBy->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $override->created_at->format('M d, Y H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" onclick="showOverrideDetails({{ $override->id }})" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        
                                        @if($override->isCurrentlyActive())
                                            <a href="#" onclick="openDeactivateModal({{ $override->id }})" class="text-red-600 hover:text-red-900">Deactivate</a>
                                        @else
                                            <span class="text-gray-400">No actions</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Deactivate Confirmation Modals for each active override -->
                @foreach($overrides as $override)
                    @if($override->isCurrentlyActive())
                        <x-delete-confirmation-modal 
                            modal-id="deactivateModal{{ $override->id }}"
                            title="Deactivate Override"
                            message="Are you sure you want to deactivate this schedule override? This action cannot be undone."
                            item-name="Override #{{ $override->id }}"
                            delete-route="{{ route('admin.laboratory.deactivate-override', $override) }}" />
                    @endif
                @endforeach
                
                <div class="mt-4">
                    {{ $overrides->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-exclamation-triangle text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Schedule Overrides Found</h3>
                    <p class="text-gray-500 mb-6">
                        You haven't created any schedule overrides yet.
                    </p>
                    <div class="flex justify-center space-x-3">
                        <a href="{{ route('admin.laboratory.create-override') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>
                            Create Override
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Override Details Modal -->
<div id="overrideDetailsModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Override Details</h3>
                <button type="button" onclick="closeOverrideDetails()" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="overrideDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Deactivation Modal -->
<x-rejection-modal 
    title="Deactivate Override"
    modal-id="deactivation-modal"
    form-id="deactivate-form"
    reason-field-id="deactivation_reason"
    reason-label="Deactivation Reason"
    submit-text="Deactivate Override"
    cancel-text="Cancel" />

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('overridesTable');
    const rows = table.querySelectorAll('.override-row');

    let sortDirection = {};
    let currentSort = null;

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

    // Sorting functionality
    table.querySelectorAll('th[data-sort]').forEach(header => {
        header.addEventListener('click', function() {
            const sortKey = this.dataset.sort;
            sortTable(sortKey);
        });
    });

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();

        rows.forEach(row => {
            const searchData = row.dataset.search;

            const matchesSearch = searchData.includes(searchTerm);

            if (matchesSearch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function sortTable(sortKey) {
        // Toggle sort direction
        if (currentSort === sortKey) {
            sortDirection[sortKey] = sortDirection[sortKey] === 'asc' ? 'desc' : 'asc';
        } else {
            sortDirection[sortKey] = 'asc';
            currentSort = sortKey;
        }

        // Update sort icons
        table.querySelectorAll('th[data-sort] i').forEach(icon => {
            icon.className = 'fas fa-sort ml-2 text-gray-400';
        });

        const currentHeader = table.querySelector(`th[data-sort="${sortKey}"] i`);
        if (sortDirection[sortKey] === 'asc') {
            currentHeader.className = 'fas fa-sort-up ml-2 text-gray-600';
        } else {
            currentHeader.className = 'fas fa-sort-down ml-2 text-gray-600';
        }

        // Convert NodeList to Array and sort
        const rowsArray = Array.from(rows);
        const tbody = table.querySelector('tbody');

        rowsArray.sort((a, b) => {
            let aVal, bVal;

            switch(sortKey) {
                case 'laboratory':
                    aVal = a.dataset.laboratory;
                    bVal = b.dataset.laboratory;
                    break;
                case 'override_type':
                    aVal = a.dataset.overrideType;
                    bVal = b.dataset.overrideType;
                    break;
                case 'override_date':
                    aVal = a.dataset.overrideDate || '0000-00-00';
                    bVal = b.dataset.overrideDate || '0000-00-00';
                    break;
                case 'id':
                    aVal = parseInt(a.dataset.overrideId);
                    bVal = parseInt(b.dataset.overrideId);
                    break;
                default:
                    return 0;
            }

            if (sortDirection[sortKey] === 'asc') {
                return aVal < bVal ? -1 : aVal > bVal ? 1 : 0;
            } else {
                return aVal > bVal ? -1 : aVal < bVal ? 1 : 0;
            }
        });

        // Remove all rows and re-append in sorted order
        rowsArray.forEach(row => {
            tbody.removeChild(row);
        });

        rowsArray.forEach(row => {
            tbody.appendChild(row);
        });
    }

    // Override details functionality
    window.showOverrideDetails = function(overrideId) {
        const modal = document.getElementById('overrideDetailsModal');
        const content = document.getElementById('overrideDetailsContent');

        // Show loading state
        content.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-gray-400"></i> Loading...</div>';
        modal.classList.remove('hidden');

        // In a real implementation, you would fetch the details via AJAX
        // For now, we'll show basic information
        content.innerHTML = `
            <div class="space-y-4">
                <p class="text-gray-600">Override details for ID: ${overrideId}</p>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500">Use this modal to display detailed information about the override, including reason, requestor, affected schedules, etc.</p>
                </div>
            </div>
        `;
    };

    window.closeOverrideDetails = function() {
        document.getElementById('overrideDetailsModal').classList.add('hidden');
    };

    // Deactivation modal functionality
    window.openDeactivateModal = function(overrideId) {
        const modal = document.getElementById('deactivation-modal');
        const form = document.getElementById('deactivate-form');

        // Set the form action URL
        form.action = `/admin/laboratory/schedule-overrides/${overrideId}/deactivate`;
        modal.classList.remove('hidden');
    };

    window.closeRejectModal = function() {
        document.getElementById('deactivation-modal').classList.add('hidden');
    };

    // Close modal handlers
    document.addEventListener('click', function(e) {
        if (e.target.dataset.action === 'close-reject-modal') {
            closeRejectModal();
        }
    });

    // Enhanced table sorting visual feedback
    const sortableHeaders = document.querySelectorAll('th a[href*="sort="]');
    sortableHeaders.forEach(header => {
        header.addEventListener('mouseenter', function() {
            this.classList.add('bg-gray-100', 'rounded', 'px-2', 'py-1');
        });

        header.addEventListener('mouseleave', function() {
            this.classList.remove('bg-gray-100', 'rounded', 'px-2', 'py-1');
        });
    });

    // Highlight currently sorted column
    const urlCurrentSort = new URLSearchParams(window.location.search).get('sort');
    if (urlCurrentSort) {
        const currentSortHeader = document.querySelector(`th a[href*="sort=${urlCurrentSort}"]`);
        if (currentSortHeader) {
            currentSortHeader.closest('th').classList.add('bg-blue-50');
            currentSortHeader.classList.add('text-blue-600', 'font-semibold');
        }
    }

    // Auto-submit on filter changes (optional)
    const filterInputs = document.querySelectorAll('select');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Optional: Auto-submit after a delay
            // setTimeout(() => this.closest('form').submit(), 500);
        });
    });

    // Add keyboard shortcut for sorting (Ctrl + S to show sort options)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            alert('Click on any column header to sort by that column. Currently sorting by: ' + (currentSort || 'override_date'));
        }
    });
});
</script>
@endpush
@endsection
