@extends('layouts.dash')
@section('title', $kelas->nama_kelas)
@section('content')

{{-- Header --}}
<div class="mb-6">
    <a href="{{ route('kelas.join') }}"
       class="text-gray-400 dark:text-gray-500 hover:text-blue-500 text-sm inline-flex items-center gap-1 mb-2 transition">
        <i class="fas fa-arrow-left text-xs"></i> Kelas Saya
    </a>
    <h2 class="text-2xl font-black text-[#1B254B] dark:text-gray-100">{{ $kelas->nama_kelas }}</h2>
    <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">
        {{ $kelas->mata_pelajaran }}
        @if($kelas->staff) · <span class="font-semibold">{{ $kelas->staff->name }}</span> @endif
    </p>
</div>

@if(session('success'))
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm font-semibold px-4 py-3 rounded-xl mb-4">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm font-semibold px-4 py-3 rounded-xl mb-4">
    <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
</div>
@endif

{{-- Warning materi overdue --}}
@php
    $overdueCount = $kelas->materis->filter(fn($m) => $m->is_overdue && !$jawabanSaya->contains('materi_id', $m->id))->count();
@endphp
@if($overdueCount > 0)
<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-2xl px-5 py-3 mb-4 flex items-center gap-3">
    <i class="fas fa-exclamation-triangle text-red-500 text-sm flex-shrink-0"></i>
    <p class="text-sm font-semibold text-red-700 dark:text-red-400">
        {{ $overdueCount }} tugas sudah melewati tenggat waktu dan belum dikumpulkan.
    </p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ══════════════ KIRI: Daftar Materi ══════════════ --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm border border-transparent dark:border-[#1e2130]">
            <div class="px-6 py-4 border-b border-gray-50 dark:border-[#1e2130] flex justify-between items-center">
                <h3 class="font-black text-[#1B254B] dark:text-gray-100 text-base">
                    <i class="fas fa-book text-blue-500 mr-2"></i>Materi Kelas
                </h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $kelas->materis->count() }} file</span>
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
                $jawabanku = $jawabanSaya->firstWhere('materi_id', $m->id);
                $daysLeft  = $m->days_left;
                $isOverdue = $m->is_overdue;
                $sudahKumpul = !is_null($jawabanku);
            @endphp

            <div x-data="{ uploadOpen: false }"
                 class="border-b border-gray-50 dark:border-[#1e2130] last:border-0
                        {{ $isOverdue && !$sudahKumpul ? 'bg-red-50/30 dark:bg-red-900/10' : '' }}">

                <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 dark:hover:bg-[#111318] transition">
                    <div class="w-10 h-10 {{ $icoBg }} rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $ico }} {{ $icoColor }}"></i>
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-[#1B254B] dark:text-gray-100 text-sm truncate">{{ $m->judul }}</p>
                        <div class="flex items-center flex-wrap gap-2 mt-0.5">
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">
                                <span class="uppercase font-bold">{{ $m->tipe_file }}</span>
                                · {{ $m->created_at->diffForHumans() }}
                            </span>

                            {{-- Badge tenggat --}}
                            @if($m->deadline)
                                @if($isOverdue)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black
                                                 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400
                                                 px-2 py-0.5 rounded-full">
                                        <i class="fas fa-exclamation-circle text-[8px]"></i>
                                        Tenggat lewat
                                    </span>
                                @elseif($daysLeft === 0)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black
                                                 bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400
                                                 px-2 py-0.5 rounded-full animate-pulse">
                                        <i class="fas fa-clock text-[8px]"></i>Hari ini!
                                    </span>
                                @elseif($daysLeft <= 2)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black
                                                 bg-orange-50 dark:bg-orange-900/30 text-orange-500 dark:text-orange-400
                                                 px-2 py-0.5 rounded-full">
                                        <i class="fas fa-clock text-[8px]"></i>
                                        {{ $daysLeft }} hari lagi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black
                                                 bg-blue-50 dark:bg-blue-900/30 text-blue-500 dark:text-blue-400
                                                 px-2 py-0.5 rounded-full">
                                        <i class="fas fa-calendar text-[8px]"></i>
                                        Tenggat {{ $m->deadline->translatedFormat('d M Y') }}
                                    </span>
                                @endif
                            @endif

                            {{-- Status kumpul --}}
                            @if($sudahKumpul)
                            <span class="inline-flex items-center gap-1 text-[10px] font-black
                                         bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400
                                         px-2 py-0.5 rounded-full">
                                <i class="fas fa-check text-[8px]"></i>Sudah dikumpul
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('kelas.materi.download', [$kelas->id, $m->id]) }}"
                           class="inline-flex items-center gap-1 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50
                                  text-blue-600 dark:text-blue-400 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-download text-[10px]"></i> Unduh
                        </a>

                        {{-- Tombol kumpul — disable jika sudah lewat tenggat --}}
                        @if(!$isOverdue || $sudahKumpul)
                        <button @click="uploadOpen = !uploadOpen"
                                :class="uploadOpen ? 'bg-indigo-600 text-white' : 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400'"
                                class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-upload text-[10px]"></i>
                            {{ $sudahKumpul ? 'Ganti' : 'Kumpul' }}
                        </button>
                        @else
                        <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1.5 rounded-lg
                                     bg-gray-100 dark:bg-[#1e2130] text-gray-400 dark:text-gray-600 cursor-not-allowed">
                            <i class="fas fa-lock text-[10px]"></i> Tutup
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Form upload jawaban (accordion) --}}
                <div x-show="uploadOpen"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="px-6 pb-4" style="display:none">
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-900/50 rounded-2xl p-4">
                        <p class="text-xs font-black text-indigo-700 dark:text-indigo-300 mb-3">
                            <i class="fas fa-upload mr-1"></i>
                            {{ $sudahKumpul ? 'Ganti Jawaban' : 'Upload Jawaban' }} — {{ $m->judul }}
                        </p>
                        <form action="{{ route('kelas.jawaban.store', [$kelas->id, $m->id]) }}"
                              method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
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
            </div>
            @empty
            <div class="py-12 text-center text-gray-400 dark:text-gray-600">
                <i class="fas fa-folder-open text-3xl mb-3 block opacity-40"></i>
                <p class="text-sm">Belum ada materi dari pengajar.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ══════════════ KANAN: Nilai & Status ══════════════ --}}
    <div class="space-y-5">

        @if($myProgress->isNotEmpty())
            @foreach($myProgress as $prog)
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg">
                <p class="text-blue-100 text-xs font-bold uppercase tracking-wider mb-1">
                    {{ $prog->course->nama_course ?? 'Nilai' }}
                </p>
                <p class="text-5xl font-black">{{ $prog->last_score }}</p>
                @php $st = $prog->status_adaptif; @endphp
                <p class="mt-2 text-sm font-bold
                    {{ $st==='Advance' ? 'text-green-300' : ($st==='Normal' ? 'text-blue-200' : 'text-red-300') }}">
                    {{ $st }}
                </p>
                @if($prog->is_at_risk)
                <div class="mt-3 bg-white/10 rounded-xl px-3 py-2 text-xs flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-yellow-300"></i>
                    <span class="text-yellow-100">Tingkatkan intensitas belajar!</span>
                </div>
                @endif
            </div>
            @endforeach
        @else
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl p-6 border border-transparent dark:border-[#1e2130] text-center">
            <i class="fas fa-star text-3xl text-gray-200 dark:text-gray-700 mb-3 block"></i>
            <p class="text-sm font-bold text-gray-400 dark:text-gray-500">Belum ada nilai</p>
            <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">Pengajar belum memberikan penilaian.</p>
        </div>
        @endif

        {{-- Progress pengumpulan --}}
        @php $totalMateri = $kelas->materis->count(); $totalKumpul = $jawabanSaya->count(); @endphp
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl p-6 shadow-sm border border-transparent dark:border-[#1e2130]">
            <h3 class="font-black text-[#1B254B] dark:text-gray-100 text-sm mb-4">
                <i class="fas fa-tasks text-indigo-500 mr-2"></i>Status Pengumpulan
            </h3>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $totalKumpul }}/{{ $totalMateri }} dikumpulkan</span>
                <span class="text-xs font-black text-blue-600 dark:text-blue-400">
                    {{ $totalMateri > 0 ? round(($totalKumpul/$totalMateri)*100) : 0 }}%
                </span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-[#1e2130] rounded-full h-2 overflow-hidden">
                <div class="bg-blue-500 h-full rounded-full transition-all duration-700"
                     style="width: {{ $totalMateri > 0 ? round(($totalKumpul/$totalMateri)*100) : 0 }}%"></div>
            </div>

            {{-- Daftar tenggat mendatang --}}
            @php
                $upcoming = $kelas->materis
                    ->filter(fn($m) => $m->deadline && !$m->is_overdue && !$jawabanSaya->contains('materi_id', $m->id))
                    ->sortBy('deadline')
                    ->take(3);
            @endphp
            @if($upcoming->isNotEmpty())
            <div class="mt-4 pt-4 border-t border-gray-50 dark:border-[#1e2130]">
                <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase mb-2">Tenggat Mendatang</p>
                @foreach($upcoming as $u)
                <div class="flex items-center justify-between py-1.5">
                    <p class="text-xs font-semibold text-[#1B254B] dark:text-gray-200 truncate max-w-[140px]">{{ $u->judul }}</p>
                    <span class="text-[10px] font-black
                        {{ $u->days_left <= 2 ? 'text-orange-500 dark:text-orange-400' : 'text-gray-400 dark:text-gray-500' }}">
                        {{ $u->days_left === 0 ? 'Hari ini' : $u->days_left . ' hari' }}
                    </span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>
@endsection