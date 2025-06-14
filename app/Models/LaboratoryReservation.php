<?php

namespace App\Models;

use App\Services\ReservationConflictService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratoryReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'laboratory_id',
        'reservation_date',
        'start_time',
        'end_time',
        'status',
        'purpose',
        'rejection_reason',
        'num_students',
        'course_code',
        'subject',
        'section',
        'is_recurring',
        'recurrence_pattern',
        'recurrence_end_date',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_recurring' => 'boolean',
        'recurrence_end_date' => 'date',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function user()
    {
        return $this->belongsTo(Ruser::class, 'user_id');
    }

    public function laboratory()
    {
        return $this->belongsTo(ComputerLaboratory::class, 'laboratory_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('reservation_date', '>=', now()->toDateString())
                     ->where('status', self::STATUS_APPROVED)
                     ->orderBy('reservation_date')
                     ->orderBy('start_time');
    }    public function isConflicting($query, $laboratoryId, $date, $startTime, $endTime, $excludeId = null)
    {
        $query = $query->where('laboratory_id', $laboratoryId)
            ->where('reservation_date', $date)
            ->where('status', self::STATUS_APPROVED);
            
        // Apply centralized time overlap logic
        $query = ReservationConflictService::applyTimeOverlapConstraints($query, $startTime, $endTime);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query;
    }

    public function getDurationAttribute()
    {
        $startTime = new \DateTime($this->start_time);
        $endTime = new \DateTime($this->end_time);
        $interval = $startTime->diff($endTime);
        $hours = $interval->format('%h');
        $minutes = $interval->format('%i');
        
        return $hours > 0 ? "$hours hr $minutes min" : "$minutes min";
    }
    
    /**
     * Check for conflicts with recurring reservations
     * 
     * @param string $laboratoryId
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @param int|null $excludeId
     * @return bool
     */
    public static function hasRecurringConflict($laboratoryId, $date, $startTime, $endTime, $excludeId = null)
    {
        // First convert the date to Carbon instance
        $checkDate = \Carbon\Carbon::parse($date);
        $dayOfWeek = $checkDate->dayOfWeek;
          // Get all recurring reservations for this laboratory
        $recurringReservations = self::where('laboratory_id', $laboratoryId)
            ->where('status', self::STATUS_APPROVED)
            ->where('is_recurring', true)
            ->where('recurrence_end_date', '>=', $checkDate);
            
        // Apply centralized time overlap logic
        $recurringReservations = ReservationConflictService::applyTimeOverlapConstraints(
            $recurringReservations, $startTime, $endTime
        );
        
        if ($excludeId) {
            $recurringReservations->where('id', '!=', $excludeId);
        }
        
        $recurringReservations = $recurringReservations->get();
        
        // Check each recurring reservation to see if it applies to our check date
        foreach ($recurringReservations as $reservation) {
            $startDate = \Carbon\Carbon::parse($reservation->reservation_date);
            $endDate = \Carbon\Carbon::parse($reservation->recurrence_end_date);
            
            // Skip if check date is before reservation start date
            if ($checkDate->lt($startDate)) {
                continue;
            }
            
            // Calculate if this recurring reservation applies to our check date
            switch ($reservation->recurrence_pattern) {
                case 'daily':
                    // Every day - direct conflict if within date range
                    return true;
                
                case 'weekly':
                    // Same day of week
                    if ($startDate->dayOfWeek === $dayOfWeek) {
                        return true;
                    }
                    break;
                    
                case 'monthly':
                    // Same day of month
                    if ($startDate->day === $checkDate->day) {
                        return true;
                    }
                    break;
            }
        }
        
        return false;
    }
}
