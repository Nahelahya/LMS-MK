<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class GithubController extends Controller
{
    // Redirect ke halaman login GitHub
    public function redirect()
    {
        return Socialite::driver('github')->redirect();
    }

    // Callback setelah login berhasil
    public function callback(Request $request)
    {
    // Kalau user cancel / deny di GitHub
    if ($request->has('error')) {
        return redirect()->route('login')->with('error', 'Login dengan GitHub dibatalkan.');
    }

    try {
        $githubUser = Socialite::driver('github')->user();

        $user = User::updateOrCreate(
            ['email' => $githubUser->getEmail()],
            [
                'name'      => $githubUser->getName() ?? $githubUser->getNickname(),
                'github_id' => $githubUser->getId(),
                'avatar'    => $githubUser->getAvatar(),
                'password'  => bcrypt(\Str::random(24)),
            ]
        );

        Auth::login($user);

        return redirect('/dashboard');

    } catch (\Exception $e) {
        return redirect()->route('login')->with('error', 'Gagal login dengan GitHub, coba lagi.');
        }
    }
}