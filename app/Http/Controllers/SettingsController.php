<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete('profile/' . $user->photo);
            }
            $fileName = time() . '_' . $user->id . '.' . $request->photo->extension();
            $request->photo->storeAs('profile', $fileName, 'public');
            $user->photo = $fileName;
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', __('messages.profile_updated'));
    }

    // ✅ Method yang hilang — ini penyebab error 500
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password'  => 'required',
            'password'          => 'required|min:8|confirmed',
        ]);

        // Cek apakah current_password cocok
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => __('messages.password_incorrect'),
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', __('messages.password_updated'));
    }

    public function updatePreferences(Request $request)
    {
        $user = auth()->user();
        $user->language = $request->language;
        $user->save();

        session(['locale' => $request->language]);

        return back()->with('success', 'Language updated!');
    }

    public function deletePhoto()
    {
        $user = auth()->user();

        if ($user->photo) {
            Storage::disk('public')->delete('profile/' . $user->photo);
            $user->photo = null;
            $user->save();
        }

        return back()->with('success', 'Photo deleted!');
    }

    public function updateLanguage(Request $request)
    {
        $request->validate(['language' => 'required|in:id,en']);

        Auth::user()->update(['language' => $request->language]);
        session(['locale' => $request->language]);

        return redirect()->back()->with('success', 'Bahasa berhasil diubah');
    }
}