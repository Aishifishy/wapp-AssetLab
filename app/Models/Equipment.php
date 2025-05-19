<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'rfid_tag',
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
} 