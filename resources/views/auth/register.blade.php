@extends('layouts.app')

@section('content')
<div class="relative z-20 w-full max-w-md p-8 m-4 bg-white/20 backdrop-blur-xl rounded-3xl border border-white/30 shadow-2xl text-white">
    
    <div class="text-center mb-8">
        <div class="flex justify-center mb-4">
            <div class="bg-blue-500/30 p-4 rounded-full">
                <i class="fas fa-user-plus text-4xl text-blue-400"></i>
            </div>
        </div>
        <h2 class="text-3xl font-bold tracking-tight">Create Account</h2>
        <p class="text-gray-200 text-sm mt-1">Join study</p>
    </div>

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="mb-5">
            <label class="block mb-1.5 text-sm font-medium text-gray-200 ml-1">Full Name</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <i class="fas fa-user"></i>
                </span>
                <input type="text" name="name" value="{{ old('name') }}" 
                    class="w-full pl-11 pr-4 py-3 bg-white border border-white rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-gray-800" 
                    placeholder="Enter your full name" required>
            </div>
            @error('name')
                <span class="text-red-400 text-xs mt-1 ml-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-5">
            <label class="block mb-1.5 text-sm font-medium text-gray-200 ml-1">Email Address</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <i class="fab fa-google"></i>
                </span>
                <input type="email" name="email" value="{{ old('email') }}" 
                    class="w-full pl-11 pr-4 py-3 bg-white border border-white rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-gray-800" 
                    placeholder="example@gmail.com" required>
            </div>
            @error('email')
                <span class="text-red-400 text-xs mt-1 ml-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-5">
            <label class="block mb-1.5 text-sm font-medium text-gray-200 ml-1">Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <i class="fas fa-lock"></i>
                </span>
                <input type="password" name="password" id="password_input" 
                    class="w-full pl-11 pr-11 py-3 bg-white border border-white rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-gray-800" 
                    placeholder="••••••••" required>
                <span class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer" onclick="togglePassword('password_input', 'eye-1')">
                    <i class="fas fa-eye text-gray-400" id="eye-1"></i>
                </span>
            </div>
            @error('password')
                <span class="text-red-400 text-xs mt-1 ml-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-8">
            <label class="block mb-1.5 text-sm font-medium text-gray-200 ml-1">Confirm Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <i class="fas fa-check-double"></i>
                </span>
                <input type="password" name="password_confirmation" id="password_confirm_input" 
                    class="w-full pl-11 pr-11 py-3 bg-white border border-white rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-gray-800" 
                    placeholder="••••••••" required>
                <span class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer" onclick="togglePassword('password_confirm_input', 'eye-2')">
                    <i class="fas fa-eye text-gray-400" id="eye-2"></i>
                </span>
            </div>
        </div>

        <button type="submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg transition-all transform hover:scale-[1.02] active:scale-[0.98]">
            Create Account <i class="fas fa-arrow-right ml-2"></i>
        </button>
    </form>

    <div class="text-center mt-8 text-sm text-gray-200">
        Already have an account? 
        <a href="{{ route('login') }}" class="font-bold text-blue-300 hover:text-blue-100 underline decoration-2 underline-offset-4 transition-all">Sign In</a>
    </div>
</div>

<script>
    function togglePassword(fieldId, iconId) {
        const passwordField = document.getElementById(fieldId);
        const eyeIcon = document.getElementById(iconId);
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>
@endsection