@extends('layouts.dash')

@section('title', 'Kelas Saya')

@section('content')

<div class="mb-6">
    <h2 class="text-2xl font-black text-[#1B254B]">Kelas Saya</h2>
    <p class="text-sm text-gray-400 mt-1">Join kelas dengan kode dari gurumu</p>
</div>

{{-- Form join kelas --}}
<div class="bg-white rounded-2xl p-6 shadow-sm mb-6 max-w-md">
    <h3 class="font-black text-[#1B254B] mb-4">Masukkan Kode Kelas</h3>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm font-semibold px-4 py-3 rounded-xl mb-4">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-600 text-sm font-semibold px-4 py-3 rounded-xl mb-4">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
    </div>
    @endif

    <form action="{{ route('kelas.join.post') }}" method="POST" class="flex gap-3">
        @csrf
        <input type="text" name="kode_kelas"
               maxlength="6"
               placeholder="Contoh: AB12CD"
               value="{{ old('kode_kelas') }}"
               class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm font-mono uppercase tracking-widest text-[#1B254B] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
               oninput="this.value = this.value.toUpperCase()">
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-3 rounded-xl transition text-sm">
            Join
        </button>
    </form>
</div>

{{-- Kelas yang sudah diikuti --}}
<h3 class="font-black text-[#1B254B] mb-3">Kelas yang Diikuti</h3>

@if($myKelas->isEmpty())
<div class="bg-white rounded-2xl p-10 text-center shadow-sm">
    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-chalkboard text-blue-300 text-xl"></i>
    </div>
    <p class="text-[#1B254B] font-bold mb-1">Belum join kelas</p>
    <p class="text-sm text-gray-400">Minta kode kelas ke gurumu dan masukkan di atas</p>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($myKelas as $k)
    <div class="bg-white rounded-2xl p-5 shadow-sm">

        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-chalkboard-teacher text-indigo-400"></i>
            </div>
            <span class="text-xs text-gray-300 font-mono font-bold">{{ $k->kode_kelas }}</span>
        </div>

        <h4 class="font-black text-[#1B254B] text-sm mb-0.5">{{ $k->nama_kelas }}</h4>
        <p class="text-xs text-blue-500 font-semibold mb-1">{{ $k->mata_pelajaran }}</p>
        <p class="text-xs text-gray-400 mb-4">
            <i class="fas fa-user mr-1"></i>{{ $k->staff->name ?? '–' }}
        </p>

        <div class="flex items-center justify-between">
            <span class="text-xs text-gray-400">
                <i class="fas fa-book mr-1"></i>{{ $k->courses->count() }} materi
            </span>
            <form action="{{ route('kelas.leave', $k) }}" method="POST"
                  onsubmit="return confirm('Keluar dari kelas {{ $k->nama_kelas }}?')">
                @csrf
                <button class="text-xs text-red-400 hover:text-red-600 font-semibold transition">
                    Keluar
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection