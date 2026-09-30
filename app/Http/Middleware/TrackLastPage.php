<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackLastPage
{
    /**
     * Remember the last page the logged-in user successfully viewed,
     * so the fallback route and role middleware can send them back to it.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            auth()->check()
            && $request->isMethod('get')
            && ! $request->ajax()
            && ! $request->expectsJson()
            && $response->getStatusCode() === 200
            && str_contains($response->headers->get('Content-Type', ''), 'text/html')
        ) {
            session(['last_page' => $request->fullUrl()]);
        }

        return $response;
    }
}