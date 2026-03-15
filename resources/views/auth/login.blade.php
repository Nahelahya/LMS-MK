@extends('layouts.app')

@section('content')
<div class="w-full max-w-md p-8 m-4 bg-white/20 backdrop-blur-lg rounded-2xl border border-white/30 shadow-2xl text-white">
    
    <div class="text-center mb-8">
        <div class="flex justify-center mb-4">
            <i class="fas fa-sign-in-alt text-5xl text-blue-400"></i>
        </div>
        <h2 class="text-3xl font-bold">Welcome!</h2>
        <p class="text-gray-200 text-sm">Sign in to your account</p>
    </div>

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="mb-6">
            <label class="block mb-2 text-sm font-medium text-gray">Email Address</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                    <i class="fab fa-google text-gray-400"></i>
                </span>
                <input type="email" name="email" value="{{ old('email') }}" 
                    class="w-full pl-10 pr-3 py-3 bg-white border border-white rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-gray-400" 
                    placeholder="Email"  required>
            </div>
            @error('email')
                <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-2 text-sm font-medium text-white-200">Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                    <i class="fas fa-lock text-gray-400"></i>
                </span>
                <input type="password" name="password" id="password"
                    class="w-full pl-10 pr-10 py-3 bg-white border border-white rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-gray-400" 
                    placeholder="Password" required>
                <span class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePassword()">
                    <i class="fas fa-eye text-gray-400" id="eye-icon"></i>
                </span>
            </div>
        </div>

        <div class="flex items-center justify-between mb-8 text-sm">
            <div class="flex items-center">
                <input type="checkbox" name="remember" class="h-4 w-4 text-blue-600 bg-white/10 border-white/20 rounded focus:ring-0">
                <label class="ml-2 text-gray-200">remember me?</label>
            </div>
            <a href="{{ route('password.request') }}" class="text-blue-300 hover:text-blue-100 font-medium">forgot password?</a>
        </div>

        <div class="social-signup text-center mb-6">
            <div class="relative flex items-center justify-center mb-4">
                <div class="grow border-t border-white/20"></div>
                <span class="shrink mx-3 text-xs text-gray-300 uppercase tracking-widest">Or Login With</span>
                <div class="grow border-t border-white/20"></div>
            </div>
            <div class="flex justify-center gap-4">
                <a href="{{ route('google.redirect') }}" class="w-12 h-12 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-full border border-white/30 transition duration-300">
                    <i class="fab fa-google text-white"></i>
                </a>
                <a href="#" class="w-12 h-12 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-full border border-white/30 transition duration-300">
                    <i class="fab fa-github text-white"></i>
                </a>
            </div>
        </div>

        <button type="submit" class="w-full p-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition duration-300 transform hover:scale-[1.02]">
            Login <i class="fas fa-arrow-right ml-2"></i>
        </button>
    </form>

    <div class="text-center mt-8 text-sm text-gray-200">
        Don't have an account? 
        <a href="{{ route('register') }}" class="font-bold text-blue-300 hover:text-blue-100 underline decoration-2 underline-offset-4">Create Account</a>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
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