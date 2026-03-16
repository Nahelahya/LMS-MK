@extends('layouts.dash')

@section('title', 'Buat Kelas Baru')

@section('content')

<div class="max-w-xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('kelas.index') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1 mb-3">
            <i class="fas fa-arrow-left text-xs"></i> Kembali
        </a>
        <h2 class="text-2xl font-black text-[#1B254B]">Buat Kelas Baru</h2>
        <p class="text-sm text-gray-400 mt-1">Kode kelas akan digenerate otomatis</p>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm">
        <form action="{{ route('kelas.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                    Nama Kelas
                </label>
                <input type="text" name="nama_kelas" value="{{ old('nama_kelas') }}"
                       placeholder="contoh: Kelas X RPL 1"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-[#1B254B] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_kelas') border-red-300 @enderror">
                @error('nama_kelas')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                    Mata Pelajaran
                </label>
                <input type="text" name="mata_pelajaran" value="{{ old('mata_pelajaran') }}"
                       placeholder="contoh: Matematika, Pemrograman Web..."
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-[#1B254B] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('mata_pelajaran') border-red-300 @enderror">
                @error('mata_pelajaran')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                    Deskripsi <span class="text-gray-300 normal-case font-normal">(opsional)</span>
                </label>
                <textarea name="deskripsi" rows="3"
                          placeholder="Deskripsi singkat tentang kelas ini..."
                          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-[#1B254B] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="bg-blue-50 rounded-xl p-4 flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-400 mt-0.5"></i>
                <p class="text-xs text-blue-600">
                    Setelah kelas dibuat, kode unik akan muncul di halaman detail kelas.
                    Bagikan kode tersebut ke siswa agar mereka bisa join.
                </p>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition text-sm">
                Buat Kelas
            </button>
        </form>
    </div>

</div>

@endsection