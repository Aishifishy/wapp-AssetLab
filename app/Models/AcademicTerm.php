<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'name',
        'term_number',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'term_number' => 'integer',
    ];

    // Relationships
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Scopes
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeActive($query)
    {
        $now = now()->startOfDay();
        return $query->whereDate('start_date', '<=', $now)
                    ->whereDate('end_date', '>=', $now);
    }

    // Methods
    public function markAsCurrent()
    {
        // Remove current flag from other terms in the same academic year
        static::query()
            ->where('academic_year_id', $this->academic_year_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);
        
        $this->update(['is_current' => true]);
        
        // Also mark the academic year as current
        $this->academicYear->markAsCurrent();
    }

    public function isActive()
    {
        $now = now()->startOfDay();
        return $now->between($this->start_date, $this->end_date);
    }

    /**
     * Get the current academic term based on today's date
     */
    public static function getCurrentByDate($date = null)
    {
        $date = $date ? \Carbon\Carbon::parse($date)->startOfDay() : now()->startOfDay();
        
        return static::whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->first();
    }

    /**
     * Automatically set the current academic term based on today's date
     */
    public static function setCurrentByDate($date = null)
    {
        $currentTerm = static::getCurrentByDate($date);
        
        if ($currentTerm) {
            $currentTerm->markAsCurrent();
            return $currentTerm;
        }
        
        return null;
    }

    /**
     * Get the schedules for this term.
     */
    public function schedules()
    {
        return $this->hasMany(LaboratorySchedule::class);
    }
} 