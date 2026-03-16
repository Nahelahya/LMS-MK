@extends('layouts.dash')

@section('title', 'Materi')

@section('content')
<div class="space-y-8">

    {{-- ── FLASH MESSAGES ── --}}
    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-5 py-3 rounded-2xl text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── UPLOAD FORM (admin & staff only) ── --}}
    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
    <div class="bg-white dark:bg-[#1a1d28] rounded-3xl shadow-sm p-6 lg:p-8
                border border-transparent dark:border-[#1e2130]">
        <h2 class="text-lg font-bold text-[#1B254B] dark:text-gray-100 mb-6">Upload Materi Pembelajaran</h2>

        <form action="{{ route('materi.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid md:grid-cols-2 gap-6">

                {{-- Judul --}}
                <div>
                    <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Judul Materi</label>
                    <input type="text" name="judul" value="{{ old('judul') }}"
                        class="w-full mt-2 p-3 rounded-xl
                               border border-gray-200 dark:border-[#1e2130]
                               bg-white dark:bg-[#111318]
                               text-[#1B254B] dark:text-gray-200
                               placeholder-gray-400 dark:placeholder-gray-600
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan judul materi">
                    @error('judul') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Upload File --}}
                <div>
                    <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Upload File</label>
                    <label class="flex flex-col items-center justify-center h-32 mt-2
                                  border-2 border-dashed border-gray-200 dark:border-[#1e2130]
                                  rounded-xl cursor-pointer
                                  hover:border-blue-500 dark:hover:border-blue-500
                                  bg-transparent dark:bg-[#111318]
                                  transition">
                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 dark:text-gray-600"></i>
                        <span class="text-xs text-gray-400 dark:text-gray-600 mt-2">PDF, Word, Excel, Video, JPG (maks 50MB)</span>
                        <input type="file" name="file" class="hidden">
                    </label>
                    @error('file') <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                    class="w-full mt-2 p-3 rounded-xl
                           border border-gray-200 dark:border-[#1e2130]
                           bg-white dark:bg-[#111318]
                           text-[#1B254B] dark:text-gray-200
                           placeholder-gray-400 dark:placeholder-gray-600
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Tambahkan deskripsi materi">{{ old('deskripsi') }}</textarea>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl shadow">
                Upload Materi
            </button>
        </form>
    </div>
    @endif

    {{-- ── DAFTAR MATERI ── --}}
    <div class="bg-white dark:bg-[#1a1d28] rounded-3xl shadow-sm p-6 lg:p-8
                border border-transparent dark:border-[#1e2130]">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-[#1B254B] dark:text-gray-100">Daftar Materi</h2>
            <span class="text-xs text-gray-400 dark:text-gray-500">{{ count($materi) }} materi tersedia</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400 dark:text-gray-500
                               border-b border-gray-100 dark:border-[#1e2130]
                               text-[11px] uppercase tracking-widest">
                        <th class="pb-3">Judul</th>
                        <th class="pb-3">Tipe</th>
                        <th class="pb-3">Diunggah</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-[#1B254B] dark:text-gray-200">
                    @forelse($materi as $m)
                    <tr class="border-b border-gray-50 dark:border-[#1e2130]
                               hover:bg-gray-50 dark:hover:bg-[#111318] transition">

                        {{-- Judul --}}
                        <td class="py-4 font-semibold">
                            {{ $m->judul }}
                            @if($m->deskripsi)
                                <p class="text-xs text-gray-400 dark:text-gray-500 font-normal mt-0.5">
                                    {{ Str::limit($m->deskripsi, 60) }}
                                </p>
                            @endif
                        </td>

                        {{-- Tipe file --}}
                        <td class="py-4">
                            @php
                                $icons = [
                                    'pdf'  => 'fa-file-pdf text-red-500',
                                    'doc'  => 'fa-file-word text-blue-600',
                                    'docx' => 'fa-file-word text-blue-600',
                                    'xls'  => 'fa-file-excel text-green-600',
                                    'xlsx' => 'fa-file-excel text-green-600',
                                    'mp4'  => 'fa-file-video text-purple-500',
                                    'jpg'  => 'fa-file-image text-yellow-500',
                                    'jpeg' => 'fa-file-image text-yellow-500',
                                    'png'  => 'fa-file-image text-yellow-500',
                                ];
                                $icon = $icons[$m->tipe_file] ?? 'fa-file text-gray-400';
                            @endphp
                            <div class="flex items-center space-x-2">
                                <i class="fas {{ $icon }} text-lg"></i>
                                <span class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">
                                    {{ $m->tipe_file }}
                                </span>
                            </div>
                        </td>

                        {{-- Tanggal --}}
                        <td class="py-4 text-xs text-gray-400 dark:text-gray-500">
                            {{ $m->created_at->diffForHumans() }}
                        </td>

                        {{-- Aksi --}}
                        <td class="py-4">
                            <div class="flex items-center justify-end space-x-2">

                                {{-- Unduh --}}
                                <a href="{{ route('materi.download', $m->id) }}"
                                   class="inline-flex items-center space-x-1
                                          bg-blue-50 dark:bg-blue-900/30
                                          text-blue-600 dark:text-blue-400
                                          hover:bg-blue-100 dark:hover:bg-blue-900/50
                                          px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                    <i class="fas fa-download text-xs"></i>
                                    <span>Unduh</span>
                                </a>

                                {{-- Edit --}}
                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                                <a href="{{ route('materi.edit', $m->id) }}"
                                   class="inline-flex items-center space-x-1
                                          bg-yellow-50 dark:bg-yellow-900/30
                                          text-yellow-600 dark:text-yellow-400
                                          hover:bg-yellow-100 dark:hover:bg-yellow-900/50
                                          px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                    <span>Edit</span>
                                </a>
                                @endif

                                {{-- Hapus --}}
                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                                <form method="POST" action="{{ route('materi.destroy', $m->id) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center space-x-1
                                               bg-red-50 dark:bg-red-900/30
                                               text-red-600 dark:text-red-400
                                               hover:bg-red-100 dark:hover:bg-red-900/50
                                               px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                        <i class="fas fa-trash text-xs"></i>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-gray-400 dark:text-gray-600 text-sm">
                            Belum ada materi. Hubungi guru anda untuk meminta materi!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection