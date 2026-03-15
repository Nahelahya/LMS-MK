<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">

<div class="min-h-screen w-full flex items-center justify-center bg-no-repeat bg-cover bg-center bg-fixed" 
     style="background-image: url('{{ asset('images/bg-pkm.jpg') }}');">
    
    <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>

    <div class="relative z-10 w-full max-w-md p-10 m-4 bg-white/20 backdrop-blur-lg rounded-2xl border border-white/30 shadow-2xl text-white">
        
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold uppercase tracking-wider">Reset Password</h2>
            <p class="text-gray-200 text-sm mt-3">Masukkan email Anda untuk menerima link reset password.</p>
        </div>

        @if (session('success'))
            <div class="bg-green-500/50 border border-green-500 text-white p-3 rounded-xl mb-6 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            
            <div class="mb-8">
                <label class="block mb-2 text-sm font-medium text-gray-200 text-center">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center">
                        <i class="fab fa-google text-gray-400"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           class="w-full pl-12 pr-4 py-3  bg-white border border-white rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-gray-400" 
                           placeholder="name@example.com" required>
                </div>
                @error('email')
                    <span class="text-red-400 text-xs mt-2 block text-center">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="w-full p-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition duration-300 transform hover:scale-[1.02]">
                Send Reset Link
            </button>
        </form>

        <div class="text-center mt-10 text-sm text-gray-200">
            Remembered your password? 
            <a href="{{ route('login') }}" class="font-bold text-blue-300 hover:text-blue-100 underline decoration-2 underline-offset-4">Back to Login</a>
        </div>

        <div class="text-center mt-12 text-xs text-white-400">
            © 2026 <strong>NAHdev</strong> . All rights reserved.
        </div>
    </div>
</div>

</body>
</html>