@extends('layouts.app')

@section('content')
<div class="relative z-20 w-full max-w-md p-8 m-4 bg-white/20 backdrop-blur-xl rounded-3xl border border-white/30 shadow-2xl text-white">
    
    <div class="text-center mb-8">
        <div class="flex justify-center mb-4">
            <div class="bg-blue-500/30 p-4 rounded-full">
                <i class="fas fa-lock text-4xl text-blue-400"></i>
            </div>
        </div>
        <h2 class="text-3xl font-bold tracking-tight">Set New Password</h2>
        <p class="text-gray-200 text-sm mt-1">Please enter your new password</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-500/50 border border-red-500 text-white p-3 rounded-xl mb-6 text-sm text-center">
            Password baru tidak cocok atau tidak memenuhi syarat.
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        
        <input type="hidden" name="token" value="{{ $token }}">
        
        <div class="mb-5 text-left">
            <label class="block mb-1.5 text-sm font-medium text-gray-200 ml-1">Email Address</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <i class="fab fa-google"></i>
                </span>
                <input type="email" name="email" value="{{ request('email') }}" 
                       class="w-full pl-11 pr-4 py-3 bg-white/10 border border-white/20 rounded-2xl placeholder-white/60 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all" 
                       placeholder="Enter your email" required>
            </div>
            @error('email')
                <span class="text-red-400 text-xs mt-1 ml-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-5 text-left">
            <label class="block mb-1.5 text-sm font-medium text-gray-200 ml-1">New Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <i class="fas fa-key"></i>
                </span>
                <input type="password" name="password" id="password"
                       class="w-full pl-11 pr-4 py-3 bg-white/10 border border-white/20 rounded-2xl placeholder-white/60 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all" 
                       placeholder="••••••••" required>
                <span class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePassword('password', 'eye-icon')">
                    <i class="fas fa-eye text-gray-400" id="eye-icon"></i>
                </span>
            </div>
        </div>
<div class="mb-8 text-left">
    <label class="block mb-1.5 text-sm font-medium text-gray-200 ml-1">Confirm New Password</label>
    <div class="relative">
        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
            <i class="fas fa-check-double"></i>
        </span>
        <input type="password" name="password_confirmation" id="password_confirmation"
               class="w-full pl-11 pr-12 py-3 bg-white/10 border border-white/20 rounded-2xl placeholder-white/60 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all" 
               placeholder="••••••••" required>
        
        <span class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePassword('password_confirmation', 'eye-2')">
            <i class="fas fa-eye text-gray-400" id="eye-2"></i>
        </span>
    </div>
</div>
        <button type="submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg transition-all transform hover:scale-[1.02] active:scale-[0.98]">
            Update Password <i class="fas fa-arrow-right ml-2"></i>
        </button>
    </form>
</div>

<script>
    function togglePassword(fieldId, iconId) {
        const passwordField = document.getElementById(fieldId);
        const eyeIcon = document.getElementById(iconId);
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>

@endsection