<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index', [
            'user' => Auth::user()
        ]);
    }

public function updateProfile(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
    ]);

    $user = auth()->user();

    // upload foto
    if ($request->hasFile('photo')) {

        // hapus lama (optional)
        if ($user->photo) {
            Storage::delete('public/profile/' . $user->photo);
        }

        $file = $request->file('photo');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/profile', $filename);

        $user->photo = $filename;
    }

    $user->name = $request->name;
    $user->save();

    return back()->with('success', 'Profile updated');
}
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:12|confirmed'
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password updated');
    }

    public function updatePreferences(Request $request)
    {
        Auth::user()->update([
            'language' => $request->language
        ]);

        return back()->with('success', 'Preferences updated');
    }
}