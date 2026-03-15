<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;   

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
{
    // Jika user belum login, biarkan Laravel menghandle lewat middleware 'auth'
    if (!Auth::check()) {
        return $next($request);
    }
    // HANYA lempar ke OTP jika statusnya beneran masih 'verify'
    // Dan pastikan tidak melempar jika user SUDAH di halaman OTP (biar gak looping)
    if (Auth::user()->status == 'verify' && !$request->is('verify-otp*')) {
        return redirect()->route('otp.index');
    }
    // if active lempar ke dashboard
    return $next($request);
}
}
