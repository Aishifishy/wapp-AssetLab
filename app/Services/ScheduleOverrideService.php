<?php

namespace App\Services;

use App\Models\LaboratorySchedule;
use App\Models\LaboratoryScheduleOverride;
use App\Models\ComputerLaboratory;
use App\Models\AcademicTerm;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ScheduleOverrideService
{
    /**
     * Create a schedule override.
     */
    public function createOverride(array $data): LaboratoryScheduleOverride
    {
        // Validate that the override date matches the day of week for the original schedule
        if (isset($data['laboratory_schedule_id'])) {
            $originalSchedule = LaboratorySchedule::find($data['laboratory_schedule_id']);
            if ($originalSchedule) {
                $overrideDate = Carbon::parse($data['override_date']);
                if ($overrideDate->dayOfWeek !== $originalSchedule->day_of_week) {
                    throw new \InvalidArgumentException('Override date must match the day of week of the original schedule.');
                }
            }
        }

        // Check for existing override on the same date
        $existingOverride = LaboratoryScheduleOverride::where('laboratory_id', $data['laboratory_id'])
            ->where('override_date', $data['override_date'])
            ->where('is_active', true)
            ->first();

        if ($existingOverride) {
            throw new \InvalidArgumentException('An active override already exists for this date.');
        }

        return LaboratoryScheduleOverride::create($data);
    }

    /**
     * Get all active overrides for a laboratory on a specific date.
     */
    public function getActiveOverridesForDate(int $laboratoryId, string $date): Collection
    {
        return LaboratoryScheduleOverride::forLaboratory($laboratoryId)
            ->forDate($date)
            ->active()
            ->with(['originalSchedule', 'createdBy'])
            ->get();
    }

    /**
     * Get effective schedule for a laboratory on a specific date.
     */
    public function getEffectiveScheduleForDate(int $laboratoryId, string $date): Collection
    {
        $dateObj = Carbon::parse($date);
        $dayOfWeek = $dateObj->dayOfWeek;

        // Get all regular schedules for this day of week
        $regularSchedules = LaboratorySchedule::where('laboratory_id', $laboratoryId)
            ->where('day_of_week', $dayOfWeek)
            ->with(['overrides' => function($query) use ($date) {
                $query->forDate($date)->active();
            }])
            ->get();

        $effectiveSchedules = collect();

        foreach ($regularSchedules as $schedule) {
            $override = $schedule->overrides->first();

            if ($override) {
                // Handle override
                if ($override->override_type === LaboratoryScheduleOverride::TYPE_CANCEL) {
                    // Skip cancelled schedules
                    continue;
                }

                $effectiveSchedule = $override->getEffectiveSchedule();
                if ($effectiveSchedule) {
                    $effectiveSchedule['id'] = $schedule->id;
                    $effectiveSchedule['override_id'] = $override->id;
                    $effectiveSchedule['is_override'] = true;
                    $effectiveSchedule['override_type'] = $override->override_type;
                    $effectiveSchedule['override_reason'] = $override->reason;
                    $effectiveSchedules->push((object) $effectiveSchedule);
                }
            } else {
                // Use regular schedule
                $effectiveSchedule = $schedule->getEffectiveScheduleForDate($date);
                $effectiveSchedule['id'] = $schedule->id;
                $effectiveSchedule['is_override'] = false;
                $effectiveSchedules->push((object) $effectiveSchedule);
            }
        }

        // Add standalone overrides (not tied to a regular schedule)
        $standaloneOverrides = LaboratoryScheduleOverride::forLaboratory($laboratoryId)
            ->forDate($date)
            ->active()
            ->whereNull('laboratory_schedule_id')
            ->get();

        foreach ($standaloneOverrides as $override) {
            $effectiveSchedule = $override->getEffectiveSchedule();
            if ($effectiveSchedule) {
                $effectiveSchedule['id'] = null;
                $effectiveSchedule['override_id'] = $override->id;
                $effectiveSchedule['is_override'] = true;
                $effectiveSchedule['override_type'] = $override->override_type;
                $effectiveSchedule['override_reason'] = $override->reason;
                $effectiveSchedules->push((object) $effectiveSchedule);
            }
        }

        return $effectiveSchedules->sortBy('start_time');
    }

    /**
     * Check if a time slot conflicts with any effective schedule.
     */
    public function hasTimeConflict(int $laboratoryId, string $date, string $startTime, string $endTime): bool
    {
        $effectiveSchedules = $this->getEffectiveScheduleForDate($laboratoryId, $date);

        foreach ($effectiveSchedules as $schedule) {
            if ($this->timesOverlap($startTime, $endTime, $schedule->start_time, $schedule->end_time)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update an existing override.
     */
    public function updateOverride(LaboratoryScheduleOverride $override, array $data): LaboratoryScheduleOverride
    {
        $override->update($data);
        return $override->fresh();
    }

    /**
     * Deactivate an override (soft delete).
     */
    public function deactivateOverride(LaboratoryScheduleOverride $override): bool
    {
        return $override->update(['is_active' => false]);
    }

    /**
     * Get all overrides for a laboratory within a date range.
     */
    public function getOverridesInDateRange(int $laboratoryId, string $startDate, string $endDate): Collection
    {
        return LaboratoryScheduleOverride::forLaboratory($laboratoryId)
            ->whereBetween('override_date', [$startDate, $endDate])
            ->with(['originalSchedule', 'createdBy'])
            ->orderBy('override_date')
            ->get();
    }

    /**
     * Bulk create overrides for multiple dates.
     */
    public function bulkCreateOverrides(array $overrides): Collection
    {
        $created = collect();

        foreach ($overrides as $overrideData) {
            try {
                $created->push($this->createOverride($overrideData));
            } catch (\Exception $e) {
                // Log error but continue with other overrides
                Log::error('Failed to create override: ' . $e->getMessage(), $overrideData);
            }
        }

        return $created;
    }

    /**
     * Clean up expired overrides.
     */
    public function cleanupExpiredOverrides(): int
    {
        return LaboratoryScheduleOverride::where('expires_at', '<', now())
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }

    /**
     * Check if two time ranges overlap.
     */
    private function timesOverlap(string $start1, string $end1, string $start2, string $end2): bool
    {
        $start1Time = strtotime($start1);
        $end1Time = strtotime($end1);
        $start2Time = strtotime($start2);
        $end2Time = strtotime($end2);

        return ($start1Time < $end2Time && $end1Time > $start2Time);
    }
}
