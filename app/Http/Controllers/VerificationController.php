<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function index()
    {
        return view('auth.otp');
    }

    // PASTIKAN NAMA FUNGSI INI ADALAH 'verify'
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $user = User::find(Auth::id());

        if ($request->otp == $user->otp) {
            $user->update([
                'status' => 'active',
                'otp' => null,
            ]);
            auth::login($user);
            
            return redirect()->route('dashboard')->with('success', 'Akun aktif!');
        }

        return back()->with('error', 'Kode OTP salah.');
    }
}