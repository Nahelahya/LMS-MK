@extends('layouts.dash')
@section('title', __('messages.materi_judul_halaman'))

@section('content')
<div class="space-y-8">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                px-5 py-3 rounded-2xl text-sm font-semibold">
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800
                text-red-600 dark:text-red-400 px-5 py-3 rounded-2xl text-sm font-semibold">
        {{ $errors->first() }}
    </div>
    @endif

    {{-- ══════ FORM UPLOAD (admin & staff only) ══════ --}}
    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
    <div class="bg-white dark:bg-[#1a1d28] rounded-3xl shadow-sm p-6 lg:p-8
                border border-transparent dark:border-[#1e2130]">

        <h2 class="text-lg font-bold text-[#1B254B] dark:text-gray-100 mb-6">
            {{ __('messages.upload_materi') }}
        </h2>

        <form action="{{ route('materi.upload') }}" method="POST"
              enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid md:grid-cols-2 gap-6">

                {{-- Judul Materi --}}
                <div>
                    <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">
                        {{ __('messages.judul_materi') }}
                    </label>
                    <input type="text" name="judul" value="{{ old('judul') }}"
                        placeholder="{{ __('messages.placeholder_judul') }}"
                        class="w-full mt-2 p-3 rounded-xl border border-gray-200 dark:border-[#1e2130]
                               bg-white dark:bg-[#111318] text-[#1B254B] dark:text-gray-200
                               placeholder-gray-400 dark:placeholder-gray-600
                               focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('judul')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Upload File --}}
                <div>
                    <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">
                        {{ __('messages.upload_file') }}
                    </label>
                    <label class="flex flex-col items-center justify-center h-32 mt-2
                                  border-2 border-dashed border-gray-200 dark:border-[#1e2130]
                                  rounded-xl cursor-pointer hover:border-blue-500
                                  bg-transparent dark:bg-[#111318] transition">
                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 dark:text-gray-600"></i>
                        <span class="text-xs text-gray-400 dark:text-gray-600 mt-2">
                            {{ __('messages.format_file') }}
                        </span>
                        <input type="file" name="file" class="hidden">
                    </label>
                    @error('file')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">
                    {{ __('messages.deskripsi') }}
                </label>
                <textarea name="deskripsi" rows="3"
                    placeholder="{{ __('messages.placeholder_deskripsi') }}"
                    class="w-full mt-2 p-3 rounded-xl border border-gray-200 dark:border-[#1e2130]
                           bg-white dark:bg-[#111318] text-[#1B254B] dark:text-gray-200
                           placeholder-gray-400 dark:placeholder-gray-600
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                >{{ old('deskripsi') }}</textarea>
            </div>

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold
                           px-6 py-3 rounded-xl shadow">
                {{ __('messages.tombol_upload') }}
            </button>
        </form>
    </div>
    @endif

    {{-- ══════ DAFTAR MATERI ══════ --}}
    <div class="bg-white dark:bg-[#1a1d28] rounded-3xl shadow-sm p-6 lg:p-8
                border border-transparent dark:border-[#1e2130]">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-[#1B254B] dark:text-gray-100">
                {{ __('messages.daftar_materi') }}
            </h2>
            {{-- ":count material(s) available" --}}
            <span class="text-xs text-gray-400 dark:text-gray-500">
                {{ __('messages.materi_tersedia', ['count' => count($materi)]) }}
            </span>
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
                [$ico, $icoColor, $icoBg] = $icons[$m->tipe_file]
                    ?? ['fa-file', 'text-gray-400', 'bg-gray-100 dark:bg-[#1e2130]'];

                $jawabanku = null;
                $isOverdue = $m->is_overdue ?? false;
                $daysLeft  = $m->days_left ?? null;

                // Query jawaban hanya untuk role student agar tidak N+1 di role lain
                if (auth()->user()->role === 'student') {
                    $jawabanku = \App\Models\Jawaban::where('student_id', auth()->id())
                                     ->where('materi_id', (string) $m->id)
                                     ->first();
                }
            @endphp

            {{-- Row per materi --}}
            <div x-data="{ uploadOpen: false }"
                 class="border-b border-gray-50 dark:border-[#1e2130] last:border-0">

                <div class="flex items-center gap-4 py-4 rounded-xl px-2
                            hover:bg-gray-50/50 dark:hover:bg-[#111318] transition">

                    {{-- Ikon tipe file --}}
                    <div class="w-10 h-10 {{ $icoBg }} rounded-xl
                                flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $ico }} {{ $icoColor }}"></i>
                    </div>

                    {{-- Info materi --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-[#1B254B] dark:text-gray-100 text-sm truncate">
                            {{ $m->judul }}
                        </p>
                        <div class="flex items-center flex-wrap gap-2 mt-0.5">

                            @if($m->deskripsi)
                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                {{ Str::limit($m->deskripsi, 50) }}
                            </span>
                            @endif

                            {{-- Badge tenggat — 3 kondisi, masing-masing diterjemahkan --}}
                            @if($m->deadline)
                                @if($isOverdue)
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-full
                                             bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400">
                                    <i class="fas fa-exclamation-circle text-[8px] mr-0.5"></i>
                                    {{ __('messages.tenggat_lewat') }}
                                </span>
                                @elseif($daysLeft !== null && $daysLeft <= 2)
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-full animate-pulse
                                             bg-orange-50 dark:bg-orange-900/30 text-orange-500 dark:text-orange-400">
                                    <i class="fas fa-clock text-[8px] mr-0.5"></i>
                                    {{--
                                        Ternary dua key berbeda:
                                        - daysLeft === 0  → 'tenggat_hari_ini' (tanpa parameter)
                                        - daysLeft > 0    → 'tenggat_hari_lagi' (dengan parameter :days)
                                    --}}
                                    @if($daysLeft === 0)
                                        {{ __('messages.tenggat_hari_ini') }}
                                    @else
                                        {{ __('messages.tenggat_hari_lagi', ['days' => $daysLeft]) }}
                                    @endif
                                </span>
                                @else
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-full
                                             bg-blue-50 dark:bg-blue-900/30 text-blue-500 dark:text-blue-400">
                                    <i class="fas fa-calendar text-[8px] mr-0.5"></i>
                                    {{ __('messages.tenggat_tanggal', ['date' => $m->deadline->translatedFormat('d M Y')]) }}
                                </span>
                                @endif
                            @endif

                            {{-- Badge sudah dikumpul (student only) --}}
                            @if(auth()->user()->role === 'student' && $jawabanku)
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full
                                         bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                <i class="fas fa-check text-[8px] mr-0.5"></i>
                                {{ __('messages.sudah_dikumpul') }}
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Tombol aksi --}}
                    <div class="flex items-center gap-2 flex-shrink-0">

                        {{-- Unduh (semua role) --}}
                        <a href="{{ route('materi.download', $m->id) }}"
                           class="inline-flex items-center gap-1 text-xs font-semibold
                                  px-3 py-1.5 rounded-lg transition
                                  bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50
                                  text-blue-600 dark:text-blue-400">
                            <i class="fas fa-download text-xs"></i>
                            <span>{{ __('messages.unduh') }}</span>
                        </a>

                        {{-- Tombol Kumpul / Ganti (student only) --}}
                        @if(auth()->user()->role === 'student')
                            @if(!$isOverdue || $jawabanku)
                            <button @click="uploadOpen = !uploadOpen"
                                    :class="uploadOpen
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400'"
                                    class="inline-flex items-center gap-1 text-xs font-semibold
                                           px-3 py-1.5 rounded-lg transition">
                                <i class="fas fa-upload text-xs"></i>
                                {{--
                                    Ternary dua key: 'ganti_file' vs 'kumpul_tugas'
                                    Catatan: di kelas-show.blade kita pakai 'kumpulkan' (satu kata).
                                    Di sini penulis asli pakai "Kumpul Tugas" (dua kata) yang lebih
                                    deskriptif — kita buat key terpisah 'kumpul_tugas' agar konsisten
                                    dengan maksud aslinya.
                                --}}
                                <span>{{ $jawabanku ? __('messages.ganti_file') : __('messages.kumpul_tugas') }}</span>
                            </button>
                            @else
                            {{-- Terkunci karena overdue --}}
                            <span class="inline-flex items-center gap-1 text-xs font-semibold
                                         px-3 py-1.5 rounded-lg cursor-not-allowed
                                         bg-gray-100 dark:bg-[#1e2130] text-gray-400 dark:text-gray-600">
                                <i class="fas fa-lock text-xs"></i>
                                {{ __('messages.tutup') }}
                            </span>
                            @endif
                        @endif

                        {{-- Edit & Hapus (admin & staff only) --}}
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                        <a href="{{ route('materi.edit', $m->id) }}"
                           class="inline-flex items-center gap-1 text-xs font-semibold
                                  px-3 py-1.5 rounded-lg transition
                                  bg-yellow-50 dark:bg-yellow-900/30 hover:bg-yellow-100 dark:hover:bg-yellow-900/50
                                  text-yellow-600 dark:text-yellow-400">
                            <i class="fas fa-pencil-alt text-xs"></i>
                            <span>{{ __('messages.edit') }}</span>
                        </a>
                        <form method="POST" action="{{ route('materi.destroy', $m->id) }}"
                              onsubmit="return confirm({{ Js::from(__('messages.konfirmasi_hapus_materi')) }})">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 text-xs font-semibold
                                           px-3 py-1.5 rounded-lg transition
                                           bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50
                                           text-red-600 dark:text-red-400">
                                <i class="fas fa-trash text-xs"></i>
                                <span>{{ __('messages.hapus') }}</span>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                {{-- Accordion upload jawaban (student only) --}}
                @if(auth()->user()->role === 'student')
                <div x-show="uploadOpen"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="px-2 pb-4" style="display:none">
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100
                                dark:border-indigo-900/50 rounded-2xl p-4">
                        <p class="text-xs font-black text-indigo-700 dark:text-indigo-300 mb-3">
                            <i class="fas fa-upload mr-1"></i>
                            {{-- Terjemahkan header accordion, nama materi tetap dari DB --}}
                            {{ $jawabanku ? __('messages.ganti_jawaban') : __('messages.upload_jawaban') }}
                            — {{ $m->judul }}
                        </p>
                        <form action="{{ route('materi.jawaban.store', $m->id) }}"
                              method="POST" enctype="multipart/form-data"
                              class="flex items-center gap-3">
                            @csrf
                            <label class="flex items-center gap-2 flex-1 px-3 py-2 rounded-xl
                                          cursor-pointer border border-dashed border-indigo-200
                                          dark:border-indigo-800 hover:border-indigo-500
                                          bg-white dark:bg-[#111318] transition">
                                <i class="fas fa-paperclip text-indigo-400 text-xs"></i>
                                <span class="text-xs text-gray-400 dark:text-gray-600">
                                    {{ __('messages.format_jawaban') }}
                                </span>
                                <input type="file" name="file" class="hidden"
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                            </label>
                            <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs
                                           font-bold px-4 py-2 rounded-xl transition flex-shrink-0">
                                <i class="fas fa-paper-plane mr-1"></i>{{ __('messages.kirim') }}
                            </button>
                        </form>
                    </div>
                </div>
                @endif

            </div>
            @empty
            {{-- Empty state --}}
            <div class="py-10 text-center text-gray-400 dark:text-gray-600 text-sm">
                {{ __('messages.belum_ada_materi_empty') }}
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection