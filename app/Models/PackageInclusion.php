<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageInclusion extends Model
{
    protected $fillable = ['package_id', 'item_name', 'sort_order'];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}