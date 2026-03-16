@extends('layouts.dash')

@section('title', 'Kelas Saya')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-black text-[#1B254B]">Kelas Saya</h2>
        <p class="text-sm text-gray-400 mt-1">Kelola kelas dan materi yang kamu ampu</p>
    </div>
    <a href="{{ route('kelas.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition flex items-center gap-2">
        <i class="fas fa-plus text-xs"></i> Buat Kelas
    </a>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 text-sm font-semibold px-4 py-3 rounded-xl mb-4">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
</div>
@endif

@if($kelas->isEmpty())
<div class="bg-white rounded-2xl p-12 text-center shadow-sm">
    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-chalkboard text-blue-400 text-2xl"></i>
    </div>
    <p class="text-[#1B254B] font-bold mb-1">Belum ada kelas</p>
    <p class="text-sm text-gray-400 mb-4">Buat kelas pertamamu sekarang</p>
    <a href="{{ route('kelas.create') }}" class="text-blue-600 text-sm font-bold hover:underline">
        + Buat Kelas Baru
    </a>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($kelas as $k)
    <div class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition">

        {{-- Header --}}
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-chalkboard-teacher text-blue-500"></i>
            </div>
            {{-- Kode kelas --}}
            <span class="bg-gray-100 text-gray-500 text-xs font-black tracking-widest px-3 py-1 rounded-lg font-mono">
                {{ $k->kode_kelas }}
            </span>
        </div>

        <h3 class="font-black text-[#1B254B] text-base mb-0.5">{{ $k->nama_kelas }}</h3>
        <p class="text-xs text-blue-500 font-semibold mb-3">{{ $k->mata_pelajaran }}</p>

        @if($k->deskripsi)
        <p class="text-xs text-gray-400 mb-3 line-clamp-2">{{ $k->deskripsi }}</p>
        @endif

        <div class="flex items-center gap-4 text-xs text-gray-400 mb-4">
            <span><i class="fas fa-users mr-1"></i>{{ $k->siswa_count }} siswa</span>
            <span><i class="fas fa-book mr-1"></i>{{ $k->courses->count() }} materi</span>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('kelas.show', $k) }}"
               class="flex-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-bold py-2 rounded-xl transition">
                Kelola
            </a>
            <form action="{{ route('kelas.destroy', $k) }}" method="POST"
                  onsubmit="return confirm('Hapus kelas {{ $k->nama_kelas }}?')">
                @csrf @method('DELETE')
                <button class="bg-red-50 hover:bg-red-100 text-red-500 text-xs font-bold px-3 py-2 rounded-xl transition">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection