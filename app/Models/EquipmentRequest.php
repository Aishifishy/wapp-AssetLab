<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EquipmentRequest extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'user_id',
        'equipment_id',
        'purpose',
        'requested_from',
        'requested_until',
        'status',
        'returned_at',
        'return_condition',
        'return_notes',
        'checked_out_at',
        'checked_out_by',
        'approved_at',
        'rejected_at',
        'approved_by',
        'rejected_by',
        'cancelled_at',
        'rejection_reason',
    ];

    protected $casts = [
        'requested_from' => 'datetime',
        'requested_until' => 'datetime',
        'returned_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_RETURNED = 'returned';
    const STATUS_CHECKED_OUT = 'checked_out';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function user()
    {
        return $this->belongsTo(Ruser::class, 'user_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function checkedOutBy()
    {
        return $this->belongsTo(Radmin::class, 'checked_out_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Radmin::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(Radmin::class, 'rejected_by');
    }

    // Status check methods
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isReturned()
    {
        return !is_null($this->returned_at);
    }

    public function isCheckedOut()
    {
        return !is_null($this->checked_out_at);
    }

    public function isOverdue()
    {
        return $this->isApproved() 
            && !$this->isReturned() 
            && Carbon::now()->isAfter($this->requested_until);
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

    public function scopeActive($query)
    {
        return $query->approved()->whereNull('returned_at');
    }

    public function scopeCheckedOut($query)
    {
        return $query->approved()->whereNotNull('checked_out_at')->whereNull('returned_at');
    }

    public function scopeOverdue($query)
    {
        return $query->active()->where('requested_until', '<', Carbon::now());
    }

    public function scopeReturned($query)
    {
        return $query->whereNotNull('returned_at');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }
} 