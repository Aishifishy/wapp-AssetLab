<div class="bg-white rounded-lg shadow-sm overflow-hidden {{ $containerClass }}">
    @if($title || $showSearch || $headerActions ?? false)
        <div class="px-6 py-4 border-b border-gray-200 {{ $headerClass }}">
            <div class="flex items-center justify-between">
                <div>
                    @if($title)
                        <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
                    @endif
                    @if($description)
                        <p class="mt-1 text-sm text-gray-600">{{ $description }}</p>
                    @endif
                </div>
                
                <div class="flex items-center space-x-4">
                    @if($showSearch)
                        <div class="relative">
                            <input type="text" 
                                   id="table-search" 
                                   placeholder="{{ $searchPlaceholder }}" 
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    @endif
                    
                    @isset($headerActions)
                        {{ $headerActions }}
                    @endisset
                </div>
            </div>
        </div>
    @endif

    <div class="@if($responsive) overflow-x-auto @endif">
        <table class="min-w-full divide-y divide-gray-200 {{ $tableClass }}">
            {{ $slot }}
        </table>
    </div>
    
    @isset($tableFooter)
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $tableFooter }}
        </div>
    @endisset
</div>

@if($showSearch)
    @pushOnce('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('table-search');
            if (!searchInput) return;

            const table = searchInput.closest('.bg-white').querySelector('table');
            if (!table) return;

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const shouldShow = text.includes(searchTerm);
                    row.style.display = shouldShow ? '' : 'none';
                });
                
                // Show "no results" message if no rows are visible
                const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
                const tbody = table.querySelector('tbody');
                
                let noResultsRow = tbody.querySelector('.no-results-row');
                if (visibleRows.length === 0 && searchTerm.trim() !== '') {
                    if (!noResultsRow) {
                        const colCount = table.querySelectorAll('thead th').length;
                        noResultsRow = document.createElement('tr');
                        noResultsRow.className = 'no-results-row';
                        noResultsRow.innerHTML = `
                            <td colspan="${colCount}" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="h-12 w-12 text-gray-300 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-lg font-medium">No results found</p>
                                    <p class="text-sm text-gray-400">Try adjusting your search terms</p>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(noResultsRow);
                    }
                    noResultsRow.style.display = '';
                } else if (noResultsRow) {
                    noResultsRow.style.display = 'none';
                }
            });
        });
    </script>
    @endPushOnce
@endif
