@props(['title', 'backRoute', 'backText' => 'Back', 'formAction', 'formMethod' => 'POST', 'submitText' => 'Save', 'cancelAction' => 'window.history.back()'])

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">{{ $title }}</h1>
        @if($backRoute)
            <a href="{{ $backRoute }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <i class="fas fa-arrow-left mr-2"></i> {{ $backText }}
            </a>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <form action="{{ $formAction }}" method="{{ $formMethod }}" {{ $attributes }}>
                @csrf
                @if($formMethod !== 'POST')
                    @method($formMethod)
                @endif
                
                <div class="space-y-6">
                    {{ $slot }}
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" 
                            onclick="{{ $cancelAction }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i> {{ $submitText }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
