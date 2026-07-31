<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\SocialAuthController;
use Closure;
use Illuminate\Http\Request;

class EnsureProfileIsComplete
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && !SocialAuthController::profileIsComplete($user)
            && !$request->routeIs('profile.complete')) {
            return redirect()->route('profile.complete');
        }

        return $next($request);
    }
}