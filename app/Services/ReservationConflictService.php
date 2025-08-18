<?php

namespace App\Services;

use App\Models\LaboratoryReservation;
use App\Models\LaboratorySchedule;
use App\Models\AcademicTerm;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ReservationConflictService
{
    // Cache properties for performance optimization
    private $cachedReservations;
    private $cachedRecurringReservations;
    private $cachedSchedules;    /**
     * Apply time overlap query constraints to any query builder (Optimized)
     * This centralizes the time conflict detection logic used across all controllers
     *
     * @param Builder $query
     * @param string $startTime
     * @param string $endTime
     * @return Builder
     */
    public static function applyTimeOverlapConstraints(Builder $query, string $startTime, string $endTime): Builder
    {
        // Convert times to comparable format for better database indexing
        $startTimeFormatted = Carbon::createFromFormat('H:i', $startTime)->format('H:i:s');
        $endTimeFormatted = Carbon::createFromFormat('H:i', $endTime)->format('H:i:s');
        
        return $query->where(function($q) use ($startTimeFormatted, $endTimeFormatted) {
            // Optimized overlap detection - check if times overlap
            $q->where(function($subQ) use ($startTimeFormatted) {
                // Case 1: Existing item starts before new item starts but ends after new item starts
                $subQ->where('start_time', '<=', $startTimeFormatted)
                     ->where('end_time', '>', $startTimeFormatted);
            })
            ->orWhere(function($subQ) use ($endTimeFormatted) {
                // Case 2: Existing item starts before new item ends but ends after new item ends  
                $subQ->where('start_time', '<', $endTimeFormatted)
                     ->where('end_time', '>=', $endTimeFormatted);
            })
            ->orWhere(function($subQ) use ($startTimeFormatted, $endTimeFormatted) {
                // Case 3: Existing item is completely within new item time range
                $subQ->where('start_time', '>=', $startTimeFormatted)
                     ->where('end_time', '<=', $endTimeFormatted);
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
     * Check for recurring reservation conflicts (Optimized)
     */
    private function checkRecurringReservationConflict($laboratoryId, $date, $startTime, $endTime, $excludeId = null)
    {
        // Convert the date to Carbon instance for easier manipulation
        $checkDate = Carbon::parse($date);
        $dayOfWeek = $checkDate->dayOfWeek;
        
        // Optimized query with better filtering
        $query = LaboratoryReservation::where('laboratory_id', $laboratoryId)
            ->where('status', LaboratoryReservation::STATUS_APPROVED)
            ->where('is_recurring', true)
            ->where('reservation_date', '<=', $checkDate) // Start date must be before or on check date
            ->where('recurrence_end_date', '>=', $checkDate); // End date must be after or on check date
            
        // Apply centralized time overlap logic early
        $query = self::applyTimeOverlapConstraints($query, $startTime, $endTime);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        // Use select to only get needed fields for better performance
        $recurringReservations = $query->select([
            'id', 'user_id', 'reservation_date', 'recurrence_end_date', 
            'recurrence_pattern', 'start_time', 'end_time'
        ])->with('user:id,name,email')->get();
        
        // Check each recurring reservation to see if it applies to our check date
        foreach ($recurringReservations as $reservation) {
            if ($this->doesRecurringReservationApply($reservation, $checkDate)) {
                return $reservation;
            }
        }
        
        return null;
    }

    /**
     * Check if a recurring reservation applies to a specific date (Optimized logic)
     */
    private function doesRecurringReservationApply($reservation, $checkDate)
    {
        $startDate = Carbon::parse($reservation->reservation_date);
        $dayOfWeek = $checkDate->dayOfWeek;
        
        // Pre-filter: check date must be within the recurring period
        if ($checkDate->lt($startDate)) {
            return false;
        }
        
        // Calculate if this recurring reservation applies to our check date
        switch ($reservation->recurrence_pattern) {
            case 'daily':
                // Every day - applies if within date range (already filtered by query)
                return true;
                
            case 'weekly':
                // Same day of week
                return $startDate->dayOfWeek === $dayOfWeek;
                
            case 'monthly':
                // Same day of month (handle month-end edge cases)
                $targetDay = $startDate->day;
                $checkMonth = $checkDate->month;
                $checkYear = $checkDate->year;
                
                // Handle cases where the target day doesn't exist in the check month
                $lastDayOfMonth = Carbon::create($checkYear, $checkMonth)->endOfMonth()->day;
                if ($targetDay > $lastDayOfMonth) {
                    // If target day is beyond the month, use the last day of the month
                    return $checkDate->day === $lastDayOfMonth;
                }
                
                return $checkDate->day === $targetDay;
                
            default:
                return false;
        }
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
     * For recurring reservations, check all dates from start to end date (Optimized)
     */
    public function checkRecurringReservationConflicts($laboratoryId, $startDate, $endDate, $startTime, $endTime, 
                                                     $recurrencePattern, $excludeId = null)
    {
        $startDateObj = Carbon::parse($startDate);
        $endDateObj = Carbon::parse($endDate);
        
        $conflicts = [];
        
        // Get current term once for efficiency
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        $termEndDate = $currentTerm ? Carbon::parse($currentTerm->end_date) : null;
        
        // Pre-fetch all potentially conflicting data for the entire date range
        $this->prefetchConflictData($laboratoryId, $startDateObj, $endDateObj, $startTime, $endTime, $excludeId);
        
        // Calculate all dates in the recurring pattern
        $datesToCheck = $this->generateRecurrenceDates($startDateObj, $endDateObj, $recurrencePattern);
        
        // Check conflicts for all dates at once (batch processing)
        foreach ($datesToCheck as $dateToCheck) {
            $dateStr = $dateToCheck->toDateString();
            
            // Check if this date falls within the current academic term
            $isWithinTerm = !$termEndDate || $dateToCheck->lte($termEndDate);
            
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
        }
        
        return $conflicts;
    }

    /**
     * Generate all dates that match the recurrence pattern (Optimized)
     */
    private function generateRecurrenceDates($startDate, $endDate, $recurrencePattern)
    {
        $dates = [];
        $current = $startDate->copy();
        $maxIterations = 1000; // Safety limit to prevent infinite loops
        $iteration = 0;
        
        while ($current->lte($endDate) && $iteration < $maxIterations) {
            $dates[] = $current->copy();
            
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
                default:
                    // Invalid pattern, break to prevent infinite loop
                    break 2;
            }
            
            $iteration++;
        }
        
        return $dates;
    }

    /**
     * Pre-fetch conflict data for better performance (Cache-like approach)
     */
    private function prefetchConflictData($laboratoryId, $startDate, $endDate, $startTime, $endTime, $excludeId = null)
    {
        // Pre-fetch existing reservations in the date range
        $this->cachedReservations = LaboratoryReservation::where('laboratory_id', $laboratoryId)
            ->where('status', LaboratoryReservation::STATUS_APPROVED)
            ->whereBetween('reservation_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($excludeId, function($query) use ($excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->select(['id', 'reservation_date', 'start_time', 'end_time', 'user_id', 'is_recurring'])
            ->get()
            ->groupBy('reservation_date');
            
        // Pre-fetch recurring reservations that might affect this range
        $this->cachedRecurringReservations = LaboratoryReservation::where('laboratory_id', $laboratoryId)
            ->where('status', LaboratoryReservation::STATUS_APPROVED)
            ->where('is_recurring', true)
            ->where('reservation_date', '<=', $endDate)
            ->where('recurrence_end_date', '>=', $startDate)
            ->when($excludeId, function($query) use ($excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->select(['id', 'reservation_date', 'recurrence_end_date', 'recurrence_pattern', 'start_time', 'end_time', 'user_id'])
            ->get();
            
        // Pre-fetch laboratory schedules
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        if ($currentTerm) {
            $this->cachedSchedules = LaboratorySchedule::where('laboratory_id', $laboratoryId)
                ->where('academic_term_id', $currentTerm->id)
                ->select(['id', 'day_of_week', 'start_time', 'end_time'])
                ->get()
                ->groupBy('day_of_week');
        } else {
            $this->cachedSchedules = collect();
        }
    }
}
