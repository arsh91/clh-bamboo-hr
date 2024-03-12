<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
            // Check if the authenticated user is an admin
            if ($request->user() && $request->user()->role->name == "SUPER_ADMIN") {
                return $next($request);
            }

            // Redirect or show an error message if the user is not authorized
            abort(403);

    }
}
