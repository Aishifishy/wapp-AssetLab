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

    // Methods
    public function markAsCurrent()
    {
        // Remove current flag from other terms in the same academic year
        static::query()
            ->where('academic_year_id', $this->academic_year_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);
        
        $this->update(['is_current' => true]);
    }

    public function isActive()
    {
        $now = now()->startOfDay();
        return $now->between($this->start_date, $this->end_date);
    }

    /**
     * Get the schedules for this term.
     */
    public function schedules()
    {
        return $this->hasMany(LaboratorySchedule::class);
    }
} 