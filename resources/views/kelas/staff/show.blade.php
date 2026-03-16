@extends('layouts.dash')

@section('title', $kelas->nama_kelas)

@section('content')

<div class="mb-6">
    <a href="{{ route('kelas.index') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1 mb-3">
        <i class="fas fa-arrow-left text-xs"></i> Kembali
    </a>
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-black text-[#1B254B]">{{ $kelas->nama_kelas }}</h2>
            <p class="text-sm text-blue-500 font-semibold mt-0.5">{{ $kelas->mata_pelajaran }}</p>
        </div>
        {{-- Kode kelas --}}
        <div class="text-center bg-white rounded-2xl px-5 py-3 shadow-sm">
            <p class="text-xs text-gray-400 mb-1">Kode Kelas</p>
            <p class="text-2xl font-black tracking-widest text-[#1B254B] font-mono">{{ $kelas->kode_kelas }}</p>
            <p class="text-xs text-gray-400 mt-1">Bagikan ke siswa</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Daftar Siswa --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-black text-[#1B254B]">Daftar Siswa</h3>
            <span class="bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full">
                {{ $kelas->siswa->count() }} siswa
            </span>
        </div>

        @if($kelas->siswa->isEmpty())
        <div class="text-center py-8">
            <i class="fas fa-users text-gray-200 text-3xl mb-3"></i>
            <p class="text-sm text-gray-400">Belum ada siswa yang join</p>
            <p class="text-xs text-gray-300 mt-1">Bagikan kode <span class="font-mono font-bold">{{ $kelas->kode_kelas }}</span> ke siswa</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($kelas->siswa as $siswa)
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 font-bold text-sm flex-shrink-0">
                    {{ substr($siswa->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-[#1B254B] truncate">{{ $siswa->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $siswa->email }}</p>
                </div>
                <span class="text-xs text-gray-300">
                    {{ $siswa->pivot->created_at->diffForHumans() }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Daftar Materi/Course --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-black text-[#1B254B]">Materi Kelas</h3>
            <a href="#" class="text-blue-600 text-xs font-bold hover:underline">
                + Tambah Materi
            </a>
        </div>

        @if($kelas->courses->isEmpty())
        <div class="text-center py-8">
            <i class="fas fa-book text-gray-200 text-3xl mb-3"></i>
            <p class="text-sm text-gray-400">Belum ada materi</p>
            <a href="#" class="text-blue-600 text-xs font-bold mt-1 block hover:underline">
                + Tambah materi pertama
            </a>
        </div>
        @else
        <div class="space-y-3">
            @foreach($kelas->courses as $course)
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-alt text-indigo-400 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-[#1B254B] truncate">{{ $course->nama_course }}</p>
                    @if($course->deadline)
                    <p class="text-xs text-gray-400">
                        Tenggat {{ \Carbon\Carbon::parse($course->deadline)->translatedFormat('d M Y') }}
                    </p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

@endsection