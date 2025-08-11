@extends('layouts.admin')

@section('content')
<div id="requestsContent">
    <!-- Pending Requests -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-clock text-yellow-500 mr-2"></i>
                    <h3 class="text-lg font-medium text-gray-900">Pending Reservation Requests</h3>
                    @if($pendingRequests->count() > 0)
                        <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $pendingRequests->count() }} pending
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="p-6">
            @if($pendingRequests->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingRequests as $request)
                        <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4 mb-2">
                                        <h4 class="text-lg font-medium text-gray-900">{{ $request->laboratory->name }}</h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                        <div>
                                            <p><strong>Requested by:</strong> {{ $request->user->name }}</p>
                                            <p><strong>Date:</strong> {{ $request->reservation_date->format('M d, Y') }}</p>
                                            <p><strong>Time:</strong> {{ $request->start_time->format('h:i A') }} - {{ $request->end_time->format('h:i A') }}</p>
                                            <p><strong>Duration:</strong> {{ $request->duration }}</p>
                                        </div>
                                        <div>
                                            <p><strong>Purpose:</strong> {{ $request->purpose }}</p>
                                            <p><strong>Students:</strong> {{ $request->num_students ?? 'Not specified' }}</p>
                                            <p><strong>Course:</strong> {{ $request->course_code ?? 'Not specified' }}</p>
                                            <p><strong>Subject:</strong> {{ $request->subject ?? 'Not specified' }}</p>
                                            @if($request->section)
                                                <p><strong>Section:</strong> {{ $request->section }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($request->is_recurring)
                                        <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded">
                                            <p class="text-sm text-blue-700">
                                                <i class="fas fa-repeat mr-1"></i>
                                                <strong>Recurring:</strong> {{ ucfirst($request->recurrence_pattern) }} 
                                                until {{ $request->recurrence_end_date->format('M d, Y') }}
                                            </p>
                                        </div>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-2">
                                        Requested {{ $request->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex flex-col space-y-2 ml-4">
                                    <form action="{{ route('admin.laboratory.approve-request', $request) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                                onclick="return confirm('Are you sure you want to approve this request?')">
                                            <i class="fas fa-check mr-1"></i> Approve
                                        </button>
                                    </form>
                                    <button type="button" 
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                            data-modal-target="rejectModal{{ $request->id }}">
                                        <i class="fas fa-times mr-1"></i> Reject
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Rejection Modal -->
                        <div id="rejectModal{{ $request->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                    <form action="{{ route('admin.laboratory.reject-request', $request) }}" method="POST">
                                        @csrf
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                        Reject Reservation Request
                                                    </h3>
                                                    <div class="mt-2">
                                                        <p class="text-sm text-gray-500 mb-4">
                                                            Please provide a reason for rejecting this reservation request.
                                                        </p>
                                                        <textarea name="rejection_reason" 
                                                                  id="rejection_reason" 
                                                                  rows="4" 
                                                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                                                  placeholder="Enter rejection reason..."
                                                                  required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                Reject Request
                                            </button>
                                            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" data-modal-close>
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Pending Requests</h3>
                    <p class="text-gray-500">There are no pending laboratory reservation requests at this time.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Requests -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <i class="fas fa-history text-gray-500 mr-2"></i>
                <h3 class="text-lg font-medium text-gray-900">Recent Request Activity</h3>
            </div>
        </div>
        <div class="p-6">
            @if($recentRequests->count() > 0)
                <div class="space-y-3">
                    @foreach($recentRequests as $request)
                        <div class="border-l-4 {{ $request->status === 'approved' ? 'border-green-400 bg-green-50' : 'border-red-400 bg-red-50' }} p-4 rounded-r-lg">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h4 class="font-medium text-gray-900">{{ $request->laboratory->name }}</h4>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                        <span class="text-sm text-gray-500">{{ $request->updated_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <p><strong>User:</strong> {{ $request->user->name }}</p>
                                        <p><strong>Date & Time:</strong> {{ $request->reservation_date->format('M d, Y') }} • {{ $request->start_time->format('h:i A') }} - {{ $request->end_time->format('h:i A') }}</p>
                                        <p><strong>Purpose:</strong> {{ $request->purpose }}</p>
                                        @if($request->status === 'rejected' && $request->rejection_reason)
                                            <p class="mt-2 p-2 bg-red-100 border border-red-200 rounded text-red-700">
                                                <strong>Rejection Reason:</strong> {{ $request->rejection_reason }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-clock text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Recent Activity</h3>
                    <p class="text-gray-500">No recent reservation request activity to display.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
