<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivedAppointmentResult extends Model
{
    public $timestamps = false; // no created_at/updated_at columns — using archived_at instead

    protected $fillable = [
        'archived_appointment_id', 'original_appointment_id', 'file_path',
        'notes', 'status', 'original_created_at', 'original_updated_at', 'archived_at',
    ];
}
