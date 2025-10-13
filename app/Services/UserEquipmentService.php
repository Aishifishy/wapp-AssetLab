<?php

namespace App\Services;

use App\Models\EquipmentRequest;
use App\Models\Equipment;
use App\Services\EquipmentConflictService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Equipment Request service for user-related equipment operations
 */
class UserEquipmentService extends BaseService
{
    protected $conflictService;
    
    public function __construct(EquipmentConflictService $conflictService)
    {
        $this->conflictService = $conflictService;
    }
    
    protected function getModel()
    {
        return EquipmentRequest::class;
    }

    /**
     * Get user's equipment request statistics
     */
    public function getUserStats($userId)
    {
        return [
            'pending_requests' => EquipmentRequest::where('user_id', $userId)->pending()->count(),
            'currently_borrowed' => EquipmentRequest::where('user_id', $userId)->where('status', EquipmentRequest::STATUS_APPROVED)->whereNull('returned_at')->count(),
            'upcoming_returns' => EquipmentRequest::where('user_id', $userId)->where('status', EquipmentRequest::STATUS_APPROVED)->whereNull('returned_at')->where('requested_until', '<=', Carbon::now()->addDays(7))->count(),
            'total_requests' => EquipmentRequest::where('user_id', $userId)->count(),
            'approved_requests' => EquipmentRequest::where('user_id', $userId)->approved()->count(),
        ];
    }

