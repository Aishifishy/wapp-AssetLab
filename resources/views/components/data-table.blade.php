<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    @if($showSearch)
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">{{ $slot ?? 'Data Table' }}</h3>
            <div class="relative">
                <input type="text" 
                       id="{{ $tableId }}-search" 
                       placeholder="Search..." 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="@if($responsive) overflow-x-auto @endif">
        <table id="{{ $tableId }}" 
               class="min-w-full divide-y divide-gray-200 {{ $tableClass }}"
               @if($sortable) data-sortable="true" @endif>
            <thead class="bg-gray-50 {{ $headerClass }}">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider {{ $header['class'] ?? '' }}"
                            @if($sortable && ($header['sortable'] ?? true))
                                data-sortable="true"
                                data-column="{{ $header['key'] ?? '' }}"
                            @endif>
                            <div class="flex items-center space-x-1">
                                <span>{{ $header['label'] ?? $header['title'] ?? '' }}</span>
                                @if($sortable && ($header['sortable'] ?? true))
                                    <svg class="w-4 h-4 text-gray-400 sort-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z" />
                                    </svg>
                                @endif
                            </div>
                            @if(isset($header['description']))
                                <div class="text-xs text-gray-400 font-normal mt-1">{{ $header['description'] }}</div>
                            @endif
                        </th>
                    @endforeach
                    
                    @if(!empty($actions))
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $actionColumnTitle }}
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 {{ $bodyClass }}">
                @forelse($data as $index => $row)
                    <tr class="hover:bg-gray-50 {{ $rowClass }} {{ $row['_row_class'] ?? '' }}" 
                        data-row-index="{{ $index }}">
                        @foreach($headers as $header)
                            <td class="px-6 py-4 whitespace-nowrap {{ $cellClass }} {{ $header['cell_class'] ?? '' }}">
                                @if(isset($header['slot']) && isset($row[$header['slot']]))
                                    {!! $row[$header['slot']] !!}
                                @else
                                    @php
                                        $value = $this->getColumnValue($row, $header);
                                        $formatted = $this->formatColumnValue($value, $header, $row);
                                    @endphp
                                    
                                    @if($header['type'] ?? 'text' === 'html')
                                        {!! $formatted !!}
                                    @else
                                        {{ $formatted }}
                                    @endif
                                @endif
                            </td>
                        @endforeach
                        
                        @if(!empty($actions))
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @foreach($actions as $action)
                                        @php
                                            $url = $action['url'] ?? '#';
                                            $method = $action['method'] ?? 'GET';
                                            $class = $action['class'] ?? 'text-blue-600 hover:text-blue-900';
                                            $title = $action['title'] ?? '';
                                            $icon = $action['icon'] ?? '';
                                            $condition = $action['condition'] ?? null;
                                            
                                            // Replace placeholders in URL
                                            if (strpos($url, '{') !== false) {
                                                preg_match_all('/\{(\w+)\}/', $url, $matches);
                                                foreach ($matches[1] as $placeholder) {
                                                    $replacement = is_object($row) ? ($row->{$placeholder} ?? '') : ($row[$placeholder] ?? '');
                                                    $url = str_replace('{' . $placeholder . '}', $replacement, $url);
                                                }
                                            }
                                            
                                            // Check condition
                                            $show = true;
                                            if ($condition && is_callable($condition)) {
                                                $show = $condition($row);
                                            } elseif ($condition && is_string($condition)) {
                                                // Simple property check
                                                $show = is_object($row) ? ($row->{$condition} ?? false) : ($row[$condition] ?? false);
                                            }
                                        @endphp
                                        
                                        @if($show)
                                            @if(strtoupper($method) === 'GET')
                                                <a href="{{ $url }}" 
                                                   class="{{ $class }}" 
                                                   title="{{ $title }}"
                                                   @if($action['target'] ?? false) target="{{ $action['target'] }}" @endif>
                                                    @if($icon)
                                                        <i class="{{ $icon }}"></i>
                                                    @endif
                                                    {{ $action['label'] ?? '' }}
                                                </a>
                                            @elseif(strtoupper($method) === 'DELETE')
                                                <form method="POST" action="{{ $url }}" class="inline" 
                                                      onsubmit="return confirm('{{ $action['confirm'] ?? 'Are you sure?' }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="{{ $class }}" title="{{ $title }}">
                                                        @if($icon)
                                                            <i class="{{ $icon }}"></i>
                                                        @endif
                                                        {{ $action['label'] ?? '' }}
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ $url }}" class="inline"
                                                      @if($action['confirm'] ?? false) onsubmit="return confirm('{{ $action['confirm'] }}')" @endif>
                                                    @csrf
                                                    @if(strtoupper($method) !== 'POST')
                                                        @method($method)
                                                    @endif
                                                    <button type="submit" class="{{ $class }}" title="{{ $title }}">
                                                        @if($icon)
                                                            <i class="{{ $icon }}"></i>
                                                        @endif
                                                        {{ $action['label'] ?? '' }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + (!empty($actions) ? 1 : 0) }}" 
                            class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="h-12 w-12 text-gray-300 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-lg font-medium">{{ $emptyMessage }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($showPagination && method_exists($data, 'links'))
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $data->links() }}
        </div>
    @endif
