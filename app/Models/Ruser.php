<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Ruser extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $table = 'rusers';

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\RuserFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'contact_number',
        'rfid_tag',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function isFaculty()
    {
        return $this->role === 'faculty';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }
    
    /**
     * Get the equipment requests for the user.
     */
    public function equipmentRequests()
    {
        return $this->hasMany(EquipmentRequest::class, 'user_id');
    }
    
    /**
     * Get the laboratory reservations for the user.
     */
    public function laboratoryReservations()
    {
        return $this->hasMany(LaboratoryReservation::class, 'user_id');
    }
}
