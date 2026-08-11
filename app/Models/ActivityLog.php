<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

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

        protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->timezone('Asia/Manila'),
        );
    }
}