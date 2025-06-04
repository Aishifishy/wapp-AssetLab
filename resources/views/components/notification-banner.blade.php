@if($shouldShow())
<div class="border rounded-lg p-4 {{ $getBannerClasses() }} {{ $attributes->get('class', 'mb-4') }}" 
     role="alert" 
     {{ $attributes->except('class') }}>
    
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 {{ $getIconClasses() }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $getDefaultIcon() }}" />
            </svg>
        </div>
        
        <div class="ml-3 flex-1">
            @if($title)
                <h3 class="text-sm font-medium {{ $getTextClasses() }}">
                    {{ $title }}
                </h3>
            @endif
            
            <div class="text-sm {{ $title ? 'mt-2' : '' }} {{ $getTextClasses() }}">
                @if($slot->isNotEmpty())
                    {{ $slot }}
                @else
                    {{ $getSessionMessage() }}
                @endif
            </div>

            @if(!empty($actions))
                <div class="mt-4">
                    <div class="-mx-2 -my-1.5 flex">
                        @foreach($actions as $action)
                            <button type="button" 
                                    class="px-2 py-1.5 rounded-md text-sm font-medium {{ $type === 'success' ? 'bg-green-100 text-green-800 hover:bg-green-200' : ($type === 'error' || $type === 'danger' ? 'bg-red-100 text-red-800 hover:bg-red-200' : ($type === 'warning' ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-blue-100 text-blue-800 hover:bg-blue-200')) }}"
                                    @if(isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif
                                    @if(isset($action['href'])) onclick="window.location.href='{{ $action['href'] }}'" @endif>
                                {{ $action['text'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        
        @if($dismissible)
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" 
                            class="inline-flex rounded-md p-1.5 {{ $getIconClasses() }} {{ $type === 'success' ? 'hover:bg-green-100' : ($type === 'error' || $type === 'danger' ? 'hover:bg-red-100' : ($type === 'warning' ? 'hover:bg-yellow-100' : 'hover:bg-blue-100')) }} focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $type === 'success' ? 'focus:ring-green-600' : ($type === 'error' || $type === 'danger' ? 'focus:ring-red-600' : ($type === 'warning' ? 'focus:ring-yellow-600' : 'focus:ring-blue-600')) }}"
                            onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
@endif
