<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckApproved
{
    public function handle(Request $request, Closure $next): mixed
    {
        // This only matters for shopkeepers
        if (auth()->check() && auth()->user()->isShopkeeper()) {
            if (!auth()->user()->isApproved()) {
                // Send them to a "waiting for approval" page
                return redirect()->route('vendor.pending');
            }
        }

        return $next($request);
    }
}