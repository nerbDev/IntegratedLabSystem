<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivedAppointment extends Model
{
    const UPDATED_AT = null; // no updates expected once archived

    protected $fillable = [
        'original_appointment_id', 'patient_id', 'service', 'appointment_type',
        'appointment_date', 'appointment_time', 'first_name', 'middle_name',
        'last_name', 'suffix', 'email', 'phone', 'municipality', 'barangay',
        'street_details', 'landmark', 'status', 'notes',
        'original_created_at', 'original_updated_at', 'archived_at',
    ];

    public function result()
    {
        return $this->hasOne(ArchivedAppointmentResult::class, 'archived_appointment_id');
    }
}