    /**
     * Get user's currently borrowed equipment
     */
    public function getCurrentlyBorrowed($userId, $perPage = 10)
    {
        return EquipmentRequest::with(['equipment'])
            ->where('user_id', $userId)
            ->where('status', EquipmentRequest::STATUS_APPROVED)
            ->whereNull('returned_at')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get user's pending equipment requests
     */
    /**
     * Get user's equipment history
     */
    public function getHistory($userId, $perPage = 15)
    {
        return EquipmentRequest::with(['equipment'])
            ->where('user_id', $userId)
            ->whereIn('status', [EquipmentRequest::STATUS_RETURNED, EquipmentRequest::STATUS_REJECTED])
            ->orWhere(function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->approved()
                      ->whereNotNull('returned_at');
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get user's recent equipment activities for dashboard
     */
    public function getRecentActivities($userId, $limit = 15)
    {
        return EquipmentRequest::with(['equipment.category', 'approvedBy', 'rejectedBy', 'checkedOutBy'])
            ->where('user_id', $userId)
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($request) {
                return $this->formatActivity($request);
            });
    }

    /**
     * Get equipment available for borrowing by category
     */
    public function getAvailableEquipmentByCategory($categoryId = null)
    {
        $query = Equipment::whereIn('status', ['available', 'borrowed', 'unavailable'])
                          ->with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->latest()->paginate(12);
    }

    /**
     * Create a new equipment request with conflict checking and advance booking support
     */
    public function createRequest(array $data, $userId)
    {
        $equipment = Equipment::findOrFail($data['equipment_id']);
        $from = Carbon::parse($data['requested_from']);
        $until = Carbon::parse($data['requested_until']);

        // Check availability for the requested time period
        $availability = $this->conflictService->checkAvailability($equipment->id, $data['requested_from'], $data['requested_until']);
        $advanceBookingCheck = null; // Initialize to avoid undefined variable error
        
        // If not available for immediate booking, allow advance booking (queue system)
        if (!$availability['available']) {
            // For advance booking, check if the time slot is valid for future booking
            $advanceBookingCheck = $this->conflictService->checkAdvanceBooking($equipment->id, $data['requested_from'], $data['requested_until']);
            if (!$advanceBookingCheck['can_book']) {
                return [
                    'success' => false,
                    'message' => 'Equipment cannot be scheduled for this time period.',
                    'details' => $advanceBookingCheck
                ];
            }
        }

        // Create the request
        $request = EquipmentRequest::create([
            'equipment_id' => $equipment->id,
            'user_id' => $userId,
            'status' => EquipmentRequest::STATUS_PENDING,
            'purpose' => $data['purpose'],
            'requested_from' => $data['requested_from'],
            'requested_until' => $data['requested_until'],
        ]);

        // Prepare response message
        $message = 'Your equipment request has been submitted successfully.';
        
        if (!$availability['available']) {
            // This is an advance booking (queue system)
            $queuePosition = $this->conflictService->getQueuePosition($equipment->id, $data['requested_from']);
            if ($queuePosition > 1) {
                $message .= " You are position #{$queuePosition} in the queue for this time slot.";
            } else {
                $message .= " Your request will be processed when the equipment becomes available.";
            }
        }

        return [
            'success' => true,
            'message' => $message,
            'request' => $request,
            'booking_info' => $advanceBookingCheck
        ];
    }

    /**
     * Cancel a pending request
     */
    public function cancelRequest(EquipmentRequest $request, $userId)
    {
        if ($request->user_id !== $userId) {
            return [
                'success' => false,
                'message' => 'You are not authorized to cancel this request.'
            ];
        }

        if ($request->status !== EquipmentRequest::STATUS_PENDING) {
            return [
                'success' => false,
                'message' => 'Only pending requests can be canceled.'
            ];
        }

        // Update status to cancelled instead of deleting
        $request->update([
            'status' => EquipmentRequest::STATUS_CANCELLED,
            'cancelled_at' => now()
        ]);
        
        return [
            'success' => true,
            'message' => 'Equipment request has been cancelled successfully.'
        ];
    }

    /**
     * Check equipment availability for a specific time period (simplified)
     */
    public function checkAvailabilityForTimeSlot($equipmentId, $requestedFrom, $requestedUntil, $userId = null)
    {
        Log::info('UserEquipmentService::checkAvailabilityForTimeSlot called', [
            'equipment_id' => $equipmentId,
            'requested_from' => $requestedFrom,
            'requested_until' => $requestedUntil,
            'user_id' => $userId
        ]);

        try {
            $equipment = Equipment::findOrFail($equipmentId);
            Log::info('Equipment found', ['equipment' => $equipment->toArray()]);
            
            $from = Carbon::parse($requestedFrom);
            $until = Carbon::parse($requestedUntil);
            Log::info('Dates parsed', ['from' => $from->toDateTimeString(), 'until' => $until->toDateTimeString()]);

            // Check if equipment is available for the requested time
            $availability = $this->conflictService->checkAvailability($equipmentId, $requestedFrom, $requestedUntil);
            Log::info('Conflict service result', ['availability' => $availability]);
        
        if ($availability['available']) {
            return [
                'available' => true,
                'message' => 'Equipment is available for the requested time period.',
                'can_book' => true
            ];
        }

        // Get detailed conflict information
        $conflicts = $this->conflictService->getConflictingRequests($equipmentId, $from, $until);
        $suggestions = $this->conflictService->getAlternativeSuggestions($equipment, $from, $until);
        
        // Check if user can join queue (advance booking)
        $canQueue = $this->conflictService->canJoinQueue($equipment, $from, $until);
        $queuePosition = null;
        
        if ($canQueue) {
            $queuePosition = $this->conflictService->getQueuePosition($equipmentId, $requestedFrom);
        }

        return [
            'available' => false,
            'message' => 'Equipment is not available for the requested time period.',
            'conflicts' => $conflicts->map(function($conflict) {
                return [
                    'requested_from' => $conflict->requested_from,
                    'requested_until' => $conflict->requested_until,
                    'user_name' => $conflict->user->name ?? 'Unknown',
                    'status' => $conflict->status
                ];
            })->toArray(),
            'suggestions' => $suggestions,
            'can_queue' => $canQueue,
            'queue_position' => $queuePosition,
            'can_book' => $canQueue // Allow booking even if there's a conflict (queue system)
        ];
        } catch (\Exception $e) {
            Log::error('Error in checkAvailabilityForTimeSlot', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Check equipment availability for booking
     *
     * @param int $equipmentId
     * @param string $bookingType
     * @param string|null $requestedFrom
     * @param string|null $requestedUntil
     * @param int $userId
     * @return array
     */
    public function checkAvailability($equipmentId, $bookingType, $requestedFrom = null, $requestedUntil = null, $userId = null)
    {
        $equipment = Equipment::findOrFail($equipmentId);
        
        if ($bookingType === 'immediate') {
            if (!$requestedFrom || !$requestedUntil) {
                return [
                    'available' => false,
                    'message' => 'Please provide both from and until dates for immediate booking.'
                ];
            }
            
            $from = Carbon::parse($requestedFrom);
            $until = Carbon::parse($requestedUntil);
            
            $isAvailable = $this->conflictService->checkAvailability($equipment, $from, $until);
            
            if ($isAvailable) {
                return [
                    'available' => true,
                    'message' => 'Equipment is available for the requested time period.'
                ];
            } else {
                $conflicts = $this->conflictService->getConflictingRequests($equipment, $from, $until);
                $nextAvailable = $this->conflictService->getNextAvailableTime($equipment, $from);
                
                return [
                    'available' => false,
                    'message' => 'Equipment is not available for the requested time period.',
                    'conflicts' => $conflicts->count(),
                    'next_available' => $nextAvailable?->format('Y-m-d\TH:i:s')
                ];
            }
        } else {
            // Advance booking - get available slots
            $startDate = Carbon::now()->addDay();
            $endDate = Carbon::now()->addDays(30);
            
            $availableSlots = $this->conflictService->getAvailableSlots($equipment, $startDate, $endDate);
            
            $result = [
                'available_slots' => collect($availableSlots)->map(function ($slot) {
                    return [
                        'from' => $slot['from']->format('Y-m-d\TH:i:s'),
                        'until' => $slot['until']->format('Y-m-d\TH:i:s'),
                        'duration_hours' => $slot['from']->diffInHours($slot['until'])
                    ];
                })->toArray()
            ];
            
            // Check if user is in queue
            if ($userId) {
                $queueInfo = $this->conflictService->checkAdvanceBooking($equipment, $startDate, $endDate);
                if ($queueInfo) {
                    $result['queue_position'] = $queueInfo['queue_position'];
                    $result['estimated_available'] = $queueInfo['estimated_available']?->format('Y-m-d\TH:i:s');
                }
            }
            
            return $result;
        }
    }

    /**
     * Check equipment availability for a specific time period
     */
    public function checkEquipmentAvailability($equipmentId, $requestedFrom, $requestedUntil)
    {
        return $this->conflictService->checkAvailability($equipmentId, $requestedFrom, $requestedUntil);
    }
    
    /**
     * Get available time slots for equipment on a specific date
     */
    public function getAvailableSlots($equipmentId, $date, $duration = 2)
    {
        return $this->conflictService->getAvailableSlots($equipmentId, $date, $duration);
    }
    
    /**
     * Get equipment booking calendar for a date range
     */
    public function getEquipmentCalendar($equipmentId, $startDate, $endDate)
    {
        $calendar = [];
        $currentDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        
        while ($currentDate->lessThanOrEqualTo($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            
            // Get bookings for this date
            $bookings = EquipmentRequest::where('equipment_id', $equipmentId)
                ->whereIn('status', [
                    EquipmentRequest::STATUS_APPROVED,
                    EquipmentRequest::STATUS_PENDING
                ])
                ->whereDate('requested_from', $dateStr)
                ->with(['user'])
                ->orderBy('requested_from')
                ->get();
            
            // Get available slots
            $availableSlots = $this->getAvailableSlots($equipmentId, $dateStr);
            
            $calendar[$dateStr] = [
                'date' => $dateStr,
                'bookings' => $bookings,
                'available_slots' => $availableSlots,
                'is_fully_booked' => empty($availableSlots),
                'total_bookings' => $bookings->count()
            ];
            
            $currentDate->addDay();
        }
        
        return $calendar;
    }

    /**
     * Request return of borrowed equipment
     */
    public function requestReturn(EquipmentRequest $request, $userId)
    {
        if ($request->user_id !== $userId) {
            return [
                'success' => false,
                'message' => 'You are not authorized to mark this equipment as returned.'
            ];
        }

        if ($request->status !== EquipmentRequest::STATUS_APPROVED || $request->returned_at !== null) {
            return [
                'success' => false,
                'message' => 'This equipment cannot be marked as returned.'
            ];
        }

        $request->update([
            'return_requested_at' => now(),
        ]);
        
        return [
            'success' => true,
            'message' => 'Return request has been submitted. Please return the equipment to the laboratory.'
        ];
    }

    /**
     * Format activity data for display
     */
    private function formatActivity($request)
    {
        $activityText = '';
        $statusClass = '';
        $detailedStatus = $request->status;
        
        // Determine detailed status for display
        if ($request->returned_at) {
            $detailedStatus = 'returned';
        } elseif ($request->isCheckedOut() && !$request->returned_at) {
            $detailedStatus = 'checked_out';
        }
        
        switch($request->status) {
            case EquipmentRequest::STATUS_PENDING:
                $activityText = "<strong>Equipment Request Submitted</strong> for {$request->equipment->name}";
                $dateRange = Carbon::parse($request->requested_from)->format('M j') . 
                           ' - ' . Carbon::parse($request->requested_until)->format('M j, Y');
                $activityText .= '<br><small class="text-gray-600">Requested period: ' . $dateRange . '</small>';
                $statusClass = 'yellow';
                break;
                
            case EquipmentRequest::STATUS_APPROVED:
                if ($request->returned_at) {
                    // Equipment has been returned
                    $activityText = "<strong>Equipment Returned</strong> - {$request->equipment->name}";
                    $activityText .= '<br><small class="text-green-600">Returned on: ' . 
                                   $request->returned_at->format('M j, Y g:i A') . '</small>';
                    if ($request->return_condition) {
                        $conditionColor = $request->return_condition === 'good' ? 'text-green-600' : 
                                        ($request->return_condition === 'damaged' ? 'text-red-600' : 'text-yellow-600');
                        $activityText .= '<br><small class="' . $conditionColor . '">Return condition: ' . 
                                       ucfirst($request->return_condition) . '</small>';
                    }
                    $detailedStatus = 'returned';
                    $statusClass = 'green';
                } elseif ($request->isCheckedOut()) {
                    // Equipment is currently checked out (borrowed)
                    $activityText = "<strong>Currently Borrowed</strong> - {$request->equipment->category->name} - {$request->equipment->name}";
                    $activityText .= '<br><small class="text-blue-600">Checked out: ' . 
                                   $request->checked_out_at->format('M j, Y g:i A') . '</small>';
                    if ($request->checkedOutBy) {
                        $activityText .= '<br><small class="text-blue-600">Processed by: ' . $request->checkedOutBy->name . '</small>';
                    }
                    $returnDate = Carbon::parse($request->requested_until);
                    $isOverdue = $returnDate->isPast();
                    $returnText = $isOverdue ? 'text-red-600' : 'text-gray-600';
                    $overdueLabel = $isOverdue ? ' (OVERDUE - Please return immediately)' : '';
                    $activityText .= '<br><small class="' . $returnText . '">Return due: ' . 
                                   $returnDate->format('M j, Y g:i A') . $overdueLabel . '</small>';
                    $detailedStatus = 'checked_out';
                    $statusClass = 'blue';
                } else {
                    // Equipment is approved but not yet checked out
                    $activityText = "<strong>Request Approved</strong> for {$request->equipment->category->name} - {$request->equipment->name}";
                    $dateRange = Carbon::parse($request->requested_from)->format('M j') . 
                               ' - ' . Carbon::parse($request->requested_until)->format('M j, Y');
                    $activityText .= '<br><small class="text-green-600">Approved period: ' . $dateRange . '</small>';
                    if ($request->approvedBy) {
                        $activityText .= '<br><small class="text-green-600">Approved by: ' . 
                                       $request->approvedBy->name . ' on ' . 
                                       $request->approved_at->format('M j, Y g:i A') . '</small>';
                    }
                    $activityText .= '<br><small class="text-blue-600">Available for pickup at the laboratory</small>';
                    $statusClass = 'green';
                }
                break;
                
            case EquipmentRequest::STATUS_REJECTED:
                $activityText = "<strong>Request Declined</strong> for {$request->equipment->category->name} - {$request->equipment->name}";
                $dateRange = Carbon::parse($request->requested_from)->format('M j') . 
                           ' - ' . Carbon::parse($request->requested_until)->format('M j, Y');
                $activityText .= '<br><small class="text-gray-600">Requested period: ' . $dateRange . '</small>';
                if ($request->rejectedBy) {
                    $activityText .= '<br><small class="text-red-600">Reviewed by: ' . 
                                   $request->rejectedBy->name . ' on ' . 
                                   $request->rejected_at->format('M j, Y g:i A') . '</small>';
                }
                if ($request->rejection_reason) {
                    $activityText .= '<br><small class="text-red-600">Reason: ' . 
                                   Str::limit($request->rejection_reason, 60) . '</small>';
                }
                $statusClass = 'red';
                break;

            case EquipmentRequest::STATUS_CANCELLED:
                $activityText = "<strong>Request Cancelled</strong> for {$request->equipment->category->name} - {$request->equipment->name}";
                $dateRange = Carbon::parse($request->requested_from)->format('M j') . 
                           ' - ' . Carbon::parse($request->requested_until)->format('M j, Y');
                $activityText .= '<br><small class="text-gray-600">Originally requested: ' . $dateRange . '</small>';
                break;

            case EquipmentRequest::STATUS_RETURNED:
                $activityText = "<strong>Equipment Successfully Returned</strong> - {$request->equipment->category->name} - {$request->equipment->name}";
                $dateRange = Carbon::parse($request->requested_from)->format('M j') . 
                           ' - ' . Carbon::parse($request->requested_until)->format('M j, Y');
                $activityText .= '<br><small class="text-gray-600">Usage period: ' . $dateRange . '</small>';
                break;

            default:
                $activityText = "Status update for {$request->equipment->name}";
                $statusClass = 'gray';
                break;
        }
        
        return [
            'id' => $request->id,
            'time' => $request->updated_at ?? $request->created_at, // Use updated_at for status changes
            'description' => $activityText,
            'status' => $detailedStatus, // Use detailed status for better badge display
            'status_class' => $statusClass,
            'equipment_name' => $request->equipment->name,
            'category_name' => $request->equipment->category->name ?? 'Uncategorized',
            'purpose' => $request->purpose,
            'activity_type' => 'request', // Use 'request' type for proper status badge colors
            'original_status' => $request->status, // Keep original status for reference
            'is_overdue' => $request->status === EquipmentRequest::STATUS_APPROVED && 
                          $request->isCheckedOut() && 
                          !$request->returned_at && 
                          Carbon::parse($request->requested_until)->isPast()
        ];
    }
}
