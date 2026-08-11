<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class ArchivedAppointment extends Model
{
    public $timestamps = false;

    protected $casts = [
        'original_created_at' => 'datetime',
        'original_updated_at' => 'datetime',
        // 'archived_at' removed from here — the accessor below handles it instead
    ];

    protected $fillable = [
        'original_appointment_id', 'patient_id', 'service', 'appointment_type',
        'appointment_date', 'appointment_time', 'first_name', 'middle_name',
        'last_name', 'suffix', 'email', 'phone', 'municipality', 'barangay',
        'street_details', 'landmark', 'status', 'notes',
        'original_created_at', 'original_updated_at', 'archived_at',
    ];

    protected function archivedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->timezone('Asia/Manila'),
        );
    }

    public function result()
    {
        return $this->hasOne(ArchivedAppointmentResult::class, 'archived_appointment_id');
    }
}