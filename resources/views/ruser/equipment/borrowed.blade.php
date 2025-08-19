@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 space-y-3 sm:space-y-0">
        <h1 class="text-2xl font-bold text-gray-900">Currently Borrowed Equipment</h1>
        <a href="{{ route('ruser.equipment.borrow') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center">
            Borrow More Equipment
        </a>
    </div>

    @if($borrowedRequests->count() > 0)
        <div class="grid gap-6">
            @foreach($borrowedRequests as $request)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start space-y-4 lg:space-y-0">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $request->equipment->name }}</h3>
                            <p class="text-gray-600 mt-1">{{ $request->equipment->description }}</p>
                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Borrowed From:</span>
                                    <div class="text-gray-900 mt-1">{{ $request->requested_from->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-600">{{ $request->requested_from->format('g:i A') }}</div>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Return By:</span>
                                    <div class="text-gray-900 mt-1">{{ $request->requested_until->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-600">{{ $request->requested_until->format('g:i A') }}</div>
                                    @if($request->requested_until < now())
                                        <div class="text-red-600 text-sm font-medium mt-1">⚠️ OVERDUE</div>
                                    @endif
                                </div>
                                <div class="sm:col-span-2">
                                    <span class="font-medium text-gray-700">Purpose:</span>
                                    <p class="text-gray-600">{{ $request->purpose }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Status:</span>
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="ml-4">
                            @if(!$request->return_requested_at)
                                <form action="{{ route('ruser.equipment.return', $request) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm"
                                            onclick="return confirm('Are you sure you want to request return of this equipment?')">
                                        Request Return
                                    </button>
                                </form>
                            @else
                                <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                    Return Requested
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $borrowedRequests->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Equipment Currently Borrowed</h3>
            <p class="text-gray-600 mb-4">You don't have any equipment currently borrowed.</p>
            <a href="{{ route('ruser.equipment.borrow') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                Browse Equipment
            </a>
        </div>
    @endif
</div>
@endsection
