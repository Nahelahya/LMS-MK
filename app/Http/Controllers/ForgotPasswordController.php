<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // Tampilkan form input email
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    // Kirim email berisi token/link reset
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $token = Str::random(64);

        // Simpan token ke tabel password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Kirim Email (Pastikan kamu sudah buat Mailable atau pakai Mail biasa)
        Mail::send('auth.emails.password-reset', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password Notification');
        });

        return back()->with('success', 'Kami telah mengirimkan link reset password ke email Anda!');
    }

    // Tampilkan form input password baru
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    // Update password di database
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        $checkToken = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token,
            ])->first();

        if (!$checkToken) {
            return back()->withErrors(['email' => 'Token reset password tidak valid!']);
        }

        // Update Password User
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        // Hapus token agar tidak bisa dipakai lagi
        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return redirect()->route('login')->with('success', 'Password berhasil diubah! Silakan login.');
    }
}