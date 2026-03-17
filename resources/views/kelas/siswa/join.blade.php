@extends('layouts.dash')
@section('title', 'Kelas Saya')
@section('content')

<div class="mb-6">
    <h2 class="text-2xl font-black text-[#1B254B] dark:text-gray-100">Kelas Saya</h2>
    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Join kelas dengan kode dari gurumu</p>
</div>

{{-- Flash --}}
@if(session('success'))
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800
            text-green-700 dark:text-green-400 text-sm font-semibold px-4 py-3 rounded-xl mb-4">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800
            text-red-600 dark:text-red-400 text-sm font-semibold px-4 py-3 rounded-xl mb-4">
    <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
</div>
@endif

{{-- Form join kelas --}}
<div class="bg-white dark:bg-[#1a1d28] rounded-2xl p-6 shadow-sm mb-6 max-w-md
            border border-transparent dark:border-[#1e2130]">
    <h3 class="font-black text-[#1B254B] dark:text-gray-100 mb-4">Masukkan Kode Kelas</h3>
    <form action="{{ route('kelas.join.post') }}" method="POST" class="flex gap-3">
        @csrf
        <input type="text" name="kode_kelas"
               maxlength="10"
               placeholder="Contoh: AB12CD"
               value="{{ old('kode_kelas') }}"
               oninput="this.value = this.value.toUpperCase()"
               class="flex-1 border border-gray-200 dark:border-[#1e2130]
                      bg-white dark:bg-[#111318]
                      text-[#1B254B] dark:text-gray-200
                      placeholder-gray-400 dark:placeholder-gray-600
                      rounded-xl px-4 py-3 text-sm font-mono uppercase tracking-widest
                      focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-3 rounded-xl transition text-sm">
            Join
        </button>
    </form>
</div>

{{-- Kelas yang diikuti --}}
<h3 class="font-black text-[#1B254B] dark:text-gray-100 mb-3">Kelas yang Diikuti</h3>

@if($myKelas->isEmpty())
<div class="bg-white dark:bg-[#1a1d28] rounded-2xl p-10 text-center shadow-sm border border-transparent dark:border-[#1e2130]">
    <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-chalkboard text-blue-300 dark:text-blue-500 text-xl"></i>
    </div>
    <p class="text-[#1B254B] dark:text-gray-100 font-bold mb-1">Belum join kelas</p>
    <p class="text-sm text-gray-400 dark:text-gray-500">Minta kode kelas ke gurumu dan masukkan di atas</p>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($myKelas as $k)
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl p-5 shadow-sm
                border border-transparent dark:border-[#1e2130]
                hover:shadow-md transition">

        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-chalkboard-teacher text-indigo-400 dark:text-indigo-400"></i>
            </div>
            <span class="text-xs text-gray-300 dark:text-gray-600 font-mono font-bold">{{ $k->kode_kelas }}</span>
        </div>

        <h4 class="font-black text-[#1B254B] dark:text-gray-100 text-sm mb-0.5">{{ $k->nama_kelas }}</h4>
        <p class="text-xs text-blue-500 dark:text-blue-400 font-semibold mb-1">{{ $k->mata_pelajaran }}</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">
            <i class="fas fa-user mr-1"></i>{{ $k->staff->name ?? '–' }}
        </p>

        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-gray-400 dark:text-gray-500">
                <i class="fas fa-book mr-1"></i>{{ $k->materis->count() }} materi
            </span>
        </div>

        {{-- Tombol Masuk + Keluar --}}
        <div class="flex gap-2">
            {{-- ✅ TOMBOL MASUK ke detail kelas --}}
            <a href="{{ route('kelas.siswa.show', $k) }}"
               class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 rounded-xl transition">
                <i class="fas fa-door-open mr-1.5"></i>Masuk Kelas
            </a>

            <form action="{{ route('kelas.leave', $k) }}" method="POST"
                  onsubmit="return confirm('Keluar dari kelas {{ $k->nama_kelas }}?')">
                @csrf
                @method('DELETE')
                <button class="bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50
                               text-red-500 dark:text-red-400 text-xs font-bold px-3 py-2.5 rounded-xl transition">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection