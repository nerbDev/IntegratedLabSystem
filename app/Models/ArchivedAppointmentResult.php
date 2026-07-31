<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivedAppointmentResult extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'archived_appointment_id', 'original_appointment_id', 'file_path',
        'notes', 'status', 'original_created_at', 'original_updated_at', 'archived_at',
    ];
}