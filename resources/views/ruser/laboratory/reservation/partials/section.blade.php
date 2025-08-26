@php
    $columns = $isPending ? ['Laboratory', 'Date', 'Time', 'Purpose', 'Status', 'Actions'] : 
               ($showActions ? ['Laboratory', 'Date', 'Time', 'Purpose', 'Actions'] : 
               ['Laboratory', 'Date', 'Time', 'Status', 'Actions']);
@endphp

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">{{ $title }}</h2>
    </div>
    <div class="p-6">
        @if($reservations->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach($columns as $column)
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $column }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reservations as $reservation)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $reservation->laboratory->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $reservation->laboratory->building }}, Room {{ $reservation->laboratory->room_number }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $reservation->reservation_date->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                                    </div>
                                    @if($isPending || $showActions)
                                        <div class="text-xs text-gray-500">
                                            Duration: {{ $reservation->duration }}
                                        </div>
                                    @endif
                                </td>
                                
                                @if($isPending)
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 truncate max-w-xs">
                                            {{ \Illuminate\Support\Str::limit($reservation->purpose, 50) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-status-badge status="pending" type="reservation" />
                                    </td>
                                @elseif($showActions)
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 truncate max-w-xs">
                                            {{ \Illuminate\Support\Str::limit($reservation->purpose, 50) }}
                                        </div>
                                    </td>
                                @else
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-status-badge :status="$reservation->status" type="reservation" />
                                    </td>
                                @endif
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($showActions)
                                        <div class="flex flex-col sm:flex-row sm:justify-end space-y-1 sm:space-y-0 sm:space-x-4">
                                            <a href="{{ route('ruser.laboratory.reservations.show', $reservation) }}" 
                                               class="text-blue-600 hover:text-blue-900 text-center sm:text-left">View</a>
                                            
                                            @php
                                                $canCancel = $isPending || 
                                                    \Carbon\Carbon::parse($reservation->reservation_date . ' ' . $reservation->start_time)
                                                    ->diffInHours(now()) >= 24;
                                            @endphp
                                            
                                            @if($canCancel)
                                                <form action="{{ route('ruser.laboratory.reservations.cancel', $reservation) }}" 
                                                      method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900 confirm-action w-full sm:w-auto" 
                                                            data-confirm-message="Are you sure you want to cancel this reservation?">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @elseif(!$isPending)
                                                <span class="text-gray-400 text-center sm:text-left" 
                                                      title="Reservations cannot be cancelled within 24 hours of start time">
                                                    Cannot Cancel
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="flex justify-center sm:justify-end">
                                            <a href="{{ route('ruser.laboratory.reservations.show', $reservation) }}" 
                                               class="text-blue-600 hover:text-blue-900">View</a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(isset($showPagination) && $showPagination)
                <div class="mt-4">
                    {{ $reservations->links() }}
                </div>
            @endif
        @else
            <p class="text-gray-500">{{ $emptyMessage }}</p>
        @endif
    </div>
</div>
