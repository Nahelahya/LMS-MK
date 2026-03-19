<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\OTPEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $otpCode = rand(100000, 999999);
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'student',
            'status'   => 'verify',
            'otp'      => $otpCode,
        ]);

        Mail::to($user->email)->send(new OTPEmail($otpCode));
        Auth::login($user);

        return redirect()->route('otp.index')->with('success', 'Akun berhasil dibuat! Silakan masukkan kode OTP dari email.');
    }

    // ── Register Staff (pakai invite code) ────────────────────────────
    public function showRegisterStaff()
    {
        return view('auth.register.staff');
    }

    public function registerStaff(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|unique:users',
            'password'    => 'required|string|min:8|confirmed',
            'invite_code' => 'required|string',
        ]);

        // Cek invite code dari .env
        if ($request->invite_code !== config('app.staff_invite_code')) {
            return back()->withErrors([
                'invite_code' => 'Kode undangan tidak valid.',
            ])->withInput();
        }

        $otpCode = rand(100000, 999999);
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'staff',
            'status'   => 'verify',
            'otp'      => $otpCode,
        ]);

        Mail::to($user->email)->send(new OTPEmail($otpCode));
        Auth::login($user);

        return redirect()->route('otp.index')->with('success', 'Akun staff berhasil dibuat! Silakan masukkan kode OTP dari email.');
    }

    // ─────────────────────────────────────────────────────────────────
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|string',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            if ($request->has('remember')) {
                Cookie::queue('user_email', $request->email, 1440);
            } else {
                Cookie::queue(Cookie::forget('user_email'));
            }

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Nama atau Password salah.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}