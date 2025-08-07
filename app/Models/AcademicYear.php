<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    // Relationships
    public function terms()
    {
        return $this->hasMany(AcademicTerm::class);
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
        // Remove current flag from other years
        static::query()->where('id', '!=', $this->id)->update(['is_current' => false]);
        $this->update(['is_current' => true]);
    }

    public function isActive()
    {
        $now = now()->startOfDay();
        return $now->between($this->start_date, $this->end_date);
    }

    /**
     * Get the current academic year based on today's date
     */
    public static function getCurrentByDate($date = null)
    {
        $date = $date ? \Carbon\Carbon::parse($date)->startOfDay() : now()->startOfDay();
        
        return static::whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->first();
    }

    /**
     * Automatically set the current academic year based on today's date
     */
    public static function setCurrentByDate($date = null)
    {
        $currentYear = static::getCurrentByDate($date);
        
        if ($currentYear) {
            $currentYear->markAsCurrent();
            return $currentYear;
        }
        
        return null;
    }
} 