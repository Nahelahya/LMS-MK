@extends('layouts.dash')
@section('title', $kelas->nama_kelas)
@section('content')

{{-- Header --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <a href="{{ route('kelas.index') }}"
           class="text-gray-400 dark:text-gray-500 hover:text-blue-500 text-sm inline-flex items-center gap-1 mb-2 transition">
            <i class="fas fa-arrow-left text-xs"></i> Kelas Saya
        </a>
        <h2 class="text-2xl font-black text-[#1B254B] dark:text-gray-100">{{ $kelas->nama_kelas }}</h2>
        <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">
            {{ $kelas->mata_pelajaran }} ·
            <span class="font-mono font-black text-blue-500 dark:text-blue-400 tracking-widest text-xs">{{ $kelas->kode_kelas }}</span>
        </p>
    </div>
    <div class="flex gap-3">
        <div class="text-center bg-white dark:bg-[#1a1d28] border border-transparent dark:border-[#1e2130] rounded-2xl px-5 py-3 shadow-sm">
            <p class="text-2xl font-black text-[#1B254B] dark:text-gray-100">{{ $kelas->siswa->count() }}</p>
            <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase">Siswa</p>
        </div>
        <div class="text-center bg-white dark:bg-[#1a1d28] border border-transparent dark:border-[#1e2130] rounded-2xl px-5 py-3 shadow-sm">
            <p class="text-2xl font-black text-[#1B254B] dark:text-gray-100">{{ $kelas->materis->count() }}</p>
            <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase">Materi</p>
        </div>
        <div class="text-center bg-white dark:bg-[#1a1d28] border border-transparent dark:border-[#1e2130] rounded-2xl px-5 py-3 shadow-sm">
            <p class="text-2xl font-black text-[#1B254B] dark:text-gray-100">{{ $kelas->courses->count() }}</p>
            <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase">Course</p>
        </div>
    </div>
</div>

@if(session('success'))
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm font-semibold px-4 py-3 rounded-xl mb-4">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm font-semibold px-4 py-3 rounded-xl mb-4">
    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
</div>
@endif

{{-- TABS --}}
<div x-data="{ tab: 'materi' }">

    <div class="flex gap-1 mb-6 bg-white dark:bg-[#1a1d28] border border-transparent dark:border-[#1e2130] rounded-2xl p-1.5 shadow-sm w-fit">
        <button @click="tab='materi'"
                :class="tab==='materi' ? 'bg-blue-600 text-white shadow' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                class="px-5 py-2 rounded-xl text-sm font-black transition-all">
            <i class="fas fa-book mr-1.5 text-xs"></i>Materi
        </button>
        <button @click="tab='course'"
                :class="tab==='course' ? 'bg-blue-600 text-white shadow' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                class="px-5 py-2 rounded-xl text-sm font-black transition-all">
            <i class="fas fa-layer-group mr-1.5 text-xs"></i>Course
            @if($kelas->courses->isEmpty())
            <span class="ml-1 bg-orange-400 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full align-middle">!</span>
            @endif
        </button>
        <button @click="tab='penilaian'"
                :class="tab==='penilaian' ? 'bg-blue-600 text-white shadow' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                class="px-5 py-2 rounded-xl text-sm font-black transition-all">
            <i class="fas fa-star mr-1.5 text-xs"></i>Penilaian
        </button>
    </div>

    {{-- ══════════════════════════════
         TAB MATERI
    ══════════════════════════════ --}}
    <div x-show="tab==='materi'"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0">

        {{-- Form Upload --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm p-6 border border-transparent dark:border-[#1e2130] mb-5">
            <h3 class="font-black text-[#1B254B] dark:text-gray-100 text-sm mb-4">
                <i class="fas fa-cloud-upload-alt text-blue-500 mr-2"></i>Upload Materi Baru
            </h3>
            <form action="{{ route('kelas.materi.store', $kelas->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Judul</label>
                        <input type="text" name="judul" value="{{ old('judul') }}" required placeholder="Judul materi"
                               class="w-full mt-1.5 px-4 py-2.5 rounded-xl text-sm border border-gray-200 dark:border-[#1e2130]
                                      bg-white dark:bg-[#111318] text-[#1B254B] dark:text-gray-200
                                      placeholder-gray-300 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('judul')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">File</label>
                        <label id="fileLabel" class="flex items-center gap-2 mt-1.5 px-4 py-2.5 rounded-xl cursor-pointer
                                      border-2 border-dashed border-gray-200 dark:border-[#1e2130]
                                      hover:border-blue-500 bg-gray-50 dark:bg-[#111318] transition">
                            <i class="fas fa-paperclip text-gray-400 dark:text-gray-600" id="fileIcon"></i>
                            <span class="text-sm text-gray-400 dark:text-gray-600 truncate" id="fileText">PDF, Word, Excel, Video (maks 50MB)</span>
                            <input type="file" name="file" class="hidden" required
                                   onchange="
                                       const name = this.files[0]?.name || '';
                                       document.getElementById('fileText').textContent = name || 'PDF, Word, Excel, Video (maks 50MB)';
                                       document.getElementById('fileIcon').className = name
                                           ? 'fas fa-check-circle text-green-500'
                                           : 'fas fa-paperclip text-gray-400 dark:text-gray-600';
                                   ">
                        </label>
                        @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">
                            Tenggat <span class="normal-case font-normal">(opsional)</span>
                        </label>
                        <input type="date" name="deadline" value="{{ old('deadline') }}" min="{{ date('Y-m-d') }}"
                               class="w-full mt-1.5 px-4 py-2.5 rounded-xl text-sm border border-gray-200 dark:border-[#1e2130]
                                      bg-white dark:bg-[#111318] text-[#1B254B] dark:text-gray-200
                                      focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('deadline')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Deskripsi <span class="normal-case font-normal">(opsional)</span></label>
                    <textarea name="deskripsi" rows="2" placeholder="Deskripsi materi"
                              class="w-full mt-1.5 px-4 py-2.5 rounded-xl text-sm border border-gray-200 dark:border-[#1e2130]
                                     bg-white dark:bg-[#111318] text-[#1B254B] dark:text-gray-200
                                     placeholder-gray-300 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('deskripsi') }}</textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition">
                        <i class="fas fa-upload mr-2"></i>Upload
                    </button>
                </div>
            </form>
        </div>

        {{-- Daftar Materi --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm border border-transparent dark:border-[#1e2130]">
            <div class="px-6 py-4 border-b border-gray-50 dark:border-[#1e2130] flex justify-between items-center">
                <h3 class="font-black text-[#1B254B] dark:text-gray-100 text-sm">Daftar Materi</h3>
                <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-black px-2.5 py-1 rounded-lg">
                    {{ $kelas->materis->count() }} file
                </span>
            </div>

            @forelse($kelas->materis as $m)
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
                $totalJawaban = $m->jawabans->count();
                $isOverdue    = $m->is_overdue;
                $daysLeft     = $m->days_left;
            @endphp

            <div x-data="{ editDeadline: false }"
                 class="border-b border-gray-50 dark:border-[#1e2130] last:border-0">
                <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 dark:hover:bg-[#111318] transition group">
                    <div class="w-10 h-10 {{ $icoBg }} rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $ico }} {{ $icoColor }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-[#1B254B] dark:text-gray-100 text-sm truncate">{{ $m->judul }}</p>
                        <div class="flex items-center flex-wrap gap-2 mt-0.5">
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">
                                <span class="uppercase font-bold">{{ $m->tipe_file }}</span> · {{ $m->created_at->diffForHumans() }}
                            </span>
                            @if($m->deadline)
                                @if($isOverdue)
                                <span class="text-[10px] font-black bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-2 py-0.5 rounded-full">
                                    <i class="fas fa-exclamation-circle text-[8px]"></i> Tenggat lewat · {{ $m->deadline->translatedFormat('d M Y') }}
                                </span>
                                @elseif($daysLeft <= 2)
                                <span class="text-[10px] font-black bg-orange-50 dark:bg-orange-900/30 text-orange-500 dark:text-orange-400 px-2 py-0.5 rounded-full">
                                    <i class="fas fa-clock text-[8px]"></i> {{ $daysLeft }} hari lagi · {{ $m->deadline->translatedFormat('d M Y') }}
                                </span>
                                @else
                                <span class="text-[10px] font-black bg-blue-50 dark:bg-blue-900/30 text-blue-500 dark:text-blue-400 px-2 py-0.5 rounded-full">
                                    <i class="fas fa-calendar text-[8px]"></i> {{ $m->deadline->translatedFormat('d M Y') }}
                                </span>
                                @endif
                            @else
                                <span class="text-[10px] text-gray-300 dark:text-gray-600 italic">Tanpa tenggat</span>
                            @endif
                            @if($totalJawaban > 0)
                            <span class="text-[10px] font-black bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-2 py-0.5 rounded-full">
                                {{ $totalJawaban }} jawaban
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition flex-shrink-0">
                        <button @click="editDeadline = !editDeadline"
                                :class="editDeadline ? 'bg-orange-500 text-white' : 'bg-orange-50 dark:bg-orange-900/30 text-orange-500 dark:text-orange-400'"
                                class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-clock text-[10px]"></i> Tenggat
                        </button>
                        <a href="{{ route('kelas.materi.download', [$kelas->id, $m->id]) }}"
                           class="inline-flex items-center gap-1 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50
                                  text-blue-600 dark:text-blue-400 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-download text-[10px]"></i> Unduh
                        </a>
                        <form action="{{ route('kelas.materi.destroy', [$kelas->id, $m->id]) }}" method="POST"
                              onsubmit="return confirm('Hapus materi ini?')">
                            @csrf @method('DELETE')
                            <button class="inline-flex items-center gap-1 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50
                                           text-red-500 dark:text-red-400 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                <i class="fas fa-trash text-[10px]"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Edit tenggat accordion --}}
                <div x-show="editDeadline"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="px-6 pb-4" style="display:none">
                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-900/40 rounded-2xl p-4">
                        <p class="text-xs font-black text-orange-700 dark:text-orange-400 mb-3">
                            <i class="fas fa-clock mr-1"></i>Atur Tenggat — {{ $m->judul }}
                        </p>
                        <form action="{{ route('kelas.materi.deadline', [$kelas->id, $m->id]) }}"
                              method="POST" class="flex items-end gap-3 flex-wrap">
                            @csrf @method('PATCH')
                            <div class="flex-1 min-w-[160px]">
                                <input type="date" name="deadline"
                                       value="{{ $m->deadline ? $m->deadline->format('Y-m-d') : '' }}"
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full px-3 py-2 rounded-xl text-sm border border-orange-200 dark:border-orange-900/50
                                              bg-white dark:bg-[#111318] text-[#1B254B] dark:text-gray-200
                                              focus:outline-none focus:ring-2 focus:ring-orange-400">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                                    <i class="fas fa-save mr-1"></i>Simpan
                                </button>
                                <form action="{{ route('kelas.materi.deadline', [$kelas->id, $m->id]) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="deadline" value="">
                                    <button type="submit" class="bg-gray-100 dark:bg-[#111318] hover:bg-gray-200 dark:hover:bg-[#1e2130]
                                                                  text-gray-500 dark:text-gray-400 text-xs font-bold px-4 py-2 rounded-xl transition">
                                        <i class="fas fa-times mr-1"></i>Hapus Tenggat
                                    </button>
                                </form>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-gray-400 dark:text-gray-600">
                <i class="fas fa-folder-open text-3xl mb-3 block opacity-40"></i>
                <p class="text-sm">Belum ada materi. Upload di atas.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ══════════════════════════════
         TAB COURSE
    ══════════════════════════════ --}}
    <div x-show="tab==='course'"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display:none">

        @if($kelas->courses->isEmpty())
        <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800/50 rounded-2xl px-5 py-3 mb-5 flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-orange-500 flex-shrink-0"></i>
            <p class="text-sm font-semibold text-orange-700 dark:text-orange-400">
                Belum ada course. Tambah minimal 1 course agar bisa memberi nilai ke siswa.
            </p>
        </div>
        @endif

        {{-- Form tambah course --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm p-6 border border-transparent dark:border-[#1e2130] mb-5">
            <h3 class="font-black text-[#1B254B] dark:text-gray-100 text-sm mb-4">
                <i class="fas fa-plus-circle text-blue-500 mr-2"></i>Tambah Course Baru
            </h3>
            <form action="{{ route('kelas.course.store', $kelas->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Nama Course</label>
                        <input type="text" name="nama_course" value="{{ old('nama_course') }}" required
                               placeholder="Contoh: Pemrograman Web"
                               class="w-full mt-1.5 px-4 py-2.5 rounded-xl text-sm border border-gray-200 dark:border-[#1e2130]
                                      bg-white dark:bg-[#111318] text-[#1B254B] dark:text-gray-200
                                      placeholder-gray-300 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('nama_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">
                            Deskripsi <span class="normal-case font-normal">(opsional)</span>
                        </label>
                        <input type="text" name="deskripsi" value="{{ old('deskripsi') }}"
                               placeholder="Deskripsi singkat course"
                               class="w-full mt-1.5 px-4 py-2.5 rounded-xl text-sm border border-gray-200 dark:border-[#1e2130]
                                      bg-white dark:bg-[#111318] text-[#1B254B] dark:text-gray-200
                                      placeholder-gray-300 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition">
                        <i class="fas fa-plus mr-2"></i>Tambah Course
                    </button>
                </div>
            </form>
        </div>

        {{-- Daftar course --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm border border-transparent dark:border-[#1e2130]">
            <div class="px-6 py-4 border-b border-gray-50 dark:border-[#1e2130] flex justify-between items-center">
                <h3 class="font-black text-[#1B254B] dark:text-gray-100 text-sm">Daftar Course</h3>
                <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-black px-2.5 py-1 rounded-lg">
                    {{ $kelas->courses->count() }} course
                </span>
            </div>

            @forelse($kelas->courses as $c)
            <div class="flex items-center gap-4 px-6 py-4 border-b border-gray-50 dark:border-[#1e2130] last:border-0
                        hover:bg-gray-50/50 dark:hover:bg-[#111318] transition group">
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-layer-group text-blue-500 dark:text-blue-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-[#1B254B] dark:text-gray-100 text-sm">{{ $c->nama_course }}</p>
                    <div class="flex items-center gap-3 mt-0.5">
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 font-mono">{{ $c->kode_course }}</span>
                        @if($c->deskripsi)
                        <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ Str::limit($c->deskripsi, 40) }}</span>
                        @endif
                        @php
                            $jumlahNilai = \App\Models\StudentProgress::where('course_id', $c->id)->count();
                        @endphp
                        @if($jumlahNilai > 0)
                        <span class="text-[10px] font-black bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 px-2 py-0.5 rounded-full">
                            {{ $jumlahNilai }} siswa dinilai
                        </span>
                        @endif
                    </div>
                </div>
                <div class="opacity-0 group-hover:opacity-100 transition flex-shrink-0">
                    <form action="{{ route('kelas.course.destroy', [$kelas->id, $c->id]) }}" method="POST"
                          onsubmit="return confirm('Hapus course ini? Data nilai terkait akan ikut terhapus.')">
                        @csrf @method('DELETE')
                        <button class="inline-flex items-center gap-1 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50
                                       text-red-500 dark:text-red-400 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-trash text-[10px]"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-gray-400 dark:text-gray-600">
                <i class="fas fa-layer-group text-3xl mb-3 block opacity-40"></i>
                <p class="text-sm">Belum ada course. Tambah course di atas.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ══════════════════════════════
         TAB PENILAIAN
    ══════════════════════════════ --}}
    <div x-show="tab==='penilaian'"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display:none">

        @if($kelas->courses->isEmpty())
        <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800/50 rounded-2xl px-5 py-3 mb-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-orange-500 flex-shrink-0"></i>
                <p class="text-sm font-semibold text-orange-700 dark:text-orange-400">
                    Belum ada course. Buat course dulu di tab <strong>Course</strong> agar bisa memberi nilai.
                </p>
            </div>
            <button @click="tab='course'"
                    class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold px-4 py-2 rounded-xl transition flex-shrink-0 ml-4">
                Buka Tab Course
            </button>
        </div>
        @endif

        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm border border-transparent dark:border-[#1e2130]">
            <div class="px-6 py-4 border-b border-gray-50 dark:border-[#1e2130]">
                <h3 class="font-black text-[#1B254B] dark:text-gray-100 text-sm">Siswa & Penilaian</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[11px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500
                                   border-b border-gray-100 dark:border-[#1e2130]">
                            <th class="text-left px-6 py-3">Siswa</th>
                            <th class="text-left px-4 py-3">Jawaban</th>
                            <th class="text-left px-4 py-3">Skor</th>
                            <th class="text-left px-4 py-3">Status</th>
                            <th class="text-left px-4 py-3">Beri Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelas->siswa as $siswa)
                        @php
                            $courseIds    = $kelas->courses->pluck('id');
                            $prog         = \App\Models\StudentProgress::where('user_id', $siswa->id)
                                                ->whereIn('course_id', $courseIds)
                                                ->orderByDesc('last_score')->first();
                            $materiIds    = $kelas->materis->pluck('id')->map(fn($id) => (string)$id);
                            $jawabanMasuk = \App\Models\Jawaban::where('student_id', $siswa->id)
                                                ->whereIn('materi_id', $materiIds)
                                                ->with('materi')->get();
                        @endphp
                        <tr class="border-b border-gray-50 dark:border-[#1e2130] last:border-0
                                   hover:bg-gray-50/50 dark:hover:bg-[#111318] transition">

                            {{-- Siswa --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                                                rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0">
                                        {{ substr($siswa->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-[#1B254B] dark:text-gray-100 text-sm">{{ $siswa->name }}</p>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ $siswa->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Jawaban + Preview --}}
                            <td class="px-4 py-4">
                                @if($jawabanMasuk->isEmpty())
                                    <span class="text-xs text-gray-300 dark:text-gray-600 italic">Belum ada</span>
                                @else
                                    <div class="flex flex-col gap-1.5">
                                        @foreach($jawabanMasuk as $j)
                                        <div class="flex items-center gap-1.5">
                                            {{-- Preview (untuk gambar/pdf) --}}
                                            @php
                                                $ext = strtolower(pathinfo($j->file_path, PATHINFO_EXTENSION));
                                                $isImage = in_array($ext, ['jpg','jpeg','png']);
                                                $isPdf   = $ext === 'pdf';
                                            @endphp

                                            @if($isImage)
                                            <button onclick="previewFile('{{ route('kelas.jawaban.preview', [$kelas->id, $j->id]) }}', 'image', '{{ addslashes($j->materi->judul ?? 'Jawaban') }}')"
                                                    class="inline-flex items-center gap-1 bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-100 dark:hover:bg-purple-900/50
                                                           text-purple-600 dark:text-purple-400 text-[10px] font-bold px-2 py-1 rounded-lg transition">
                                                <i class="fas fa-eye text-[9px]"></i> Preview
                                            </button>
                                            @elseif($isPdf)
                                            <button onclick="previewFile('{{ route('kelas.jawaban.preview', [$kelas->id, $j->id]) }}', 'pdf', '{{ addslashes($j->materi->judul ?? 'Jawaban') }}')"
                                                    class="inline-flex items-center gap-1 bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-100 dark:hover:bg-purple-900/50
                                                           text-purple-600 dark:text-purple-400 text-[10px] font-bold px-2 py-1 rounded-lg transition">
                                                <i class="fas fa-eye text-[9px]"></i> Preview
                                            </button>
                                            @endif

                                            <a href="{{ route('kelas.jawaban.download', [$kelas->id, $j->id]) }}"
                                               class="inline-flex items-center gap-1 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50
                                                      text-indigo-600 dark:text-indigo-400 text-[10px] font-bold px-2 py-1 rounded-lg transition">
                                                <i class="fas fa-download text-[9px]"></i>
                                                {{ Str::limit($j->materi->judul ?? 'Jawaban', 18) }}
                                            </a>
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            {{-- Skor --}}
                            <td class="px-4 py-4">
                                <span class="font-black text-[#1B254B] dark:text-gray-100 text-lg">
                                    {{ $prog->last_score ?? '—' }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-4">
                                @if($prog)
                                @php $st = $prog->status_adaptif; @endphp
                                <span class="text-[10px] font-black px-2.5 py-1 rounded-full
                                    {{ $st==='Advance'  ? 'bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400'  : '' }}
                                    {{ $st==='Normal'   ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'      : '' }}
                                    {{ $st==='Remedial' ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400'          : '' }}">
                                    {{ $st }}
                                </span>
                                @else
                                    <span class="text-xs text-gray-300 dark:text-gray-600 italic">—</span>
                                @endif
                            </td>

                            {{-- Form nilai --}}
                            <td class="px-4 py-4">
                                @if($kelas->courses->isNotEmpty())
                                <form action="{{ route('kelas.nilai.store', [$kelas->id, $siswa->id]) }}"
                                      method="POST" class="flex flex-col gap-1.5">
                                    @csrf
                                    <select name="course_id"
                                            class="w-full px-2 py-1.5 rounded-xl text-xs border border-gray-200 dark:border-[#1e2130]
                                                   bg-gray-50 dark:bg-[#111318] text-[#1B254B] dark:text-gray-200
                                                   focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @foreach($kelas->courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->nama_course }}</option>
                                        @endforeach
                                    </select>
                                    <div class="flex gap-1.5">
                                        <input type="number" name="nilai" min="0" max="100"
                                               value="{{ $prog->last_score ?? '' }}" placeholder="0–100"
                                               class="w-20 px-3 py-1.5 rounded-xl text-sm text-center font-black
                                                      border border-gray-200 dark:border-[#1e2130]
                                                      bg-gray-50 dark:bg-[#111318] text-[#1B254B] dark:text-gray-200
                                                      focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <button type="submit"
                                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1.5 rounded-xl transition">
                                            Simpan
                                        </button>
                                    </div>
                                </form>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-600 italic">Buat course dulu</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400 dark:text-gray-600">
                                <i class="fas fa-users text-3xl mb-3 block opacity-40"></i>
                                <p class="text-sm">Belum ada siswa.</p>
                                <p class="text-xs mt-1">Bagikan kode <span class="font-mono font-black text-blue-500">{{ $kelas->kode_kelas }}</span></p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════
     MODAL PREVIEW JAWABAN
══════════════════════════════ --}}
<div id="previewModal"
     class="fixed inset-0 z-50 bg-black/70 items-center justify-center p-4"
     style="display:none"
     onclick="if(event.target===this) closePreview()">
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-[#1e2130] flex-shrink-0">
            <p class="font-black text-[#1B254B] dark:text-gray-100 text-sm" id="previewTitle">Preview Jawaban</p>
            <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-[#1e2130]">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex-1 overflow-auto p-4 min-h-[300px]" id="previewContent">
            <div class="flex flex-col items-center justify-center h-48 text-gray-400 gap-3">
                <i class="fas fa-spinner fa-spin text-3xl text-blue-500"></i>
                <p class="text-sm">Memuat preview...</p>
            </div>
        </div>
    </div>
</div>

<script>
function previewFile(url, type, title) {
    const modal   = document.getElementById('previewModal');
    const content = document.getElementById('previewContent');
    const titleEl = document.getElementById('previewTitle');

    titleEl.textContent = 'Preview — ' + (title || 'Jawaban');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    if (type === 'image') {
        const img = new Image();
        img.onload = () => {
            content.innerHTML = '<img src="' + url + '" class="max-w-full mx-auto rounded-xl shadow" />';
        };
        img.onerror = () => {
            content.innerHTML = previewError();
        };
        img.src = url;
        content.innerHTML = '<div class="flex flex-col items-center justify-center h-48 text-gray-400 gap-3"><i class="fas fa-spinner fa-spin text-3xl text-blue-500"></i><p class="text-sm">Memuat gambar...</p></div>';

    } else if (type === 'pdf') {
        content.innerHTML =
            '<iframe src="' + url + '#toolbar=0" ' +
            'class="w-full rounded-xl border border-gray-100 dark:border-[#1e2130]" ' +
            'style="height:65vh" ' +
            'onload="this.previousElementSibling && this.previousElementSibling.remove()" ' +
            'onerror="this.parentElement.innerHTML=\'' + previewError(true) + '\'"></iframe>';
    }
}

function previewError(escaped) {
    return '<div class="flex flex-col items-center justify-center h-48 text-gray-400 gap-3">' +
           '<i class="fas fa-exclamation-circle text-3xl text-red-400"></i>' +
           '<p class="text-sm">Gagal memuat preview. Silakan unduh file-nya.</p>' +
           '</div>';
}

function closePreview() {
    const modal = document.getElementById('previewModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
    document.getElementById('previewContent').innerHTML =
        '<div class="flex flex-col items-center justify-center h-48 text-gray-400 gap-3">' +
        '<i class="fas fa-spinner fa-spin text-3xl text-blue-500"></i>' +
        '<p class="text-sm">Memuat preview...</p>' +
        '</div>';
}
</script>

@endsection