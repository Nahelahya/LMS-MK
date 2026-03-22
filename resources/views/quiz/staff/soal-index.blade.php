@extends('layouts.dash')
@section('title', 'Soal Quiz — ' . $kelas->nama_kelas)

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">

    <div>
        <a href="{{ route('kelas.show', $kelas) }}"
           class="text-gray-400 hover:text-blue-500 text-sm inline-flex items-center gap-1 mb-2 transition">
            <i class="fas fa-arrow-left text-xs"></i> {{ $kelas->nama_kelas }}
        </a>
        <h1 class="text-2xl font-black text-[#1B254B] dark:text-gray-100">Manajemen Soal Quiz</h1>
        <p class="text-sm text-gray-400 mt-0.5">{{ $kelas->mata_pelajaran }}</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm font-semibold px-4 py-3 rounded-xl">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm font-semibold px-4 py-3 rounded-xl">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
    </div>
    @endif

    {{-- Form tambah soal --}}
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl border border-gray-50 dark:border-[#1e2130] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 dark:border-[#1e2130]">
            <h2 class="font-black text-[#1B254B] dark:text-gray-100">
                <i class="fas fa-plus-circle text-indigo-500 mr-2"></i>Tambah Soal Baru
            </h2>
        </div>

        <form action="{{ route('quiz.soal.store', $kelas) }}" method="POST" id="formSoal" class="px-6 py-5 space-y-4">
            @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-1">Tipe Soal</label>
                <select name="tipe" id="tipe-soal" onchange="toggleTipe(this.value)"
                    class="w-full bg-gray-50 dark:bg-[#111318] border border-gray-200 dark:border-[#1e2130] rounded-xl px-3 py-2 text-sm text-[#1B254B] dark:text-gray-100 focus:outline-none focus:border-indigo-400 transition">
                    <option value="pilihan_ganda" {{ old('tipe') === 'essay' ? '' : 'selected' }}>Pilihan Ganda</option>
                    <option value="essay" {{ old('tipe') === 'essay' ? 'selected' : '' }}>Essay</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-1">Level Kesulitan</label>
                <select name="level"
                    class="w-full bg-gray-50 dark:bg-[#111318] border border-gray-200 dark:border-[#1e2130] rounded-xl px-3 py-2 text-sm text-[#1B254B] dark:text-gray-100 focus:outline-none focus:border-indigo-400 transition">
                    <option value="mudah" {{ old('level') === 'mudah' ? 'selected' : '' }}>Mudah</option>
                    <option value="sedang" {{ old('level', 'sedang') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="sulit" {{ old('level') === 'sulit' ? 'selected' : '' }}>Sulit</option>
                </select>
            </div>
        </div>
        
                {{-- uneditable kelas --}}
                <input type="hidden" name="mata_pelajaran" value="{{ $kelas->mata_pelajaran }}">
                <div>
                <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-1">Pertanyaan</label>
                <textarea name="pertanyaan" rows="3" required
                    class="w-full bg-gray-50 dark:bg-[#111318] border border-gray-200 dark:border-[#1e2130] rounded-xl px-3 py-2 text-sm text-[#1B254B] dark:text-gray-100 focus:outline-none focus:border-indigo-400 transition"
                    placeholder="Tulis pertanyaan di sini...">{{ old('pertanyaan') }}</textarea>
            </div>

            {{-- Opsi pilihan ganda --}}
            <div id="section-pg">
                <p class="text-xs font-black text-gray-400 uppercase tracking-wider mb-3">Pilihan Jawaban</p>
                <div class="space-y-3">
                    @foreach(['a','b','c','d'] as $opsi)
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-black flex-shrink-0">
                            {{ strtoupper($opsi) }}
                        </span>
                        <input type="text" name="opsi_{{ $opsi }}" value="{{ old('opsi_' . $opsi) }}"
                            class="flex-1 bg-gray-50 dark:bg-[#111318] border border-gray-200 dark:border-[#1e2130] rounded-xl px-3 py-2 text-sm text-[#1B254B] dark:text-gray-100 focus:outline-none focus:border-indigo-400 transition"
                            placeholder="Pilihan {{ strtoupper($opsi) }}">
                    </div>
                    @endforeach
                </div>
                <div class="mt-3">
                    <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-1">Jawaban Benar</label>
                    <select name="jawaban_benar" id="jawaban-benar-pg"
                        class="w-full bg-gray-50 dark:bg-[#111318] border border-gray-200 dark:border-[#1e2130] rounded-xl px-3 py-2 text-sm text-[#1B254B] dark:text-gray-100 focus:outline-none focus:border-indigo-400 transition">
                        <option value="a">A</option>
                        <option value="b">B</option>
                        <option value="c">C</option>
                        <option value="d">D</option>
                    </select>
                </div>
            </div>

            {{-- Kunci jawaban essay --}}
            <div id="section-essay" style="display:none;">
                <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-1">
                    Kunci Jawaban / Panduan <span class="text-gray-400 font-normal">(untuk AI menilai)</span>
                </label>
                <textarea name="jawaban_benar_essay" id="jawaban-benar-essay" rows="2"
                    class="w-full bg-gray-50 dark:bg-[#111318] border border-gray-200 dark:border-[#1e2130] rounded-xl px-3 py-2 text-sm text-[#1B254B] dark:text-gray-100 focus:outline-none focus:border-indigo-400 transition"
                    placeholder="Tulis poin-poin kunci jawaban yang benar...">{{ old('jawaban_benar_essay') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-1">
                    Pembahasan <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea name="pembahasan" rows="2"
                    class="w-full bg-gray-50 dark:bg-[#111318] border border-gray-200 dark:border-[#1e2130] rounded-xl px-3 py-2 text-sm text-[#1B254B] dark:text-gray-100 focus:outline-none focus:border-indigo-400 transition"
                    placeholder="Pembahasan yang ditampilkan setelah siswa menjawab...">{{ old('pembahasan') }}</textarea>
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition">
                    <i class="fas fa-plus mr-1"></i> Tambah Soal
                </button>
            </div>
        </form>
    </div>

    {{-- Daftar soal --}}
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl border border-gray-50 dark:border-[#1e2130] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 dark:border-[#1e2130] flex justify-between items-center">
            <h2 class="font-black text-[#1B254B] dark:text-gray-100">
                <i class="fas fa-list text-blue-500 mr-2"></i>Daftar Soal
            </h2>
            <span class="text-xs text-gray-400">{{ $soals->count() }} soal</span>
        </div>

        @forelse($soals as $s)
        <div class="px-6 py-4 border-b border-gray-50 dark:border-[#1e2130] last:border-0">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-black px-2 py-0.5 rounded-lg uppercase
                            {{ $s->level === 'mudah' ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' :
                               ($s->level === 'sedang' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400' :
                               'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400') }}">
                            {{ $s->level }}
                        </span>
                        <span class="text-[10px] font-black px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 uppercase">
                            {{ $s->tipe === 'pilihan_ganda' ? 'PG' : 'Essay' }}
                        </span>
                        <span class="text-[10px] text-gray-400">{{ $s->mata_pelajaran }}</span>
                    </div>
                    <p class="text-sm font-semibold text-[#1B254B] dark:text-gray-100">{{ $s->pertanyaan }}</p>
                    @if($s->tipe === 'pilihan_ganda')
                    <p class="text-xs text-gray-400 mt-1">
                        A: {{ $s->opsi_a }} · B: {{ $s->opsi_b }}
                        @if($s->opsi_c) · C: {{ $s->opsi_c }} @endif
                        @if($s->opsi_d) · D: {{ $s->opsi_d }} @endif
                        · <span class="text-green-600 dark:text-green-400 font-bold">Jawaban: {{ strtoupper($s->jawaban_benar) }}</span>
                    </p>
                    @else
                    <p class="text-xs text-gray-400 mt-1">
                        <span class="font-semibold">Kunci:</span> {{ Str::limit($s->jawaban_benar, 80) }}
                    </p>
                    @endif
                </div>
                <form action="{{ route('quiz.soal.destroy', [$kelas, $s->id]) }}" method="POST"
                      onsubmit="return confirm('Hapus soal ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-600 text-xs transition flex-shrink-0">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="py-12 text-center text-gray-400 dark:text-gray-600">
            <i class="fas fa-question-circle text-3xl mb-3 block opacity-40"></i>
            <p class="text-sm">Belum ada soal. Tambahkan soal pertama!</p>
        </div>
        @endforelse
    </div>

</div>

<script>
function toggleTipe(val) {
    var pg    = document.getElementById('section-pg');
    var essay = document.getElementById('section-essay');
    var pgJawaban    = document.getElementById('jawaban-benar-pg');
    var essayJawaban = document.getElementById('jawaban-benar-essay');

    if (val === 'essay') {
        pg.style.display    = 'none';
        essay.style.display = 'block';
        pgJawaban.removeAttribute('name');
        essayJawaban.setAttribute('name', 'jawaban_benar');
    } else {
        pg.style.display    = 'block';
        essay.style.display = 'none';
        pgJawaban.setAttribute('name', 'jawaban_benar');
        essayJawaban.removeAttribute('name');
    }
}

// Jalankan saat load untuk handle old() value
document.addEventListener('DOMContentLoaded', function() {
    toggleTipe(document.getElementById('tipe-soal').value);
});
</script>

@endsection