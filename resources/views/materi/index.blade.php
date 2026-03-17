@extends('layouts.dash')
@section('title', 'Materi')
@section('content')
<div class="space-y-8">

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-5 py-3 rounded-2xl text-sm font-semibold">
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-5 py-3 rounded-2xl text-sm font-semibold">
        {{ $errors->first() }}
    </div>
    @endif

    {{-- Upload form (admin & staff only) --}}
    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
    <div class="bg-white dark:bg-[#1a1d28] rounded-3xl shadow-sm p-6 lg:p-8 border border-transparent dark:border-[#1e2130]">
        <h2 class="text-lg font-bold text-[#1B254B] dark:text-gray-100 mb-6">Upload Materi Pembelajaran</h2>
        <form action="{{ route('materi.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Judul Materi</label>
                    <input type="text" name="judul" value="{{ old('judul') }}"
                        class="w-full mt-2 p-3 rounded-xl border border-gray-200 dark:border-[#1e2130]
                               bg-white dark:bg-[#111318] text-[#1B254B] dark:text-gray-200
                               placeholder-gray-400 dark:placeholder-gray-600
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan judul materi">
                    @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Upload File</label>
                    <label class="flex flex-col items-center justify-center h-32 mt-2 border-2 border-dashed
                                  border-gray-200 dark:border-[#1e2130] rounded-xl cursor-pointer
                                  hover:border-blue-500 bg-transparent dark:bg-[#111318] transition">
                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 dark:text-gray-600"></i>
                        <span class="text-xs text-gray-400 dark:text-gray-600 mt-2">PDF, Word, Excel, Video, JPG (maks 50MB)</span>
                        <input type="file" name="file" class="hidden">
                    </label>
                    @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                    class="w-full mt-2 p-3 rounded-xl border border-gray-200 dark:border-[#1e2130]
                           bg-white dark:bg-[#111318] text-[#1B254B] dark:text-gray-200
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

    {{-- Daftar Materi --}}
    <div class="bg-white dark:bg-[#1a1d28] rounded-3xl shadow-sm p-6 lg:p-8 border border-transparent dark:border-[#1e2130]">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-[#1B254B] dark:text-gray-100">Daftar Materi</h2>
            <span class="text-xs text-gray-400 dark:text-gray-500">{{ count($materi) }} materi tersedia</span>
        </div>

        <div class="space-y-0">
            @forelse($materi as $m)
            @php
                $icons = [
                    'pdf'  => ['fa-file-pdf',   'text-red-500',    'bg-red-50 dark:bg-red-900/30'],
                    'doc'  => ['fa-file-word',  'text-blue-600',   'bg-blue-50 dark:bg-blue-900/30'],
                    'docx' => ['fa-file-word',  'text-blue-600',   'bg-blue-50 dark:bg-blue-900/30'],
                    'xls'  => ['fa-file-excel', 'text-green-600',  'bg-green-50 dark:bg-green-900/30'],
                    'xlsx' => ['fa-file-excel', 'text-green-600',  'bg-green-50 dark:bg-green-900/30'],
                    'mp4'  => ['fa-file-video', 'text-purple-500', 'bg-purple-50 dark:bg-purple-900/30'],
                    'jpg'  => ['fa-file-image', 'text-yellow-500', 'bg-yellow-50 dark:bg-yellow-900/30'],
                    'jpeg' => ['fa-file-image', 'text-yellow-500', 'bg-yellow-50 dark:bg-yellow-900/30'],
                    'png'  => ['fa-file-image', 'text-yellow-500', 'bg-yellow-50 dark:bg-yellow-900/30'],
                ];
                [$ico, $icoColor, $icoBg] = $icons[$m->tipe_file] ?? ['fa-file', 'text-gray-400', 'bg-gray-100 dark:bg-[#1e2130]'];

                // Cek apakah siswa sudah upload jawaban untuk materi ini
                $jawabanku = null;
                $isOverdue = $m->is_overdue ?? false;
                $daysLeft  = $m->days_left ?? null;
                if (auth()->user()->role === 'student') {
                    $jawabanku = \App\Models\Jawaban::where('student_id', auth()->id())
                                     ->where('materi_id', (string) $m->id)
                                     ->first();
                }
            @endphp

            {{-- Row per materi dengan accordion upload untuk siswa --}}
            <div x-data="{ uploadOpen: false }"
                 class="border-b border-gray-50 dark:border-[#1e2130] last:border-0">

                <div class="flex items-center gap-4 py-4 hover:bg-gray-50/50 dark:hover:bg-[#111318] transition rounded-xl px-2">

                    {{-- Icon --}}
                    <div class="w-10 h-10 {{ $icoBg }} rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $ico }} {{ $icoColor }}"></i>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-[#1B254B] dark:text-gray-100 text-sm truncate">{{ $m->judul }}</p>
                        <div class="flex items-center flex-wrap gap-2 mt-0.5">
                            @if($m->deskripsi)
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ Str::limit($m->deskripsi, 50) }}</span>
                            @endif

                            {{-- Badge tenggat --}}
                            @if($m->deadline)
                                @if($isOverdue)
                                <span class="text-[10px] font-black bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400 px-2 py-0.5 rounded-full">
                                    <i class="fas fa-exclamation-circle text-[8px] mr-0.5"></i>Tenggat lewat
                                </span>
                                @elseif($daysLeft !== null && $daysLeft <= 2)
                                <span class="text-[10px] font-black bg-orange-50 dark:bg-orange-900/30 text-orange-500 dark:text-orange-400 px-2 py-0.5 rounded-full animate-pulse">
                                    <i class="fas fa-clock text-[8px] mr-0.5"></i>
                                    {{ $daysLeft === 0 ? 'Hari ini!' : $daysLeft . ' hari lagi' }}
                                </span>
                                @else
                                <span class="text-[10px] font-black bg-blue-50 dark:bg-blue-900/30 text-blue-500 dark:text-blue-400 px-2 py-0.5 rounded-full">
                                    <i class="fas fa-calendar text-[8px] mr-0.5"></i>
                                    Tenggat {{ $m->deadline->translatedFormat('d M Y') }}
                                </span>
                                @endif
                            @endif

                            {{-- Status sudah kumpul (khusus siswa) --}}
                            @if(auth()->user()->role === 'student' && $jawabanku)
                            <span class="text-[10px] font-black bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 px-2 py-0.5 rounded-full">
                                <i class="fas fa-check text-[8px] mr-0.5"></i>Sudah dikumpul
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Aksi --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        {{-- Unduh --}}
                        <a href="{{ route('materi.download', $m->id) }}"
                           class="inline-flex items-center gap-1 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50
                                  text-blue-600 dark:text-blue-400 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-download text-xs"></i>
                            <span>Unduh</span>
                        </a>

                        {{-- Tombol Kumpul Tugas (khusus siswa) --}}
                        @if(auth()->user()->role === 'student')
                            @if(!$isOverdue || $jawabanku)
                            <button @click="uploadOpen = !uploadOpen"
                                    :class="uploadOpen ? 'bg-indigo-600 text-white' : 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400'"
                                    class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                <i class="fas fa-upload text-xs"></i>
                                <span>{{ $jawabanku ? 'Ganti' : 'Kumpul Tugas' }}</span>
                            </button>
                            @else
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg
                                         bg-gray-100 dark:bg-[#1e2130] text-gray-400 dark:text-gray-600 cursor-not-allowed">
                                <i class="fas fa-lock text-xs"></i> Tutup
                            </span>
                            @endif
                        @endif

                        {{-- Edit & Hapus (staff/admin) --}}
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                        <a href="{{ route('materi.edit', $m->id) }}"
                           class="inline-flex items-center gap-1 bg-yellow-50 dark:bg-yellow-900/30 hover:bg-yellow-100 dark:hover:bg-yellow-900/50
                                  text-yellow-600 dark:text-yellow-400 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-pencil-alt text-xs"></i><span>Edit</span>
                        </a>
                        <form method="POST" action="{{ route('materi.destroy', $m->id) }}"
                              onsubmit="return confirm('Hapus materi ini?')">
                            @csrf @method('DELETE')
                            <button class="inline-flex items-center gap-1 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50
                                           text-red-600 dark:text-red-400 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                <i class="fas fa-trash text-xs"></i><span>Hapus</span>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                {{-- Accordion upload tugas (siswa) --}}
                @if(auth()->user()->role === 'student')
                <div x-show="uploadOpen"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="px-2 pb-4" style="display:none">
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-900/50 rounded-2xl p-4">
                        <p class="text-xs font-black text-indigo-700 dark:text-indigo-300 mb-3">
                            <i class="fas fa-upload mr-1"></i>
                            {{ $jawabanku ? 'Ganti Jawaban' : 'Upload Jawaban' }} — {{ $m->judul }}
                        </p>
                        <form action="{{ route('materi.jawaban.store', $m->id) }}"
                              method="POST" enctype="multipart/form-data"
                              class="flex items-center gap-3">
                            @csrf
                            <label class="flex items-center gap-2 flex-1 px-3 py-2 rounded-xl cursor-pointer
                                          border border-dashed border-indigo-200 dark:border-indigo-800
                                          hover:border-indigo-500 bg-white dark:bg-[#111318] transition">
                                <i class="fas fa-paperclip text-indigo-400 text-xs"></i>
                                <span class="text-xs text-gray-400 dark:text-gray-600">PDF, Word, atau gambar (maks 20MB)</span>
                                <input type="file" name="file" class="hidden"
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                            </label>
                            <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition flex-shrink-0">
                                <i class="fas fa-paper-plane mr-1"></i>Kirim
                            </button>
                        </form>
                    </div>
                </div>
                @endif

            </div>
            @empty
            <div class="py-10 text-center text-gray-400 dark:text-gray-600 text-sm">
                Belum ada materi. Hubungi guru anda!
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection