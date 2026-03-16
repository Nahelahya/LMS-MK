<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Learning AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-[#F4F7FE] font-sans" x-data="{ sidebarOpen: false }">

<div class="flex min-h-screen overflow-hidden">

    {{-- ── SIDEBAR ── --}}
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-72 bg-[#111C44] text-white transition-transform duration-300 transform lg:translate-x-0 lg:static lg:inset-0 flex flex-col h-screen">

        {{-- Logo --}}
        <div class="p-8 text-center border-b border-gray-700 relative">
            <h2 class="text-2xl font-bold tracking-tighter uppercase">Learning <span class="text-blue-500">AI</span></h2>
            <p class="text-[10px] text-blue-400 mt-1 uppercase tracking-widest">Adaptive Learning AI</p>
            <button @click="sidebarOpen = false" class="absolute top-8 right-4 lg:hidden text-gray-400">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-grow p-6 space-y-2 overflow-y-auto">

            {{-- Dashboard: always visible --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center space-x-4 p-4 rounded-2xl {{ request()->is('dashboard') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800' }} transition">
                <i class="fas fa-th-large text-lg {{ request()->is('dashboard') ? 'text-white' : 'text-gray-400' }}"></i>
                <span class="font-semibold text-sm">Dashboard</span>
            </a>

            {{-- Materi: always visible --}}
            <a href="{{ route('materi') }}"
               class="flex items-center space-x-4 p-4 rounded-2xl {{ request()->is('materi*') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800' }} transition">
                <i class="fas fa-book text-lg {{ request()->is('materi*') ? 'text-white' : 'text-gray-400' }}"></i>
                <span class="font-semibold text-sm">Materi</span>
            </a>

@if(auth()->user()->role === 'staff' || auth()->user()->role === 'admin')
<a href="{{ route('kelas.index') }}"
   class="flex items-center space-x-4 p-4 rounded-2xl {{ request()->is('kelas*') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800' }} transition">
    <i class="fas fa-chalkboard-teacher text-lg {{ request()->is('kelas*') ? 'text-white' : 'text-gray-400' }}"></i>
    <span class="font-semibold text-sm">Kelas</span>
</a>
@endif

            {{-- Progres: always visible --}}
            <a href="/progres"
               class="flex items-center space-x-4 p-4 rounded-2xl {{ request()->is('progres*') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800' }} transition">
                <i class="fas fa-chart-line text-lg {{ request()->is('progres*') ? 'text-white' : 'text-gray-400' }}"></i>
                <span class="font-semibold text-sm">Progres</span>
            </a>

@if(auth()->user()->role === 'student')
<a href="{{ route('kelas.join') }}"
   class="flex items-center space-x-4 p-4 rounded-2xl {{ request()->is('join-kelas*') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800' }} transition">
    <i class="fas fa-chalkboard text-lg {{ request()->is('join-kelas*') ? 'text-white' : 'text-gray-400' }}"></i>
    <span class="font-semibold text-sm">Kelas Saya</span>
</a>
@endif

            {{-- Admin & Staff only --}}
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
            <div class="pt-4">
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest px-4 mb-2">Manajemen</p>

                <a href="/users"
                   class="flex items-center space-x-4 p-4 rounded-2xl {{ request()->is('users*') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800' }} transition">
                    <i class="fas fa-users text-lg {{ request()->is('users*') ? 'text-white' : 'text-gray-400' }}"></i>
                    <span class="font-semibold text-sm">Data Siswa</span>
                </a>
            </div>
            @endif

            {{-- Settings --}}
            <a href="/settings"
               class="flex items-center space-x-4 p-4 rounded-2xl hover:bg-gray-800 transition">
                <i class="fas fa-cog text-lg text-gray-400"></i>
                <span class="font-semibold text-sm">Settings</span>
            </a>
        </nav>

        {{-- Role badge + Logout --}}
        <div class="p-6 border-t border-gray-700">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-sm">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div>
                    <p class="text-sm font-bold text-white truncate max-w-[160px]">{{ auth()->user()->name }}</p>
                    <span class="text-[10px] font-black uppercase tracking-tighter
                        {{ auth()->user()->role === 'admin' ? 'text-red-400' :
                           (auth()->user()->role === 'staff' ? 'text-yellow-400' : 'text-blue-400') }}">
                        {{ auth()->user()->role }}
                    </span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full bg-red-500/10 text-red-500 p-3 rounded-2xl font-bold hover:bg-red-500 hover:text-white transition flex items-center justify-center space-x-2 text-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Overlay (mobile) --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden transition-opacity"></div>

    {{-- ── MAIN CONTENT ── --}}
    <div class="flex-1 flex flex-col h-screen overflow-hidden">

        {{-- Top header --}}
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-30 p-4 lg:p-6 flex justify-between items-center px-6 lg:px-10 border-b border-gray-100">
            <div class="flex items-center space-x-4">
                <button @click="sidebarOpen = true" class="lg:hidden text-[#1B254B] p-2 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h3 class="text-lg lg:text-xl font-black text-[#1B254B]">@yield('title', 'Dashboard')</h3>
            </div>

            <div class="flex items-center space-x-3 lg:space-x-6">
                <div class="text-right hidden sm:block">
                    <p class="text-xs lg:text-sm font-bold text-[#1B254B]">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">{{ auth()->user()->role }}</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-600 rounded-xl lg:rounded-2xl shadow-lg border-2 border-white flex items-center justify-center text-white font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-8 px-6 lg:px-10">
            @yield('content')
        </main>
    </div>
</div>
{{-- Untuk staff/admin --}}
@if(auth()->user()->role === 'staff' || auth()->user()->role === 'admin')
<a href="{{ route('kelas.index') }}"
   class="flex items-center space-x-4 p-4 rounded-2xl {{ request()->is('kelas*') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800' }} transition">
    <i class="fas fa-chalkboard-teacher text-lg"></i>
    <span class="font-semibold text-sm">Kelas</span>
</a>
@endif

{{-- Untuk siswa --}}
@if(auth()->user()->role === 'student')
<a href="{{ route('kelas.join') }}"
   class="flex items-center space-x-4 p-4 rounded-2xl {{ request()->is('join-kelas*') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800' }} transition">
    <i class="fas fa-chalkboard text-lg"></i>
    <span class="font-semibold text-sm">Kelas Saya</span>
</a>
@endif
</body>
</html>
