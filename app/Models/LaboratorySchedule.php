<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaboratorySchedule extends Model
{
    protected $fillable = [
        'laboratory_id',
        'academic_term_id',
        'subject_code',
        'subject_name',
        'instructor_name',
        'section',
        'day_of_week',
        'start_time',
        'end_time',
        'type',
        'notes',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Constants for days of the week
    const SUNDAY = 0;
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;

    // Constants for schedule types
    const TYPE_REGULAR = 'regular';
    const TYPE_SPECIAL = 'special';

    /**
     * Get the laboratory that owns the schedule.
     */
    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(ComputerLaboratory::class, 'laboratory_id');
    }

    /**
     * Get the academic term that owns the schedule.
     */
    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class, 'academic_term_id');
    }

    /**
     * Get the day name of the schedule.
     */
    public function getDayNameAttribute(): string
    {
        return match($this->day_of_week) {
            self::SUNDAY => 'Sunday',
            self::MONDAY => 'Monday',
            self::TUESDAY => 'Tuesday',
            self::WEDNESDAY => 'Wednesday',
            self::THURSDAY => 'Thursday',
            self::FRIDAY => 'Friday',
            self::SATURDAY => 'Saturday',
            default => 'Unknown',
        };
    }

    /**
     * Get the formatted time range.
     */
    public function getTimeRangeAttribute(): string
    {
        return $this->start_time->format('h:i A') . ' - ' . $this->end_time->format('h:i A');
    }

    /**
     * Check if the schedule conflicts with another schedule.
     */
    public function conflictsWith(LaboratorySchedule $schedule): bool
    {
        if ($this->laboratory_id !== $schedule->laboratory_id ||
            $this->academic_term_id !== $schedule->academic_term_id ||
            $this->day_of_week !== $schedule->day_of_week) {
            return false;
        }

        $thisStart = strtotime($this->start_time);
        $thisEnd = strtotime($this->end_time);
        $otherStart = strtotime($schedule->start_time);
        $otherEnd = strtotime($schedule->end_time);

        return ($thisStart < $otherEnd && $thisEnd > $otherStart);
    }
}
