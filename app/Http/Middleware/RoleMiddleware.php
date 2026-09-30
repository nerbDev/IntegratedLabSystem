<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Usage: role:admin  or  role:admin,staff
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Not logged in -> login page
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Role allowed -> continue
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Role not allowed -> stay on the last page (no 403)
        $last = session('last_page');

        if ($last && $last !== $request->fullUrl()) {
            return redirect($last);
        }

        // No last page yet -> send to their own dashboard
        return match ($user->role) {
            'admin'   => redirect()->route('admindashboard'),
            'staff'   => redirect()->route('staffdashboard'),
            'patient' => redirect()->route('patientdashboard'),
            default   => redirect()->route('welcome'),
        };
    }
}