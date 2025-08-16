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