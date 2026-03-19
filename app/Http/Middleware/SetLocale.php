<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        // Ambil bahasa dari user yang login, atau dari session
        if (Auth::check() && Auth::user()->language) {
            App::setLocale(Auth::user()->language);
        } elseif (session()->has('locale')) {
            App::setLocale(session('locale'));
        }

        return $next($request);
    }
}