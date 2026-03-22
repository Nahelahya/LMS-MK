@extends('layouts.dash')
@section('title', 'Hasil Quiz')

@section('content')
<div class="max-w-2xl mx-auto pb-12">

    {{-- Hasil utama --}}
    @php
        $skor = $session->skor_akhir ?? 0;
        $grade = $skor >= 80 ? ['Advance', 'text-green-600 dark:text-green-400', 'bg-green-100 dark:bg-green-900/30', 'fa-trophy'] :
                ($skor >= 60 ? ['Normal', 'text-blue-600 dark:text-blue-400', 'bg-blue-100 dark:bg-blue-900/30', 'fa-check-circle'] :
                              ['Remedial', 'text-red-500 dark:text-red-400', 'bg-red-100 dark:bg-red-900/30', 'fa-redo']);
    @endphp

    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl border border-gray-50 dark:border-[#1e2130] p-8 text-center mb-6">
        <div class="w-20 h-20 rounded-2xl {{ $grade[2] }} flex items-center justify-center mx-auto mb-4">
            <i class="fas {{ $grade[3] }} text-3xl {{ $grade[1] }}"></i>
        </div>
        <h1 class="text-4xl font-black text-[#1B254B] dark:text-gray-100 mb-1">{{ $skor }}</h1>
        <p class="text-gray-400 mb-3">Skor akhir kamu</p>
        <span class="inline-flex items-center gap-1.5 text-sm font-black px-4 py-1.5 rounded-xl {{ $grade[2] }} {{ $grade[1] }}">
            {{ $grade[0] }}
        </span>

        <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-50 dark:border-[#1e2130]">
            <div>
                <p class="text-2xl font-black text-green-600 dark:text-green-400">{{ $session->benar }}</p>
                <p class="text-xs text-gray-400">Benar</p>
            </div>
            <div>
                <p class="text-2xl font-black text-red-500 dark:text-red-400">{{ $session->salah }}</p>
                <p class="text-xs text-gray-400">Salah</p>
            </div>
            <div>
                <p class="text-2xl font-black text-[#1B254B] dark:text-gray-100">{{ $session->total_soal }}</p>
                <p class="text-xs text-gray-400">Total soal</p>
            </div>
        </div>
    </div>

    {{-- Motivasi AI --}}
    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-900/50 rounded-2xl px-5 py-4 mb-6 flex items-start gap-3">
        <i class="fas fa-robot text-indigo-500 mt-0.5 flex-shrink-0"></i>
        <p class="text-sm text-indigo-700 dark:text-indigo-300">
            @if($skor >= 80)
                Luar biasa! Kamu sangat menguasai materi ini. Terus pertahankan prestasimu! 🎉
            @elseif($skor >= 60)
                Bagus! Nilaimu cukup baik. Ada beberapa hal yang bisa diperkuat lagi — review jawaban di bawah ya!
            @else
                Jangan menyerah! Setiap kesalahan adalah kesempatan belajar. Review pembahasan di bawah dan coba lagi. 💪
            @endif
        </p>
    </div>

    {{-- Review jawaban --}}
    <div class="space-y-4">
        <h2 class="font-black text-[#1B254B] dark:text-gray-100">Review Jawaban</h2>

        @foreach($jawabans as $i => $j)
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl border {{ $j->is_benar ? 'border-green-100 dark:border-green-900/50' : 'border-red-100 dark:border-red-900/50' }} overflow-hidden">
            <div class="px-5 py-4 {{ $j->is_benar ? 'bg-green-50/50 dark:bg-green-900/10' : 'bg-red-50/50 dark:bg-red-900/10' }}">
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full {{ $j->is_benar ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas {{ $j->is_benar ? 'fa-check text-green-600 dark:text-green-400' : 'fa-times text-red-500 dark:text-red-400' }}" style="font-size:10px;"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-gray-400">Soal {{ $i + 1 }}</span>
                            <span class="text-[10px] font-black px-1.5 py-0.5 rounded capitalize
                                {{ $j->level === 'mudah' ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' :
                                   ($j->level === 'sedang' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400' :
                                   'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400') }}">
                                {{ $j->level }}
                            </span>
                        </div>
                        <p class="text-sm font-semibold text-[#1B254B] dark:text-gray-100 mb-2">{{ $j->pertanyaan }}</p>

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Jawabanmu: <span class="font-bold {{ $j->is_benar ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                                {{ $j->tipe === 'pilihan_ganda' ? strtoupper($j->jawaban_siswa) . '. ' . ($j->{'opsi_' . $j->jawaban_siswa} ?? '') : $j->jawaban_siswa }}
                            </span>
                        </p>

                        @if(!$j->is_benar && $j->tipe === 'pilihan_ganda')
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Jawaban benar: <span class="font-bold text-green-600 dark:text-green-400">
                                {{ strtoupper($j->jawaban_benar) }}. {{ $j->{'opsi_' . $j->jawaban_benar} ?? '' }}
                            </span>
                        </p>
                        @endif

                        @if($j->feedback_ai)
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-1 italic">
                            <i class="fas fa-robot mr-1"></i>{{ $j->feedback_ai }}
                        </p>
                        @endif

                        @if($j->pembahasan)
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            <i class="fas fa-lightbulb text-yellow-400 mr-1"></i>{{ $j->pembahasan }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6 flex gap-3 justify-center">
        <a href="{{ route('dashboard') }}"
           class="bg-gray-100 dark:bg-[#1e2130] hover:bg-gray-200 dark:hover:bg-[#252636] text-gray-700 dark:text-gray-300 font-bold text-sm px-5 py-2.5 rounded-xl transition">
            <i class="fas fa-home mr-1"></i> Dashboard
        </a>
    </div>

</div>
@endsection