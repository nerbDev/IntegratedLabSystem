<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(
        string $module,
        string $action,
        string $description,
        ?array $old = null,
        ?array $new = null,
        ?int $referenceId = null,
        string $status = 'success'
    ) {
        $user = Auth::user();

        ActivityLog::create([
            'user_id'      => $user->id ?? null,
            'user_name'    => $user->name ?? 'System',
            'user_role'    => $user->role ?? 'N/A',
            'module'       => $module,
            'action'       => $action,
            'reference_id' => $referenceId,   // fixed: snake_case to match DB column
            'description'  => $description,
            'old_values'   => $old,
            'new_values'   => $new,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'status'       => $status,
        ]);
    }
}