</div>

@if($sortable || $showSearch)
    @pushOnce('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable functionality
            const tableElement = document.getElementById('{{ $tableId }}');
            if (!tableElement) return;

            // Search functionality
            @if($showSearch)
                const searchInput = document.getElementById('{{ $tableId }}-search');
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase();
                        const rows = tableElement.querySelectorAll('tbody tr');
                        
                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            const shouldShow = text.includes(searchTerm);
                            row.style.display = shouldShow ? '' : 'none';
                        });
                    });
                }
            @endif

            // Sort functionality
            @if($sortable)
                const sortableHeaders = tableElement.querySelectorAll('th[data-sortable="true"]');
                let currentSort = { column: null, direction: 'asc' };

                sortableHeaders.forEach(header => {
                    header.style.cursor = 'pointer';
                    header.addEventListener('click', function() {
                        const column = this.dataset.column;
                        const columnIndex = Array.from(this.parentNode.children).indexOf(this);
                        
                        // Update sort direction
                        if (currentSort.column === column) {
                            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                        } else {
                            currentSort.direction = 'asc';
                        }
                        currentSort.column = column;

                        // Sort the table
                        sortTable(columnIndex, currentSort.direction);
                        
                        // Update visual indicators
                        updateSortIndicators(this, currentSort.direction);
                    });
                });

                function sortTable(columnIndex, direction) {
                    const tbody = tableElement.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    
                    rows.sort((a, b) => {
                        const aValue = a.children[columnIndex]?.textContent.trim() || '';
                        const bValue = b.children[columnIndex]?.textContent.trim() || '';
                        
                        // Try to parse as numbers
                        const aNum = parseFloat(aValue);
                        const bNum = parseFloat(bValue);
                        
                        if (!isNaN(aNum) && !isNaN(bNum)) {
                            return direction === 'asc' ? aNum - bNum : bNum - aNum;
                        }
                        
                        // String comparison
                        const result = aValue.localeCompare(bValue);
                        return direction === 'asc' ? result : -result;
                    });
                    
                    // Re-append sorted rows
                    rows.forEach(row => tbody.appendChild(row));
                }

                function updateSortIndicators(activeHeader, direction) {
                    // Reset all indicators
                    sortableHeaders.forEach(header => {
                        const icon = header.querySelector('.sort-icon');
                        if (icon) {
                            icon.classList.remove('text-blue-500');
                            icon.classList.add('text-gray-400');
                        }
                    });
                    
                    // Update active indicator
                    const activeIcon = activeHeader.querySelector('.sort-icon');
                    if (activeIcon) {
                        activeIcon.classList.remove('text-gray-400');
                        activeIcon.classList.add('text-blue-500');
                        
                        // Update icon based on direction
                        if (direction === 'desc') {
                            activeIcon.style.transform = 'rotate(180deg)';
                        } else {
                            activeIcon.style.transform = 'rotate(0deg)';
                        }
                    }
                }
            @endif
        });
    </script>
    @endPushOnce
@endif
