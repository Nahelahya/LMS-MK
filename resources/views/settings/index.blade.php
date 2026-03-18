@extends('layouts.dash')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <h1 class="text-2xl font-bold">⚙️ Settings</h1>

    {{-- PROFILE --}}
    <form action="{{ route('settings.profile') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow">
        @csrf
        <h2 class="font-semibold mb-4">Profile</h2>

        <input type="text" name="name" value="{{ $user->name }}" class="w-full mb-3 border p-2 rounded">
        <input type="email" name="email" value="{{ $user->email }}" class="w-full mb-3 border p-2 rounded">

        <input type="file" name="photo" class="mb-3">

        <button class="bg-blue-500 text-white px-4 py-2 rounded">Update Profile</button>
    </form>

    {{-- PASSWORD --}}
    <form action="{{ route('settings.password') }}" method="POST" class="bg-white p-6 rounded-xl shadow">
        @csrf
        <h2 class="font-semibold mb-4">Change Password</h2>

        <input type="password" name="password" placeholder="New Password" class="w-full mb-3 border p-2 rounded">
        <input type="password" name="password_confirmation" placeholder="Confirm Password" class="w-full mb-3 border p-2 rounded">

        <button class="bg-red-500 text-white px-4 py-2 rounded">Change Password</button>
    </form>

    {{-- PREFERENCES --}}
    <form action="{{ route('settings.preferences') }}" method="POST" class="bg-white p-6 rounded-xl shadow">
        @csrf
        <h2 class="font-semibold mb-4">Preferences</h2>

        <select name="language" class="w-full mb-3 border p-2 rounded">
            <option value="id" {{ $user->language == 'id' ? 'selected' : '' }}>Indonesia</option>
            <option value="en" {{ $user->language == 'en' ? 'selected' : '' }}>English</option>
        </select>

        <button class="bg-green-500 text-white px-4 py-2 rounded">Save Preferences</button>
    </form>

</div>
@endsection