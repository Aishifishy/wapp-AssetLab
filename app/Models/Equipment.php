<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'name',
        'description',
        'rfid_tag', // Keep for backward compatibility during transition
        'barcode',
        'category_id',
        'status',
        'current_borrower_id',
    ];

    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_BORROWED = 'borrowed';
    const STATUS_UNAVAILABLE = 'unavailable';

    // Relationships
    public function borrowRequests()
    {
        return $this->hasMany(EquipmentRequest::class);
    }

    public function currentBorrower()
    {
        return $this->belongsTo(Ruser::class, 'current_borrower_id');
    }

    public function category()
    {
        return $this->belongsTo(EquipmentCategory::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeBorrowed($query)
    {
        return $query->where('status', self::STATUS_BORROWED);
    }

    public function scopeUnavailable($query)
    {
        return $query->where('status', self::STATUS_UNAVAILABLE);
    }

    // Methods
    public function isAvailable()
    {
        return $this->status === self::STATUS_AVAILABLE;
    }
    
    /**
     * Check if equipment is available for a specific time period
     * This method considers time-based availability for advance booking
     */
    public function isAvailableForPeriod($startTime, $endTime)
    {
        // Equipment must not be permanently unavailable
        if ($this->status === self::STATUS_UNAVAILABLE) {
            return false;
        }
        
        // Check for conflicting approved requests during this time period
        $conflicts = $this->borrowRequests()
            ->whereIn('status', [
                EquipmentRequest::STATUS_APPROVED,
                EquipmentRequest::STATUS_PENDING
            ])
            ->whereNull('returned_at')
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($q) use ($startTime) {
                    $q->where('requested_from', '<=', $startTime)
                      ->where('requested_until', '>', $startTime);
                })
                ->orWhere(function($q) use ($endTime) {
                    $q->where('requested_from', '<', $endTime)
                      ->where('requested_until', '>=', $endTime);
                })
                ->orWhere(function($q) use ($startTime, $endTime) {
                    $q->where('requested_from', '>=', $startTime)
                      ->where('requested_until', '<=', $endTime);
                });
            })
            ->exists();
            
        return !$conflicts;
    }

    public function isBorrowed()
    {
        return $this->status === self::STATUS_BORROWED;
    }

    public function isUnavailable()
    {
        return $this->status === self::STATUS_UNAVAILABLE;
    }

    /**
     * Get the primary identification code (barcode preferred, RFID as fallback)
     */
    public function getIdentificationCode()
    {
        return $this->barcode ?? $this->rfid_tag;
    }

    /**
     * Get identification label for display
     */
    public function getIdentificationLabel()
    {
        return $this->barcode ? 'Barcode' : 'RFID Tag (Legacy)';
    }

    /**
     * Generate a unique barcode for equipment
     */
    public static function generateBarcode($prefix = 'EQP')
    {
        do {
            $barcode = $prefix . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('barcode', $barcode)->exists());
        
        return $barcode;
    }
} 