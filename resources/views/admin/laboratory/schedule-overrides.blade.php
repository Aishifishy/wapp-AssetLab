@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Laboratory Management</h1>
        <a href="{{ route('admin.laboratory.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            <i class="fas fa-arrow-left mr-2"></i> Back to Laboratories
        </a>
    </div>

    <x-flash-messages />

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-6">
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

    <!-- Schedule Overrides Content -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Schedule Overrides</h2>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.laboratory.create-override') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i>
                Create Override
            </a>
        </div>
    </div>

    <x-flash-messages />

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('admin.laboratory.schedule-overrides') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="laboratory_id" class="block text-sm font-medium text-gray-700">Laboratory</label>
                <select name="laboratory_id" id="laboratory_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">All Laboratories</option>
                    @foreach($laboratories as $lab)
                        <option value="{{ $lab->id }}" {{ request('laboratory_id') == $lab->id ? 'selected' : '' }}>
                            {{ $lab->name }} ({{ $lab->building }} - {{ $lab->room_number }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="md:col-span-4 flex justify-end space-x-3">
                <a href="{{ route('admin.laboratory.schedule-overrides') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Clear Filters
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-search mr-2"></i>
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Overrides Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>
                    <h3 class="text-lg font-medium text-gray-900">Schedule Overrides</h3>
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $overrides->total() }} total
                    </span>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            @if($overrides->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Override Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Original Schedule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($overrides as $override)
                        <tr class="{{ $override->isCurrentlyActive() ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $override->laboratory->name }}</div>
                                <div class="text-sm text-gray-500">{{ $override->laboratory->building }} - {{ $override->laboratory->room_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $override->override_date->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $override->override_date->format('l') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($override->override_type === 'cancel') bg-red-100 text-red-800
                                    @elseif($override->override_type === 'reschedule') bg-yellow-100 text-yellow-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst($override->override_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($override->originalSchedule)
                                    <div class="text-sm text-gray-900">{{ $override->originalSchedule->subject_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $override->originalSchedule->instructor_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $override->originalSchedule->time_range }}</div>
                                @else
                                    <span class="text-sm text-gray-400">No original schedule</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($override->override_type === 'cancel')
                                    <span class="text-sm text-red-600 font-medium">Cancelled</span>
                                @else
                                    @php $effectiveSchedule = $override->getEffectiveSchedule(); @endphp
                                    @if($effectiveSchedule)
                                        <div class="text-sm text-gray-900">{{ $effectiveSchedule['subject_name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $effectiveSchedule['instructor_name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $override->time_range }}</div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($override->reason, 50) }}</div>
                                @if($override->expires_at)
                                    <div class="text-xs text-gray-500 mt-1">
                                        Expires: {{ $override->expires_at->format('M d, Y H:i') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($override->requestedBy)
                                    <div class="text-sm text-gray-900">{{ $override->requestedBy->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $override->requestedBy->email }}</div>
                                @else
                                    <span class="text-sm text-gray-400">No user specified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $override->createdBy->name }}</div>
                                <div class="text-sm text-gray-500">{{ $override->created_at->format('M d, Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($override->isCurrentlyActive())
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($override->isCurrentlyActive())
                                    <form action="{{ route('admin.laboratory.deactivate-override', $override) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure you want to deactivate this override?')">
                                            Deactivate
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400">No actions</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $overrides->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-exclamation-triangle text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Schedule Overrides Found</h3>
                    <p class="text-gray-500 mb-6">
                        @if(request()->hasAny(['laboratory_id', 'start_date', 'end_date', 'status']))
                            No overrides match your current filters.
                        @else
                            You haven't created any schedule overrides yet.
                        @endif
                    </p>
                    <div class="flex justify-center space-x-3">
                        @if(request()->hasAny(['laboratory_id', 'start_date', 'end_date', 'status']))
                            <a href="{{ route('admin.laboratory.schedule-overrides') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Clear Filters
                            </a>
                        @endif
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

    @if($overrides->count() > 0)
        <!-- Summary Statistics -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="text-sm font-medium text-gray-500">Total Overrides</div>
                <div class="text-2xl font-bold text-gray-900">{{ $overrides->total() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="text-sm font-medium text-gray-500">Active</div>
                <div class="text-2xl font-bold text-green-600">{{ $overrides->filter(fn($o) => $o->isCurrentlyActive())->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="text-sm font-medium text-gray-500">Cancellations</div>
                <div class="text-2xl font-bold text-red-600">{{ $overrides->filter(fn($o) => $o->override_type === 'cancel')->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="text-sm font-medium text-gray-500">Reschedules</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $overrides->filter(fn($o) => $o->override_type === 'reschedule')->count() }}</div>
            </div>
        </div>
    @endif
</div>
@endsection
