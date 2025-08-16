@extends('layouts.admin')

@section('title', 'Export Equipment Barcodes')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 space-y-3 sm:space-y-0">
        <h1 class="text-2xl font-semibold text-gray-800">Export Equipment Barcodes</h1>
        <a href="{{ route('admin.equipment.manage') }}" 
           class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            <i class="fas fa-arrow-left mr-2"></i> Back to Equipment
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Export Options Card -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Export Options</h3>
                <p class="text-sm text-gray-600">Choose what barcodes to export and label size</p>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Label Size Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Label Size</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($labelSizes as $key => $label)
                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:bg-gray-50">
                                <input type="radio" name="label_size" value="{{ $key }}" 
                                       class="sr-only" 
                                       {{ $key === 'standard' ? 'checked' : '' }}>
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">{{ $label }}</span>
                                        <span class="mt-1 flex items-center text-sm text-gray-500">
                                            @if($key === 'small')
                                                Perfect for asset tags
                                            @elseif($key === 'standard')
                                                Most common size
                                            @elseif($key === 'medium')
                                                Good visibility
                                            @else
                                                Maximum readability
                                            @endif
                                        </span>
                                    </span>
                                </span>
                                <span class="label-size-check absolute -inset-px rounded-lg border-2 pointer-events-none" aria-hidden="true"></span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Export Actions -->
                <div class="space-y-4">
                    <h4 class="text-sm font-medium text-gray-700">Export Actions</h4>
                    
                    <!-- Export All -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-download text-blue-600"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <h5 class="text-sm font-medium text-blue-900">Export All Equipment Barcodes</h5>
                                <p class="text-sm text-blue-700 mb-3">Generate PDF with all {{ $equipment->count() }} equipment barcodes</p>
                                <button type="button" onclick="exportAllBarcodes()" 
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-file-pdf mr-1"></i> Export All
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Export Selected -->
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-square text-green-600"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <h5 class="text-sm font-medium text-green-900">Export Selected Equipment</h5>
                                <p class="text-sm text-green-700 mb-3">Select specific equipment items from the list below</p>
                                <button type="button" onclick="exportSelectedBarcodes()" 
                                        id="export-selected-btn"
                                        disabled
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-file-pdf mr-1"></i> Export Selected (<span id="selected-count">0</span>)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Label Preview</h3>
                <p class="text-sm text-gray-600">Preview of how your labels will look</p>
            </div>
            
            <div class="p-6">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <div id="label-preview" class="inline-block border border-gray-400 p-4 bg-white" style="width: 200px;">
                        <div class="text-xs font-bold mb-1">SAMPLE EQUIPMENT</div>
                        <div class="text-xs text-gray-600 mb-2">Computer Hardware</div>
                        <div class="my-2">
                            <div class="bg-black h-8 w-full mb-1" style="background: repeating-linear-gradient(90deg, #000 0px, #000 2px, #fff 2px, #fff 4px);"></div>
                            <div class="text-xs font-mono">EQP123456</div>
                        </div>
                        <div class="text-xs text-gray-500">ID: 1</div>
                    </div>
                    <p class="text-sm text-gray-500 mt-4">This is a preview of how your labels will appear</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipment Selection Table -->
    <div class="mt-8 bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Equipment Selection</h3>
                <p class="text-sm text-gray-600">Select specific equipment to export</p>
            </div>
            <div class="flex items-center space-x-2">
                <button type="button" onclick="selectAll()" 
                        class="text-sm text-blue-600 hover:text-blue-800">Select All</button>
                <span class="text-gray-300">|</span>
                <button type="button" onclick="selectNone()" 
                        class="text-sm text-gray-600 hover:text-gray-800">Clear All</button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="select-all-checkbox" onchange="toggleSelectAll()">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($equipment as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="equipment-checkbox" value="{{ $item->id }}" onchange="updateSelectedCount()">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($item->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->category->name ?? 'Uncategorized' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="text-sm font-mono bg-gray-100 px-2 py-1 rounded">{{ $item->barcode }}</code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-status-badge :status="$item->status" type="equipment" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.equipment.barcode.single', $item) }}?label_size=standard" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-download mr-1"></i> Single
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No equipment with barcodes found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    updateLabelPreview();
    updateSelectedCount();
    
    // Add event listeners for label size changes
    const labelSizeInputs = document.querySelectorAll('input[name="label_size"]');
    labelSizeInputs.forEach(input => {
        input.addEventListener('change', updateLabelPreview);
    });
});

function updateLabelPreview() {
    const selectedSize = document.querySelector('input[name="label_size"]:checked').value;
    const preview = document.getElementById('label-preview');
    
    // Update visual styling of radio buttons
    const labels = document.querySelectorAll('.label-size-check');
    labels.forEach(label => {
        label.classList.remove('border-blue-500');
        label.classList.add('border-transparent');
    });
    
    const selectedLabel = document.querySelector('input[name="label_size"]:checked').closest('label').querySelector('.label-size-check');
    selectedLabel.classList.remove('border-transparent');
    selectedLabel.classList.add('border-blue-500');
    
    // Update preview size
    switch(selectedSize) {
        case 'small':
            preview.style.width = '150px';
            preview.style.fontSize = '10px';
            break;
        case 'medium':
            preview.style.width = '250px';
            preview.style.fontSize = '12px';
            break;
        case 'large':
            preview.style.width = '300px';
            preview.style.fontSize = '14px';
            break;
        default: // standard
            preview.style.width = '200px';
            preview.style.fontSize = '11px';
    }
}

function getSelectedLabelSize() {
    return document.querySelector('input[name="label_size"]:checked').value;
}

function exportAllBarcodes() {
    const labelSize = getSelectedLabelSize();
    window.open(`{{ route('admin.equipment.barcode.all') }}?label_size=${labelSize}`, '_blank');
}

function exportSelectedBarcodes() {
    const selectedIds = getSelectedEquipmentIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one equipment item');
        return;
    }
    
    const labelSize = getSelectedLabelSize();
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.equipment.barcode.selected") }}';
    form.target = '_blank';
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Add label size
    const labelSizeInput = document.createElement('input');
    labelSizeInput.type = 'hidden';
    labelSizeInput.name = 'label_size';
    labelSizeInput.value = labelSize;
    form.appendChild(labelSizeInput);
    
    // Add selected equipment IDs
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'equipment_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function getSelectedEquipmentIds() {
    const checkboxes = document.querySelectorAll('.equipment-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function updateSelectedCount() {
    const count = getSelectedEquipmentIds().length;
    document.getElementById('selected-count').textContent = count;
    document.getElementById('export-selected-btn').disabled = count === 0;
}

function selectAll() {
    const checkboxes = document.querySelectorAll('.equipment-checkbox');
    checkboxes.forEach(cb => cb.checked = true);
    document.getElementById('select-all-checkbox').checked = true;
    updateSelectedCount();
}

function selectNone() {
    const checkboxes = document.querySelectorAll('.equipment-checkbox');
    checkboxes.forEach(cb => cb.checked = false);
    document.getElementById('select-all-checkbox').checked = false;
    updateSelectedCount();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const checkboxes = document.querySelectorAll('.equipment-checkbox');
    
    checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
    updateSelectedCount();
}
</script>
@endsection
