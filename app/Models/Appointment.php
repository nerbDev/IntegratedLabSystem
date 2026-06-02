<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments';

    protected $fillable = [
        'patient_id',
        'service',
        'appointment_date',
        'appointment_time',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email',
        'phone',
        'appointment_type',
        'status',
        'municipality',
        'barangay',
        'street_details',
        'landmark',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'string',
    ];

    // Relationship to UserAccount
    public function user()
    {
        return $this->belongsTo(UserAccount::class, 'patient_id', 'id');
    }

    // One appointment has ONE result (the uploaded PDF record)
    public function result()
    {
        return $this->hasOne(AppointmentResult::class, 'appointment_id');
    }

    // One appointment has MANY result rows (structured data)
    public function testResults()
    {
        return $this->hasMany(AppointmentResult::class, 'appointment_id');
    }
}