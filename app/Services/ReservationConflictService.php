<?php

namespace App\Services;

use App\Models\LaboratoryReservation;
use App\Models\LaboratorySchedule;
use App\Models\AcademicTerm;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ReservationConflictService
{    /**
     * Apply time overlap query constraints to any query builder
     * This centralizes the time conflict detection logic used across all controllers
     *
     * @param Builder $query
     * @param string $startTime
     * @param string $endTime
     * @return Builder
     */
    public static function applyTimeOverlapConstraints(Builder $query, string $startTime, string $endTime): Builder
    {
        return $query->where(function($q) use ($startTime, $endTime) {
            // Case 1: Existing item starts before new item starts but ends after new item starts
            $q->where(function($subQ) use ($startTime) {
                $subQ->where('start_time', '<=', $startTime)
                     ->where('end_time', '>', $startTime);
            })
            // Case 2: Existing item starts before new item ends but ends after new item ends  
            ->orWhere(function($subQ) use ($endTime) {
                $subQ->where('start_time', '<', $endTime)
                     ->where('end_time', '>=', $endTime);
            })
            // Case 3: Existing item is completely within new item time range
            ->orWhere(function($subQ) use ($startTime, $endTime) {
                $subQ->where('start_time', '>=', $startTime)
                     ->where('end_time', '<=', $endTime);
            });
        });
    }

    /**
     * Check for all types of reservation conflicts
     *
     * @param int $laboratoryId
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @param int|null $excludeId
     * @return array
     */
    public function checkConflicts($laboratoryId, $date, $startTime, $endTime, $excludeId = null)
    {
        $conflicts = [
            'has_conflict' => false,
            'conflict_type' => null,
            'conflict_details' => null
        ];

        // Check for single reservation conflicts
        $singleConflict = $this->checkSingleReservationConflict($laboratoryId, $date, $startTime, $endTime, $excludeId);
        if ($singleConflict) {
            $conflicts['has_conflict'] = true;
            $conflicts['conflict_type'] = 'single_reservation';
            $conflicts['conflict_details'] = $singleConflict;
            return $conflicts;
        }

        // Check for recurring reservation conflicts
        $recurringConflict = $this->checkRecurringReservationConflict($laboratoryId, $date, $startTime, $endTime, $excludeId);
        if ($recurringConflict) {
            $conflicts['has_conflict'] = true;
            $conflicts['conflict_type'] = 'recurring_reservation';
            $conflicts['conflict_details'] = $recurringConflict;
            return $conflicts;
        }

        // Check for class schedule conflicts
        $scheduleConflict = $this->checkClassScheduleConflict($laboratoryId, $date, $startTime, $endTime);
        if ($scheduleConflict) {
            $conflicts['has_conflict'] = true;
            $conflicts['conflict_type'] = 'class_schedule';
            $conflicts['conflict_details'] = $scheduleConflict;
            return $conflicts;
        }

        return $conflicts;
    }    /**
     * Check for single reservation conflicts
     */
    private function checkSingleReservationConflict($laboratoryId, $date, $startTime, $endTime, $excludeId = null)
    {
        $query = LaboratoryReservation::where('laboratory_id', $laboratoryId)
            ->where('reservation_date', $date)
            ->where('status', LaboratoryReservation::STATUS_APPROVED);
            
        // Apply centralized time overlap logic
        $query = self::applyTimeOverlapConstraints($query, $startTime, $endTime);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->with('user')->first();
    }

    /**
     * Check for recurring reservation conflicts
     */
    private function checkRecurringReservationConflict($laboratoryId, $date, $startTime, $endTime, $excludeId = null)
    {
        // First convert the date to Carbon instance for easier manipulation
        $checkDate = Carbon::parse($date);
        $dayOfWeek = $checkDate->dayOfWeek;
          // Get all recurring reservations for this laboratory
        $recurringReservations = LaboratoryReservation::where('laboratory_id', $laboratoryId)
            ->where('status', LaboratoryReservation::STATUS_APPROVED)
            ->where('is_recurring', true)
            ->where('recurrence_end_date', '>=', $checkDate);
            
        // Apply centralized time overlap logic
        $recurringReservations = self::applyTimeOverlapConstraints($recurringReservations, $startTime, $endTime)
            ->with('user');
        
        if ($excludeId) {
            $recurringReservations->where('id', '!=', $excludeId);
        }
        
        $recurringReservations = $recurringReservations->get();
        
        // Check each recurring reservation to see if it applies to our check date
        foreach ($recurringReservations as $reservation) {
            $startDate = Carbon::parse($reservation->reservation_date);
            $endDate = Carbon::parse($reservation->recurrence_end_date);
            
            // Skip if check date is before reservation start date
            if ($checkDate->lt($startDate)) {
                continue;
            }
            
            $applies = false;
            
            // Calculate if this recurring reservation applies to our check date
            switch ($reservation->recurrence_pattern) {
                case 'daily':
                    // Every day - direct conflict if within date range
                    $applies = true;
                    break;
                
                case 'weekly':
                    // Same day of week
                    if ($startDate->dayOfWeek === $dayOfWeek) {
                        $applies = true;
                    }
                    break;
                    
                case 'monthly':
                    // Same day of month
                    if ($startDate->day === $checkDate->day) {
                        $applies = true;
                    }
                    break;
            }
            
            if ($applies) {
                return $reservation;
            }
        }
        
        return null;
    }

    /**
     * Check for class schedule conflicts
     */
    private function checkClassScheduleConflict($laboratoryId, $date, $startTime, $endTime)
    {
        $checkDate = Carbon::parse($date);
        $dayOfWeek = $checkDate->dayOfWeek;
        
        // Get current term
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        if (!$currentTerm) {
            return null; // No current term set
        }
        
        // Check if the date falls within the academic term
        $termStart = Carbon::parse($currentTerm->start_date);
        $termEnd = Carbon::parse($currentTerm->end_date);
        
        if ($checkDate->lt($termStart) || $checkDate->gt($termEnd)) {
            return null; // Date is outside current academic term
        }
          // Check for conflicts with laboratory schedules
        $conflictingSchedule = LaboratorySchedule::where('laboratory_id', $laboratoryId)
            ->where('academic_term_id', $currentTerm->id)
            ->where('day_of_week', $dayOfWeek);
            
        // Apply centralized time overlap logic
        $conflictingSchedule = self::applyTimeOverlapConstraints($conflictingSchedule, $startTime, $endTime)
            ->first();
            
        return $conflictingSchedule;
    }
      /**
     * For recurring reservations, check all dates from start to end date
     */
    public function checkRecurringReservationConflicts($laboratoryId, $startDate, $endDate, $startTime, $endTime, 
                                                     $recurrencePattern, $excludeId = null)
    {
        $startDateObj = Carbon::parse($startDate);
        $endDateObj = Carbon::parse($endDate);
        
        $conflicts = [];
        
        // Get current term
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        $termEndDate = $currentTerm ? Carbon::parse($currentTerm->end_date) : null;
        
        // Set up iteration based on recurrence pattern
        $current = $startDateObj->copy();
        
        while ($current->lte($endDateObj)) {
            $dateStr = $current->toDateString();
            
            // Check if this date falls within the current academic term
            $isWithinTerm = true;
            if ($termEndDate && $current->gt($termEndDate)) {
                $isWithinTerm = false;
            }
            
            // Check conflicts for this specific date
            $conflictCheck = $this->checkConflicts($laboratoryId, $dateStr, $startTime, $endTime, $excludeId);
            
            // If there's a conflict or date is outside the term, add to conflicts list
            if ($conflictCheck['has_conflict']) {
                $conflicts[] = [
                    'date' => $dateStr,
                    'conflict_type' => $conflictCheck['conflict_type'],
                    'conflict_details' => $conflictCheck['conflict_details'],
                    'is_within_term' => $isWithinTerm
                ];
            } else if (!$isWithinTerm) {
                // Add a special conflict for dates outside the current term
                $conflicts[] = [
                    'date' => $dateStr,
                    'conflict_type' => 'outside_term',
                    'conflict_details' => [
                        'term_end_date' => $termEndDate->toDateString()
                    ],
                    'is_within_term' => false
                ];
            }
            
            // Move to next occurrence based on pattern
            switch ($recurrencePattern) {
                case 'daily':
                    $current->addDay();
                    break;
                case 'weekly':
                    $current->addWeek();
                    break;
                case 'monthly':
                    $current->addMonth();
                    break;
            }
        }
        
        return $conflicts;
    }
}
