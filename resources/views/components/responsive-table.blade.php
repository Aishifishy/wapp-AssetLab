@props([
    'headers' => [],
    'sortable' => false
])

<div {{ $attributes->merge(['class' => 'overflow-x-auto']) }}>
    <table class="min-w-full divide-y divide-gray-200">
        @if(!empty($headers))
            <thead class="bg-gray-50">
                <tr>
                    @foreach($headers as $header)
                        @if(is_array($header))
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider {{ $sortable && isset($header['sortable']) && $header['sortable'] ? 'cursor-pointer hover:bg-gray-100' : '' }}"
                                @if($sortable && isset($header['sortable']) && $header['sortable']) data-sort="{{ $header['key'] ?? '' }}" @endif>
                                @if($sortable && isset($header['sortable']) && $header['sortable'])
                                    <div class="flex items-center">
                                        {{ $header['label'] ?? $header }}
                                        <i class="fas fa-sort ml-2 text-gray-400"></i>
                                    </div>
                                @else
                                    {{ $header['label'] ?? $header }}
                                @endif
                            </th>
                        @else
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $header }}
                            </th>
                        @endif
                    @endforeach
                </tr>
            </thead>
        @endif
        
        <tbody class="bg-white divide-y divide-gray-200">
            {{ $slot }}
        </tbody>
    </table>
</div>
