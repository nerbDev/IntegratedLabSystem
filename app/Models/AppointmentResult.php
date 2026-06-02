<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'category',
        'parameter_name',
        'result_value',
        'unit',
        'reference_range',
        'is_abnormal',
        'file_path',   // ← added
        'notes',       // ← added
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}