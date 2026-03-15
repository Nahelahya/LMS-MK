@extends('layouts.dash')

@section('title', 'Edit Materi')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-3xl shadow-sm p-6 lg:p-8">

        <div class="flex items-center space-x-3 mb-8">
            <a href="{{ route('materi') }}" class="text-gray-400 hover:text-blue-600 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-lg font-bold text-[#1B254B]">Edit Materi</h2>
        </div>

        <form action="{{ route('materi.update', $materi) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="text-xs font-bold text-gray-400 uppercase">Judul Materi</label>
                <input type="text" name="judul" value="{{ old('judul', $materi->judul) }}"
                    class="w-full mt-2 p-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-xs font-bold text-gray-400 uppercase">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                    class="w-full mt-2 p-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-400 uppercase">Ganti File (opsional)</label>
                <p class="text-xs text-gray-400 mt-1 mb-2">
                    File saat ini: <span class="font-semibold text-[#1B254B]">{{ $materi->original_name ?? $materi->file_path }}</span>
                </p>
                <label class="flex flex-col items-center justify-center h-24 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-blue-500 transition">
                    <i class="fas fa-cloud-upload-alt text-xl text-gray-400"></i>
                    <span class="text-xs text-gray-400 mt-1">Pilih file baru jika ingin mengganti</span>
                    <input type="file" name="file" class="hidden">
                </label>
                @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex space-x-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl shadow transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('materi') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold px-6 py-3 rounded-xl transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
