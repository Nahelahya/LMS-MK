<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role !== 'student') {
            abort(403);
        }
        return $next($request);
    }
}
