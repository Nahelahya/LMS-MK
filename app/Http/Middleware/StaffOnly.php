<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StaffOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!in_array(auth()->user()->role, ['admin', 'staff'])) {
            abort(403);
        }
        return $next($request);
    }
}
