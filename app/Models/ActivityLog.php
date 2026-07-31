<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    const UPDATED_AT = null;   // this table has no updated_at column

    protected $fillable = [
        'user_id', 'user_name', 'user_role', 'module', 'action',
        'reference_id', 'description', 'old_values', 'new_values',
        'ip_address', 'user_agent', 'status'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];
}