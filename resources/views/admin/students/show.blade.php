@extends('layouts.dash')

@section('title', $user->name)

@section('content')
<div class="p-6 space-y-6">

    {{-- Tombol kembali --}}
    <a href="{{ route('students.index') }}"
       class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm transition">
        <i class="fas fa-arrow-left"></i>
        Kembali ke Daftar Siswa
    </a>

    {{-- ===== KARTU PROFIL SISWA ===== --}}
    <div class="bg-gray-800/40 border border-gray-700/50 rounded-2xl p-8">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">

            {{-- Foto / Avatar --}}
            @if($user->photo)
                <img src="{{ Storage::url($user->photo) }}"
                     alt="{{ $user->name }}"
                     class="w-24 h-24 rounded-full object-cover ring-4 ring-blue-500/30">
            @else
                <div class="w-24 h-24 rounded-full bg-blue-600/30 ring-4 ring-blue-500/30
                            flex items-center justify-center text-blue-300 font-bold text-3xl">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
            @endif

            {{-- Info utama --}}
            <div class="flex-1 text-center sm:text-left">
                <h2 class="text-2xl font-bold text-white">{{ $user->name }}</h2>
                <p class="text-gray-400 mt-1">{{ $user->email }}</p>
                <div class="mt-3 flex items-center justify-center sm:justify-start gap-3 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold
                                 bg-blue-500/15 text-blue-400 border border-blue-500/30">
                        <i class="fas fa-user-graduate text-xs"></i>
                        Siswa
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold
                        {{ ($user->status ?? 'active') === 'active'
                            ? 'bg-green-500/15 text-green-400 border border-green-500/30'
                            : 'bg-gray-600/30 text-gray-400 border border-gray-600/40' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ ($user->status ?? 'active') === 'active' ? 'bg-green-400' : 'bg-gray-500' }}"></span>
                        {{ ($user->status ?? 'active') === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                    <span class="text-gray-500 text-xs">
                        Bergabung {{ $user->created_at->format('d M Y') }}
                    </span>
                </div>
            </div>

            {{-- Tombol hapus --}}
            <form action="{{ route('students.destroy', $user) }}"
                  method="POST"
                  onsubmit="return confirm('Hapus data siswa ini secara permanen?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 rounded-xl
                               bg-red-600/20 hover:bg-red-600/40 text-red-400 text-sm transition">
                    <i class="fas fa-trash"></i>
                    Hapus Siswa
                </button>
            </form>
        </div>
    </div>

    {{-- ===== INFO DETAIL ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <div class="bg-gray-800/40 border border-gray-700/50 rounded-2xl p-6 space-y-4">
            <h3 class="text-white font-semibold flex items-center gap-2">
                <i class="fas fa-id-card text-blue-400"></i>
                Informasi Akun
            </h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">ID Pengguna</span>
                    <span class="text-white font-mono">#{{ $user->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Nama Lengkap</span>
                    <span class="text-white">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Email</span>
                    <span class="text-white">{{ $user->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Role</span>
                    <span class="text-white capitalize">{{ $user->role }}</span>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/40 border border-gray-700/50 rounded-2xl p-6 space-y-4">
            <h3 class="text-white font-semibold flex items-center gap-2">
                <i class="fas fa-clock text-blue-400"></i>
                Aktivitas
            </h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Tanggal Daftar</span>
                    <span class="text-white">{{ $user->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Terakhir Diperbarui</span>
                    <span class="text-white">{{ $user->updated_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection