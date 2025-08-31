<?php

namespace App\Services;

use App\Models\EquipmentRequest;
use App\Models\Equipment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class EquipmentConflictService
{
    /**
     * Check if equipment is available for the requested time period
     *
     * @param int $equipmentId
     * @param string $requestedFrom
     * @param string $requestedUntil
     * @param int|null $excludeRequestId
     * @return array
     */
    public function checkAvailability($equipmentId, $requestedFrom, $requestedUntil, $excludeRequestId = null)
    {
        $startTime = Carbon::parse($requestedFrom);
        $endTime = Carbon::parse($requestedUntil);
        
        // Check for overlapping approved requests
        $conflictingRequests = $this->getConflictingRequests($equipmentId, $startTime, $endTime, $excludeRequestId);
        
        if ($conflictingRequests->count() > 0) {
            return [
                'available' => false,
                'conflict_type' => 'existing_booking',
                'conflicting_requests' => $conflictingRequests,
                'next_available_time' => $this->getNextAvailableTime($equipmentId, $startTime),
                'message' => 'Equipment is already booked during this time period.'
            ];
        }
        
        return [
            'available' => true,
            'message' => 'Equipment is available for this time period.'
        ];
    }
    
    /**
     * Get conflicting equipment requests for a time period
     *
     * @param int $equipmentId
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param int|null $excludeRequestId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getConflictingRequests($equipmentId, Carbon $startTime, Carbon $endTime, $excludeRequestId = null)
    {
        $query = EquipmentRequest::where('equipment_id', $equipmentId)
            ->whereIn('status', [
                EquipmentRequest::STATUS_APPROVED,
                EquipmentRequest::STATUS_PENDING
            ])
            ->whereNull('returned_at');
            
        if ($excludeRequestId) {
            $query->where('id', '!=', $excludeRequestId);
        }
        
        // Apply time overlap constraints
        $query->where(function($q) use ($startTime, $endTime) {
            $q->where(function($subQ) use ($startTime) {
                // Existing booking starts before requested time but ends after requested start
                $subQ->where('requested_from', '<=', $startTime)
                     ->where('requested_until', '>', $startTime);
            })
            ->orWhere(function($subQ) use ($endTime) {
                // Existing booking starts before requested end but ends after requested end
                $subQ->where('requested_from', '<', $endTime)
                     ->where('requested_until', '>=', $endTime);
            })
            ->orWhere(function($subQ) use ($startTime, $endTime) {
                // Existing booking is completely within requested time range
                $subQ->where('requested_from', '>=', $startTime)
                     ->where('requested_until', '<=', $endTime);
            });
        });
        
        return $query->with(['user'])->get();
    }
    
    /**
     * Get the next available time for equipment after a specific time
     *
     * @param int $equipmentId
     * @param Carbon $afterTime
     * @return Carbon|null
     */
    public function getNextAvailableTime($equipmentId, Carbon $afterTime)
    {
        $futureBookings = EquipmentRequest::where('equipment_id', $equipmentId)
            ->whereIn('status', [
                EquipmentRequest::STATUS_APPROVED,
                EquipmentRequest::STATUS_PENDING
            ])
            ->whereNull('returned_at')
            ->where('requested_from', '>=', $afterTime)
            ->orderBy('requested_from')
            ->first();
            
        if (!$futureBookings) {
            // No future bookings, available immediately after current time
            return $afterTime;
        }
        
        // Find gaps between bookings
        $currentBookings = EquipmentRequest::where('equipment_id', $equipmentId)
            ->whereIn('status', [
                EquipmentRequest::STATUS_APPROVED,
                EquipmentRequest::STATUS_PENDING
            ])
            ->whereNull('returned_at')
            ->where('requested_until', '>', $afterTime)
            ->orderBy('requested_from')
            ->get();
            
        $lastEndTime = $afterTime;
        
        foreach ($currentBookings as $booking) {
            $bookingStart = Carbon::parse($booking->requested_from);
            $bookingEnd = Carbon::parse($booking->requested_until);
            
            // Check if there's a gap between last end time and this booking start
            if ($bookingStart->greaterThan($lastEndTime)) {
                return $lastEndTime;
            }
            
            $lastEndTime = $bookingEnd->greaterThan($lastEndTime) ? $bookingEnd : $lastEndTime;
        }
        
        return $lastEndTime;
    }
    
    /**
     * Get available time slots for equipment on a specific date
     *
     * @param int $equipmentId
     * @param string $date (Y-m-d format)
     * @param int $slotDurationHours
     * @return array
     */
    public function getAvailableSlots($equipmentId, $date, $slotDurationHours = 2)
    {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();
        
        // Get all bookings for this date
        $dayBookings = EquipmentRequest::where('equipment_id', $equipmentId)
            ->whereIn('status', [
                EquipmentRequest::STATUS_APPROVED,
                EquipmentRequest::STATUS_PENDING
            ])
            ->whereNull('returned_at')
            ->whereDate('requested_from', $date)
            ->orderBy('requested_from')
            ->get();
            
        $availableSlots = [];
        $currentTime = $startOfDay->copy()->setHour(8); // Start from 8 AM
        $dayEnd = $startOfDay->copy()->setHour(18); // End at 6 PM
        
        foreach ($dayBookings as $booking) {
            $bookingStart = Carbon::parse($booking->requested_from);
            $bookingEnd = Carbon::parse($booking->requested_until);
            
            // Add available slots before this booking
            while ($currentTime->copy()->addHours($slotDurationHours)->lessThanOrEqualTo($bookingStart)) {
                $slotEnd = $currentTime->copy()->addHours($slotDurationHours);
                $availableSlots[] = [
                    'start' => $currentTime->format('Y-m-d H:i:s'),
                    'end' => $slotEnd->format('Y-m-d H:i:s'),
                    'duration' => $slotDurationHours . ' hours'
                ];
                $currentTime->addHours($slotDurationHours);
            }
            
            // Move current time to end of this booking
            if ($bookingEnd->greaterThan($currentTime)) {
                $currentTime = $bookingEnd->copy();
            }
        }
        
        // Add remaining slots after last booking
        while ($currentTime->copy()->addHours($slotDurationHours)->lessThanOrEqualTo($dayEnd)) {
            $slotEnd = $currentTime->copy()->addHours($slotDurationHours);
            $availableSlots[] = [
                'start' => $currentTime->format('Y-m-d H:i:s'),
                'end' => $slotEnd->format('Y-m-d H:i:s'),
                'duration' => $slotDurationHours . ' hours'
            ];
            $currentTime->addHours($slotDurationHours);
        }
        
        return $availableSlots;
    }
    
    /**
     * Check if equipment can be advance booked (queue position)
     *
     * @param int $equipmentId
     * @param string $requestedFrom
     * @param string $requestedUntil
     * @return array
     */
    public function checkAdvanceBooking($equipmentId, $requestedFrom, $requestedUntil)
    {
        $availability = $this->checkAvailability($equipmentId, $requestedFrom, $requestedUntil);
        
        if ($availability['available']) {
            return [
                'can_book' => true,
                'booking_type' => 'immediate',
                'message' => 'Equipment is available for immediate booking.'
            ];
        }
        
        // Equipment is not available, but can be advance booked
        $queuePosition = $this->getQueuePosition($equipmentId, $requestedFrom);
        $nextAvailable = $availability['next_available_time'] ?? null;
        
        return [
            'can_book' => true,
            'booking_type' => 'advance',
            'queue_position' => $queuePosition,
            'next_available_time' => $nextAvailable,
            'conflicting_requests' => $availability['conflicting_requests'] ?? [],
            'message' => "Equipment is currently booked. You can advance book and will be position #{$queuePosition} in the queue."
        ];
    }
    
    /**
     * Get alternative time suggestions when equipment is not available
     *
     * @param Equipment $equipment
     * @param Carbon $requestedFrom
     * @param Carbon $requestedUntil
     * @return array
     */
    public function getAlternativeSuggestions($equipment, Carbon $requestedFrom, Carbon $requestedUntil)
    {
        $suggestions = [];
        $duration = $requestedFrom->diffInHours($requestedUntil);
        $startDate = $requestedFrom->copy()->startOfDay();
        $maxDate = $requestedFrom->copy()->addDays(7); // Look for alternatives within a week
        
        $currentDate = $startDate;
        
        while ($currentDate->lessThanOrEqualTo($maxDate) && count($suggestions) < 3) {
            $availableSlots = $this->getAvailableSlots($equipment->id, $currentDate->format('Y-m-d'), $duration);
            
            foreach ($availableSlots as $slot) {
                $slotStart = Carbon::parse($slot['start']);
                $slotEnd = Carbon::parse($slot['end']);
                
                // Only suggest if it's after the originally requested time
                if ($slotStart->greaterThan($requestedFrom)) {
                    $suggestions[] = [
                        'from' => $slotStart->format('Y-m-d\TH:i:s'),
                        'until' => $slotEnd->format('Y-m-d\TH:i:s'),
                        'duration_hours' => $duration
                    ];
                    
                    if (count($suggestions) >= 3) {
                        break;
                    }
                }
            }
            
            $currentDate->addDay();
        }
        
        return $suggestions;
    }
    
    /**
     * Check if user can join queue for advance booking
     *
     * @param Equipment $equipment
     * @param Carbon $requestedFrom
     * @param Carbon $requestedUntil
     * @return bool
     */
    public function canJoinQueue($equipment, Carbon $requestedFrom, Carbon $requestedUntil)
    {
        // Check if equipment exists and is borrowable
        if ($equipment->status !== Equipment::STATUS_AVAILABLE) {
            return false;
        }
        
        // Check if the requested time is in the future
        if ($requestedFrom->isPast()) {
            return false;
        }
        
        // Check if the duration is reasonable (not more than 7 days)
        $duration = $requestedFrom->diffInDays($requestedUntil);
        if ($duration > 7) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get queue position for advance booking
     *
     * @param int $equipmentId
     * @param string $requestedFrom
     * @return int
     */
    public function getQueuePosition($equipmentId, $requestedFrom)
    {
        $requestTime = Carbon::parse($requestedFrom);
        
        // Count pending requests for the same equipment that are requested for earlier times
        $earlierRequests = EquipmentRequest::where('equipment_id', $equipmentId)
            ->where('status', EquipmentRequest::STATUS_PENDING)
            ->where('requested_from', '<=', $requestTime)
            ->count();
            
        return $earlierRequests + 1;
    }
}
