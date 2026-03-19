<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user login dan punya kolom language, pakai itu
        if (auth()->check() && auth()->user()->language) {
            App::setLocale(auth()->user()->language);
        } 
        // Jika tidak login, cek session (untuk halaman login/register)
        elseif (session()->has('locale')) {
            App::setLocale(session('locale'));
        }

        return $next($request);
    }
}