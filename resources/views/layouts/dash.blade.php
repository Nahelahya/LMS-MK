<!DOCTYPE html>
<html lang="id" id="htmlRoot">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Learning AI</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    {{-- Pasang dark class SEBELUM render — hindari flash putih --}}
    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <style>
        *, *::before, *::after {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.2s ease;
        }

        /* ════════════════════════════════════════════════════════
           AUTO DARK MODE — Semua child blade otomatis gelap
           tanpa perlu edit tiap file satu per satu.
           Override spesifik tetap bisa lewat dark: class Tailwind.
        ════════════════════════════════════════════════════════ */

        /* Backgrounds */
        .dark .bg-white              { background-color: #1a1d28 !important; }
        .dark .bg-gray-50            { background-color: #111318 !important; }
        .dark .bg-gray-100           { background-color: #1e2130 !important; }
        .dark .bg-gray-200           { background-color: #252836 !important; }

        /* Teks */
        .dark .text-gray-900         { color: #e2e4ec !important; }
        .dark .text-gray-800         { color: #d1d5e8 !important; }
        .dark .text-gray-700         { color: #c0c4d8 !important; }
        .dark .text-gray-600         { color: #9199b3 !important; }
        .dark .text-gray-500         { color: #6b7394 !important; }
        .dark .text-\[\#1B254B\]     { color: #e2e4ec !important; }

        /* Borders */
        .dark .border-gray-100       { border-color: #1e2130 !important; }
        .dark .border-gray-200       { border-color: #252836 !important; }
        .dark .border-gray-300       { border-color: #2d3148 !important; }

        /* Input / select / textarea */
        .dark input:not([type="range"]):not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]),
        .dark select,
        .dark textarea {
            background-color: #1a1d28 !important;
            border-color: #1e2130 !important;
            color: #e2e4ec !important;
        }
        .dark input::placeholder,
        .dark textarea::placeholder  { color: #4a5068 !important; }

        /* Tabel */
        .dark thead tr               { border-color: #1e2130 !important; }
        .dark tbody tr               { border-color: #1e2130 !important; }
        .dark tbody tr:hover         { background-color: #111318 !important; }
        .dark th                     { color: #6b7394 !important; }
        .dark td                     { color: #c0c4d8 !important; }

        /* Divider */
        .dark .divide-gray-100 > * + *  { border-color: #1e2130 !important; }
        .dark .divide-gray-200 > * + *  { border-color: #252836 !important; }

        /* Rounded containers — semua varian rounded yang biasa dipakai */
        .dark .rounded-2xl,
        .dark .rounded-3xl,
        .dark .rounded-\[2rem\],
        .dark .rounded-\[2\.5rem\] {
            /* tidak override bg — hanya pastikan border terlihat */
            border-color: #1e2130;
        }

        /* Shadow — hilangkan shadow putih di dark mode */
        .dark .shadow-sm,
        .dark .shadow,
        .dark .shadow-md         { box-shadow: 0 1px 6px rgba(0,0,0,0.4) !important; }

        /* Label form */
        .dark label               { color: #9199b3 !important; }

        /* Badge / pill warna semantik */
        .dark .bg-blue-50         { background-color: rgba(37,99,235,0.15) !important; }
        .dark .bg-indigo-50       { background-color: rgba(79,70,229,0.15) !important; }
        .dark .bg-green-50        { background-color: rgba(22,163,74,0.15) !important; }
        .dark .bg-red-50          { background-color: rgba(220,38,38,0.15) !important; }
        .dark .bg-yellow-50       { background-color: rgba(202,138,4,0.15) !important; }
        .dark .bg-purple-50       { background-color: rgba(147,51,234,0.15) !important; }
        .dark .bg-orange-50       { background-color: rgba(234,88,12,0.15) !important; }

        /* Teks warna semantik */
        .dark .text-blue-600      { color: #60a5fa !important; }
        .dark .text-indigo-600    { color: #818cf8 !important; }
        .dark .text-green-600,
        .dark .text-green-700     { color: #4ade80 !important; }
        .dark .text-red-600,
        .dark .text-red-700       { color: #f87171 !important; }
        .dark .text-yellow-600    { color: #facc15 !important; }
        .dark .text-purple-600    { color: #c084fc !important; }

        /* Border warna semantik */
        .dark .border-green-200   { border-color: rgba(22,163,74,0.3) !important; }
        .dark .border-red-200     { border-color: rgba(220,38,38,0.3) !important; }
        .dark .border-blue-200    { border-color: rgba(37,99,235,0.3) !important; }

        /* Hover bg semantik */
        .dark .hover\:bg-blue-100:hover   { background-color: rgba(37,99,235,0.25) !important; }
        .dark .hover\:bg-red-100:hover    { background-color: rgba(220,38,38,0.25) !important; }
        .dark .hover\:bg-yellow-100:hover { background-color: rgba(202,138,4,0.25) !important; }
        .dark .hover\:bg-gray-50:hover    { background-color: #111318 !important; }

        /* File upload dropzone */
        .dark .border-dashed      { border-color: #2d3148 !important; }

        /* Focus ring tetap biru --*/
        .dark .focus\:ring-blue-500:focus { --tw-ring-color: rgba(59,130,246,0.5); }

        /* Scrollbar supaya tidak aneh di dark mode */
        .dark ::-webkit-scrollbar        { width: 6px; height: 6px; }
        .dark ::-webkit-scrollbar-track  { background: #0d0f14; }
        .dark ::-webkit-scrollbar-thumb  { background: #1e2130; border-radius: 4px; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #2d3148; }
    </style>
</head>

<body class="bg-[#F4F7FE] dark:bg-[#0d0f14] font-sans">

<div class="flex min-h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    {{-- ══════════════════════════════
         SIDEBAR
    ══════════════════════════════ --}}
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-72
               bg-[#111C44] dark:bg-[#111318]
               text-white transition-transform duration-300 transform
               lg:translate-x-0 lg:static lg:inset-0
               flex flex-col h-screen
               border-r border-transparent dark:border-[#1e2130]">

        {{-- Logo --}}
        <div class="p-8 text-center border-b border-gray-700 dark:border-[#1e2130] relative">
            <h2 class="text-2xl font-bold tracking-tighter uppercase">
                Learning <span class="text-blue-500">AI</span>
            </h2>
            <p class="text-[10px] text-blue-400 mt-1 uppercase tracking-widest">Adaptive Learning AI</p>
            <button @click="sidebarOpen = false" class="absolute top-8 right-4 lg:hidden text-gray-400">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-grow p-6 space-y-2 overflow-y-auto">

            <a href="{{ route('dashboard') }}"
               class="flex items-center space-x-4 p-4 rounded-2xl transition
                      {{ request()->is('dashboard') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800 dark:hover:bg-[#1a1d28]' }}">
                <i class="fas fa-th-large text-lg {{ request()->is('dashboard') ? 'text-white' : 'text-gray-400' }}"></i>
                <span class="font-semibold text-sm">{{ __('messages.dashboard') }}</span>
            </a>

            <a href="{{ route('materi') }}"
                class="flex items-center space-x-4 p-4 rounded-2xl transition
                    {{ request()->is('materi*') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800 dark:hover:bg-[#1a1d28]' }}">
                <i class="fas fa-book text-lg {{ request()->is('materi*') ? 'text-white' : 'text-gray-400' }}"></i>
                <span class="font-semibold text-sm">{{ __('messages.materi') }}</span>
            </a>

            @if(auth()->user()->role === 'staff' || auth()->user()->role === 'admin')
            <a href="{{ route('kelas.index') }}"
               class="flex items-center space-x-4 p-4 rounded-2xl transition
                    {{ request()->is('kelas*') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800 dark:hover:bg-[#1a1d28]' }}">
                <i class="fas fa-chalkboard-teacher text-lg {{ request()->is('kelas*') ? 'text-white' : 'text-gray-400' }}"></i>
                <span class="font-semibold text-sm">{{ __('messages.kelas') }}</span>
            </a>
            @endif

<a href="{{ route('progres.index') }}"
   class="flex items-center space-x-4 p-4 rounded-2xl transition
          {{ request()->routeIs('progres*') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800 dark:hover:bg-[#1a1d28]' }}">
    <i class="fas fa-chart-line text-lg {{ request()->routeIs('progres*') ? 'text-white' : 'text-gray-400' }}"></i>
    <span class="font-semibold text-sm">{{ __('messages.progres') }}</span>
</a>


            @if(auth()->user()->role === 'student')
            <a href="{{ route('kelas.join') }}"
               class="flex items-center space-x-4 p-4 rounded-2xl transition
                      {{ request()->is('join-kelas*') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800 dark:hover:bg-[#1a1d28]' }}">
                <i class="fas fa-chalkboard text-lg {{ request()->is('join-kelas*') ? 'text-white' : 'text-gray-400' }}"></i>
                <span class="font-semibold text-sm">{{ __('messages.kelas') }}</span>
            </a>
            @endif

            {{-- Presensi: Student --}}
            @if(auth()->user()->role === 'student')
            <a href="{{ route('presensi.index') }}"
               class="flex items-center space-x-4 p-4 rounded-2xl transition
                      {{ request()->is('presensi*') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800 dark:hover:bg-[#1a1d28]' }}">
                <i class="fas fa-clipboard-check text-lg {{ request()->is('presensi*') ? 'text-white' : 'text-gray-400' }}"></i>
                <span class="font-semibold text-sm">{{ __('messages.presensi') }}</span>
            </a>
            @endif

            {{-- Presensi: Admin & Staff --}}
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
            <a href="{{ route('admin.presensi') }}"
               class="flex items-center space-x-4 p-4 rounded-2xl transition
                      {{ request()->is('admin/presensi*') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800 dark:hover:bg-[#1a1d28]' }}">
                <i class="fas fa-clipboard-list text-lg {{ request()->is('admin/presensi*') ? 'text-white' : 'text-gray-400' }}"></i>
                <span class="font-semibold text-sm">{{ __('messages.presensi') }}</span>
            </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
            <div class="pt-4">
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest px-4 mb-2">{{ __('messages.manajemen') }}</p>
                <a href="/users"
                   class="flex items-center space-x-4 p-4 rounded-2xl transition
                          {{ request()->is('users*') ? 'bg-blue-600 shadow-lg' : 'hover:bg-gray-800 dark:hover:bg-[#1a1d28]' }}">
                    <i class="fas fa-users text-lg {{ request()->is('users*') ? 'text-white' : 'text-gray-400' }}"></i>
                    <span class="font-semibold text-sm">{{ __('messages.siswa') }}</span>
                </a>
            </div>
            @endif

            <a href="{{ route('settings.index') }}"
               class="flex items-center space-x-4 p-4 rounded-2xl hover:bg-gray-800 dark:hover:bg-[#1a1d28] transition">
                <i class="fas fa-cog text-lg text-gray-400"></i>
                <span class="font-semibold text-sm">{{ __('messages.settings') }}</span>
            </a>
        </nav>

        {{-- Toggle + User + Logout --}}
        <div class="p-6 border-t border-gray-700 dark:border-[#1e2130]">

            {{-- ─── DARK / LIGHT TOGGLE ─── --}}
            <div class="flex items-center justify-between mb-5 px-1">
                <span class="text-xs text-gray-400 flex items-center gap-2">
                    <i id="toggleIcon" class="fas fa-moon"></i>
                    <span id="toggleText">{{ __('messages.key') }}</span>
                </span>
                <button
                    onclick="toggleTheme()"
                    id="toggleBtn"
                    class="relative inline-flex items-center w-11 h-6 rounded-full
                           bg-gray-600 transition-colors duration-300 focus:outline-none">
                    <span
                        id="toggleKnob"
                        class="inline-block w-4 h-4 bg-white rounded-full
                               shadow transform transition-transform duration-300 translate-x-1">
                    </span>
                </button>
            </div>

            {{-- User info --}}
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-sm">
                    <img src="{{ auth()->user()->photo ? asset('storage/profile/' . auth()->user()->photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}" 
     class="w-full h-full rounded-xl object-cover shadow-lg border border-white/20">
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

            {{-- Logout --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full bg-red-500/10 text-red-500 p-3 rounded-2xl font-bold
                               hover:bg-red-500 hover:text-white transition
                               flex items-center justify-center space-x-2 text-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Overlay (mobile) --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden transition-opacity"></div>

    {{-- ══════════════════════════════
         MAIN CONTENT
    ══════════════════════════════ --}}
    <div class="flex-1 flex flex-col h-screen overflow-hidden">

        <header class="bg-white/80 dark:bg-[#111318]/90 backdrop-blur-md sticky top-0 z-30
                       p-4 lg:p-6 flex justify-between items-center px-6 lg:px-10
                       border-b border-gray-100 dark:border-[#1e2130]">
            <div class="flex items-center space-x-4">
                <button @click="sidebarOpen = true"
                        class="lg:hidden text-[#1B254B] dark:text-gray-300 p-2
                               hover:bg-gray-100 dark:hover:bg-[#1a1d28] rounded-lg">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h3 class="text-lg lg:text-xl font-black text-[#1B254B] dark:text-gray-100">
                    @yield('title', 'Dashboard')
                </h3>
            </div>

            <div class="flex items-center space-x-3 lg:space-x-6">
                <div class="text-right hidden sm:block">
                    <p class="text-xs lg:text-sm font-bold text-[#1B254B] dark:text-gray-200">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">
                        {{ auth()->user()->role }}
                    </p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-600 rounded-xl lg:rounded-2xl
                            shadow-lg border-2 border-white dark:border-[#1e2130]
                            flex items-center justify-center text-white font-bold">
                    <img src="{{ auth()->user()->photo ? asset('storage/profile/' . auth()->user()->photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}" 
     class="w-full h-full rounded-xl object-cover shadow-lg border border-white/20">
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 lg:p-8 px-6 lg:px-10
                     bg-[#F4F7FE] dark:bg-[#0d0f14] text-[#1B254B] dark:text-gray-200">
            @yield('content')
        </main>
    </div>
</div>

<script>
    let isDark = document.documentElement.classList.contains('dark');

    document.addEventListener('DOMContentLoaded', function () {
        updateToggleUI();
    });

    function toggleTheme() {
        isDark = !isDark;

        if (isDark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }

        updateToggleUI();

        // Notifikasi child blade (chart, dll) saat tema berubah
        if (typeof window.onThemeChange === "function") window.onThemeChange();
    }

    function updateToggleUI() {
        const btn  = document.getElementById('toggleBtn');
        const knob = document.getElementById('toggleKnob');
        const icon = document.getElementById('toggleIcon');
        const text = document.getElementById('toggleText');

        if (!btn) return;

        if (isDark) {
        btn.classList.replace('bg-gray-600', 'bg-blue-600');
        knob.classList.replace('translate-x-1', 'translate-x-6');
        icon.className = 'fas fa-sun text-yellow-400';
        text.textContent = "{{ __('messages.mode_gelap') }}"; 
    } else {
        btn.classList.replace('bg-blue-600', 'bg-gray-600');
        knob.classList.replace('translate-x-6', 'translate-x-1');
        icon.className = 'fas fa-moon text-gray-400';
        text.textContent = "{{ __('messages.mode_terang') }}";
    }
    }
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
@include('partials.chatbot')
</body>
</html>