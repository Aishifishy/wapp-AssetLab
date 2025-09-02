<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LaboratoryScheduleOverride extends Model
{
    protected $fillable = [
        'laboratory_schedule_id',
        'laboratory_id',
        'academic_term_id',
        'override_date',
        'override_type',
        'new_start_time',
        'new_end_time',
        'new_subject_code',
        'new_subject_name',
        'new_instructor_name',
        'new_section',
        'new_notes',
        'reason',
        'created_by',
        'requested_by',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'override_date' => 'date',
        'new_start_time' => 'datetime:H:i',
        'new_end_time' => 'datetime:H:i',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Override type constants
    const TYPE_CANCEL = 'cancel';
    const TYPE_RESCHEDULE = 'reschedule';
    const TYPE_REPLACE = 'replace';

    /**
     * Get the original laboratory schedule being overridden.
     */
    public function originalSchedule(): BelongsTo
    {
        return $this->belongsTo(LaboratorySchedule::class, 'laboratory_schedule_id');
    }

    /**
     * Get the laboratory that owns this override.
     */
    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(ComputerLaboratory::class, 'laboratory_id');
    }

    /**
     * Get the academic term that owns this override.
     */
    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class, 'academic_term_id');
    }

    /**
     * Get the admin who created this override.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Radmin::class, 'created_by');
    }

    /**
     * Get the user who requested this override.
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(Ruser::class, 'requested_by');
    }

    /**
     * Scope to get active overrides.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope to get overrides for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('override_date', Carbon::parse($date)->toDateString());
    }

    /**
     * Scope to get overrides for a specific laboratory.
     */
    public function scopeForLaboratory($query, $laboratoryId)
    {
        return $query->where('laboratory_id', $laboratoryId);
    }

    /**
     * Check if this override is currently active.
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Get the effective schedule details (considering override type).
     */
    public function getEffectiveSchedule(): ?array
    {
        if ($this->override_type === self::TYPE_CANCEL) {
            return null; // No schedule on this date
        }

        // For reschedule/replace, return the new schedule details
        if ($this->override_type === self::TYPE_RESCHEDULE || $this->override_type === self::TYPE_REPLACE) {
            return [
                'start_time' => $this->new_start_time,
                'end_time' => $this->new_end_time,
                'subject_code' => $this->new_subject_code ?? $this->originalSchedule?->subject_code,
                'subject_name' => $this->new_subject_name ?? $this->originalSchedule?->subject_name,
                'instructor_name' => $this->new_instructor_name ?? $this->originalSchedule?->instructor_name,
                'section' => $this->new_section ?? $this->originalSchedule?->section,
                'notes' => $this->new_notes ?? $this->originalSchedule?->notes,
                'type' => 'override',
            ];
        }

        return null;
    }

    /**
     * Get display name for override type.
     */
    public function getOverrideTypeNameAttribute(): string
    {
        return match($this->override_type) {
            self::TYPE_CANCEL => 'Cancelled',
            self::TYPE_RESCHEDULE => 'Rescheduled',
            self::TYPE_REPLACE => 'Replaced',
            default => 'Unknown',
        };
    }

    /**
     * Get formatted time range for the override.
     */
    public function getTimeRangeAttribute(): ?string
    {
        if ($this->override_type === self::TYPE_CANCEL) {
            return 'Cancelled';
        }

        if ($this->new_start_time && $this->new_end_time) {
            return $this->new_start_time->format('h:i A') . ' - ' . $this->new_end_time->format('h:i A');
        }

        return null;
    }
}
