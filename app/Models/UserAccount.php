<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserAccount extends Authenticatable
{
    use HasFactory, Notifiable;

    // Table name (optional, Laravel would guess 'useraccounts' otherwise)
    protected $table = 'useraccount';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'role',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'sex',
        'phone_number',
        'email',
        'Umunicipality',
        'Ubarangay',
        'Ustreet_house',
        'contact_person',
        'contact_number',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays (like when sending data to view).
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the full name of the user.
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    /**
     * Check if user is a patient.
     */
    public function isPatient()
    {
        return $this->role === 'patient';
    }

    /**
     * Check if user is a staff.
     */
    public function isStaff()
    {
        return $this->role === 'staff';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Dynamic relationship mapping linking user accounts to lab appointments history table.
     * Maps useraccount.id to your appointments table patient_id field.
     */
    public function appointments(): HasMany
    {
        // Eloquent looks up the default App\Models\Appointment namespace mapping. 
        // Swap out '\App\Models\Appointment' string if your appointment model uses a different name!
        return $this->hasMany('\App\Models\Appointment', 'patient_id', 'id');
    }
}