<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        // If user is not logged in, send to login page
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // If user's role does not match what this route needs, deny access
        if (auth()->user()->role !== $role) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}