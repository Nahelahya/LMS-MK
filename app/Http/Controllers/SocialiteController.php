<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    public function redirect()
    {
        // Mengarahkan ke halaman login Google
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Cari user berdasarkan email, kalau tidak ada buat baru
            $user = User::updateOrCreate([
                'email' => $googleUser->getEmail(),
            ], [
                'name' => $googleUser->getName(),
                'provider_id' => $googleUser->getId(),
                'provider_name' => 'google',
                // Password diisi random karena login lewat Google
                'password' => bcrypt(Str::random(24))
            ]);

            Auth::login($user);

            // Arahkan ke dashboard setelah sukses
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            // Jika gagal, balik ke login dengan pesan error
            return redirect()->route('login')->with('error', 'Gagal login menggunakan Google.');
        }
    }
}