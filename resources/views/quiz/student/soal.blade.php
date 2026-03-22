@extends('layouts.dash')
@section('title', 'Quiz Adaptif')

@section('content')
<div class="max-w-2xl mx-auto pb-12">

    {{-- Header progress --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-lg font-black text-[#1B254B] dark:text-gray-100">
                Quiz Adaptif — {{ $session->mata_pelajaran }}
            </h1>
            <span class="text-sm font-bold text-gray-400">{{ $nomorSoal }}/{{ $session->total_soal }}</span>
        </div>
        <div class="w-full bg-gray-100 dark:bg-[#1e2130] rounded-full h-2 overflow-hidden">
            <div class="bg-indigo-500 h-full rounded-full transition-all duration-500"
                 style="width: {{ $progress }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400 mt-1">
            <span>
                <i class="fas fa-check text-green-500 mr-0.5"></i>{{ $session->benar }} benar
                · <i class="fas fa-times text-red-400 mr-0.5"></i>{{ $session->salah }} salah
            </span>
            <span class="capitalize">Level: <span class="font-bold text-indigo-500">{{ $level }}</span></span>
        </div>
    </div>

    {{-- Feedback soal sebelumnya --}}
    @if(session('feedback_isBenar') !== null)
    <div class="mb-5 rounded-2xl p-4 border {{ session('feedback_isBenar') ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' }}">
        <p class="text-sm font-black {{ session('feedback_isBenar') ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }} mb-1">
            <i class="fas {{ session('feedback_isBenar') ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
            {{ session('feedback_isBenar') ? 'Benar!' : 'Kurang tepat' }}
        </p>
        @if(!session('feedback_isBenar') && session('feedback_benar'))
        <p class="text-xs text-gray-600 dark:text-gray-300">
            Jawaban benar: <span class="font-bold">{{ session('feedback_benar') }}</span>
        </p>
        @endif
        @if(session('feedback_ai'))
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">{{ session('feedback_ai') }}</p>
        @endif
        @if(session('feedback_pembahasan'))
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            <i class="fas fa-lightbulb text-yellow-400 mr-1"></i>{{ session('feedback_pembahasan') }}
        </p>
        @endif
    </div>
    @endif

    {{-- Card soal --}}
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl border border-gray-50 dark:border-[#1e2130] overflow-hidden shadow-sm">

        <div class="px-6 py-5 border-b border-gray-50 dark:border-[#1e2130]">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-[10px] font-black px-2 py-0.5 rounded-lg uppercase
                    {{ $soal->level === 'mudah' ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' :
                       ($soal->level === 'sedang' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400' :
                       'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400') }}">
                    {{ $soal->level }}
                </span>
                <span class="text-[10px] font-black px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 uppercase">
                    {{ $soal->tipe === 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay' }}
                </span>
            </div>
            <p class="text-base font-bold text-[#1B254B] dark:text-gray-100 leading-relaxed">
                {{ $nomorSoal }}. {{ $soal->pertanyaan }}
            </p>
        </div>

        <form action="{{ route('quiz.submit', $session->id) }}" method="POST" class="px-6 py-5">
            @csrf
            <input type="hidden" name="soal_id" value="{{ $soal->id }}">

            @if($soal->tipe === 'pilihan_ganda')
            <div class="space-y-3">
                @foreach(['a','b','c','d'] as $opsi)
                @php $val = $soal->{'opsi_' . $opsi}; @endphp
                @if($val)
                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-[#1e2130] hover:border-indigo-300 dark:hover:border-indigo-700 cursor-pointer transition group">
                    <input type="radio" name="jawaban" value="{{ $opsi }}" required
                        class="w-4 h-4 text-indigo-600 focus:ring-indigo-400">
                    <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-[#1e2130] flex items-center justify-center text-xs font-black text-gray-500 dark:text-gray-400 flex-shrink-0 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30 group-hover:text-indigo-500 transition">
                        {{ strtoupper($opsi) }}
                    </span>
                    <span class="text-sm text-[#1B254B] dark:text-gray-200">{{ $val }}</span>
                </label>
                @endif
                @endforeach
            </div>
            @else
            <div>
                <textarea name="jawaban" rows="5" required
                    class="w-full bg-gray-50 dark:bg-[#111318] border border-gray-200 dark:border-[#1e2130] rounded-xl px-4 py-3 text-sm text-[#1B254B] dark:text-gray-100 focus:outline-none focus:border-indigo-400 transition resize-none"
                    placeholder="Tulis jawabanmu di sini..."></textarea>
                <p class="text-xs text-gray-400 mt-1">
                    <i class="fas fa-robot text-indigo-400 mr-1"></i>Jawaban essay akan dinilai otomatis oleh AI
                </p>
            </div>
            @endif

            <div class="flex justify-between items-center mt-5">
                <span class="text-xs text-gray-400">
                    Soal {{ $nomorSoal }} dari {{ $session->total_soal }}
                </span>
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl transition">
                    {{ $nomorSoal < $session->total_soal ? 'Jawab & Lanjut' : 'Selesai Quiz' }}
                    <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection