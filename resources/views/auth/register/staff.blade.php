<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Staff — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="min-h-screen bg-gray-950 flex items-center justify-center px-4">

<div class="w-full max-w-md">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 mb-2">
            <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center">
                <i class="fas fa-graduation-cap text-white text-sm"></i>
            </div>
            <span class="text-white font-bold text-xl">{{ config('app.name') }}</span>
        </div>
        <p class="text-gray-400 text-sm">Pendaftaran Akun Staff</p>
    </div>

    {{-- Card --}}
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8 shadow-2xl">

        <h1 class="text-white font-semibold text-lg mb-1">Daftar sebagai Staff</h1>
        <p class="text-gray-400 text-sm mb-6">Masukkan kode undangan yang diberikan admin.</p>

        {{-- Error --}}
        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm px-4 py-3 rounded-xl mb-5 space-y-1">
                @foreach($errors->all() as $error)
                    <p><i class="fas fa-circle-exclamation mr-1"></i>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('register.staff.post') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Nama --}}
            <div>
                <label class="block text-sm text-gray-300 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 transition"
                    placeholder="Nama lengkap kamu">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm text-gray-300 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 transition"
                    placeholder="email@sekolah.com">
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm text-gray-300 mb-1.5">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-2.5 pr-10 text-sm focus:outline-none focus:border-indigo-500 transition"
                        placeholder="Min. 8 karakter">
                    <button type="button" onclick="togglePass('password', 'eye1')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white">
                        <i id="eye1" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label class="block text-sm text-gray-300 mb-1.5">Konfirmasi Password</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password2" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-2.5 pr-10 text-sm focus:outline-none focus:border-indigo-500 transition"
                        placeholder="Ulangi password">
                    <button type="button" onclick="togglePass('password2', 'eye2')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white">
                        <i id="eye2" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Invite Code --}}
            <div>
                <label class="block text-sm text-gray-300 mb-1.5">
                    Kode Undangan
                    <span class="text-gray-500 text-xs ml-1">(minta ke admin)</span>
                </label>
                <div class="relative">
                    <input type="password" name="invite_code" id="invite_code" required
                        class="w-full bg-gray-800 border @error('invite_code') border-red-500 @else border-gray-700 @enderror text-white rounded-xl px-4 py-2.5 pr-10 text-sm focus:outline-none focus:border-indigo-500 transition tracking-widest font-mono"
                        placeholder="••••••••">
                    <button type="button" onclick="togglePass('invite_code', 'eye3')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white">
                        <i id="eye3" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-xl text-sm transition mt-2">
                <i class="fas fa-user-shield mr-1.5"></i> Daftar sebagai Staff
            </button>
        </form>

        <p class="text-center text-gray-500 text-sm mt-5">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 transition">Masuk di sini</a>
        </p>
    </div>

    <p class="text-center text-gray-600 text-xs mt-6">
        Daftar sebagai siswa?
        <a href="{{ route('register') }}" class="text-gray-400 hover:text-white transition">Klik di sini</a>
    </p>
</div>

<script>
function togglePass(inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
</body>
</html>