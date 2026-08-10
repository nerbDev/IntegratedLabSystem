<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnavailableDate extends Model
{
    protected $fillable = ['date', 'reason', 'created_by'];

    protected $casts = [
        'date' => 'date',
    ];

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString())->orderBy('date');
    }
}