@props([
    'title' => 'Reject Reservation',
    'actionUrl' => '',
    'modalId' => 'rejection-modal',
    'formId' => 'reject-form',
    'reasonFieldId' => 'rejection_reason',
    'reasonLabel' => 'Rejection Reason',
    'submitText' => 'Reject Reservation',
    'cancelText' => 'Cancel'
])

<div id="{{ $modalId }}" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
            <button type="button" onclick="closeRejectModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="{{ $formId }}" action="{{ $actionUrl }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="{{ $reasonFieldId }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $reasonLabel }}</label>
                <textarea id="{{ $reasonFieldId }}" name="rejection_reason" rows="4" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="button" 
                        data-action="close-reject-modal" 
                        class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 mr-3">
                    {{ $cancelText }}
                </button>
                <button type="submit" class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700">
                    {{ $submitText }}
                </button>
            </div>
        </form>
    </div>
</div>
