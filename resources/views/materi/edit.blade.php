@extends('layouts.dash')
@section('title', __('messages.edit_materi'))

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-3xl shadow-sm p-6 lg:p-8">

        {{-- Header dengan tombol kembali --}}
        <div class="flex items-center space-x-3 mb-8">
            <a href="{{ route('materi') }}"
               class="text-gray-400 hover:text-blue-600 transition"
               title="{{ __('messages.kembali') }}">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-lg font-bold text-[#1B254B]">{{ __('messages.edit_materi') }}</h2>
        </div>

        <form action="{{ route('materi.update', $materi) }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Field: Judul Materi --}}
            <div>
                <label class="text-xs font-bold text-gray-400 uppercase">
                    {{ __('messages.judul_materi') }}
                </label>
                <input type="text"
                       name="judul"
                       value="{{ old('judul', $materi->judul) }}"
                       class="w-full mt-2 p-3 rounded-xl border border-gray-200
                              focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('judul')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Field: Deskripsi --}}
            <div>
                <label class="text-xs font-bold text-gray-400 uppercase">
                    {{ __('messages.deskripsi') }}
                </label>
                <textarea name="deskripsi"
                          rows="3"
                          class="w-full mt-2 p-3 rounded-xl border border-gray-200
                                 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >{{ old('deskripsi', $materi->deskripsi) }}</textarea>
            </div>

            {{-- Field: Ganti File --}}
            <div>
                <label class="text-xs font-bold text-gray-400 uppercase">
                    {{ __('messages.ganti_file_label') }}
                </label>
                {{--
                    Kalimat "File saat ini: [nama file]" — nama file dari DB tidak
                    diterjemahkan, hanya teks di sekelilingnya yang pakai __().
                --}}
                <p class="text-xs text-gray-400 mt-1 mb-2">
                    {{ __('messages.file_saat_ini') }}:
                    <span class="font-semibold text-[#1B254B]">
                        {{ $materi->original_name ?? $materi->file_path }}
                    </span>
                </p>
                <label class="flex flex-col items-center justify-center h-24 border-2
                              border-dashed border-gray-200 rounded-xl cursor-pointer
                              hover:border-blue-500 transition">
                    <i class="fas fa-cloud-upload-alt text-xl text-gray-400"></i>
                    <span class="text-xs text-gray-400 mt-1">
                        {{ __('messages.pilih_file_baru') }}
                    </span>
                    <input type="file" name="file" class="hidden">
                </label>
                @error('file')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol aksi --}}
            <div class="flex space-x-3 pt-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold
                               px-6 py-3 rounded-xl shadow transition">
                    {{ __('messages.simpan_perubahan') }}
                </button>
                <a href="{{ route('materi') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold
                          px-6 py-3 rounded-xl transition">
                    {{ __('messages.batal') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection