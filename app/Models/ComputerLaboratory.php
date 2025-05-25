<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComputerLaboratory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'room_number',
        'building',
        'capacity',
        'number_of_computers',
        'equipment_inventory',
        'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'number_of_computers' => 'integer',
        'equipment_inventory' => 'array',
    ];

    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_IN_USE = 'in_use';
    const STATUS_UNDER_MAINTENANCE = 'under_maintenance';
    const STATUS_RESERVED = 'reserved';

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeInUse($query)
    {
        return $query->where('status', self::STATUS_IN_USE);
    }

    public function scopeUnderMaintenance($query)
    {
        return $query->where('status', self::STATUS_UNDER_MAINTENANCE);
    }

    public function scopeReserved($query)
    {
        return $query->where('status', self::STATUS_RESERVED);
    }

    // Methods
    public function isAvailable()
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isInUse()
    {
        return $this->status === self::STATUS_IN_USE;
    }

    public function isUnderMaintenance()
    {
        return $this->status === self::STATUS_UNDER_MAINTENANCE;
    }

    public function isReserved()
    {
        return $this->status === self::STATUS_RESERVED;
    }

    public function updateStatus($status)
    {
        if (!in_array($status, [
            self::STATUS_AVAILABLE,
            self::STATUS_IN_USE,
            self::STATUS_UNDER_MAINTENANCE,
            self::STATUS_RESERVED,
        ])) {
            throw new \InvalidArgumentException('Invalid status provided');
        }

        $this->update(['status' => $status]);
    }

    /**
     * Get the schedules for the laboratory.
     */
    public function schedules()
    {
        return $this->hasMany(LaboratorySchedule::class, 'laboratory_id');
    }    /**
     * Get the reservations for the laboratory.
     */
    public function reservations()
    {
        return $this->hasMany(LaboratoryReservation::class, 'laboratory_id');
    }

    /**
     * Get the schedules for the current term.
     */
    public function currentTermSchedules()
    {
        return $this->schedules()
            ->whereHas('academicTerm', function($query) {
                $query->where('is_current', true);
            });
    }
} 