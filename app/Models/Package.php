<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = ['name', 'price', 'requires_fasting', 'is_active'];

    protected $casts = [
        'price' => 'decimal:2',
        'requires_fasting' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function inclusions(): HasMany
    {
        return $this->hasMany(PackageInclusion::class)->orderBy('sort_order');
    }
}