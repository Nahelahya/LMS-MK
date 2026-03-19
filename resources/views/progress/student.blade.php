@extends('layouts.dash')
@section('title', __('messages.progres_belajar_judul'))

@section('content')
<div class="space-y-6">

    {{-- ── Greeting ── --}}
    <div>
        <h2 class="text-xl font-black text-[#1B254B] dark:text-white">
            {{ __('messages.hai_student', ['name' => $user->name]) }}
        </h2>
        {{--
            Kalimat semester mengandung dua angka tahun yang dihitung langsung dari PHP.
            Kita pisahkan label "Semester Genap / Even Semester" dari angkanya.
            Karena tahun akademik Indonesia = tahun lalu/tahun ini, kita kirim keduanya
            sebagai parameter :from dan :to — bukan hanya :year — agar fleksibel.
        --}}
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ __('messages.ringkasan_progres') }} ·
            {{ __('messages.semester_genap', [
                'from' => date('Y') - 1,
                'to'   => date('Y'),
            ]) }}
        </p>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Rata-rata Nilai --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm p-5
                    border border-gray-100 dark:border-[#1e2130] relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl
                        bg-gradient-to-r from-blue-500 to-blue-400"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                    {{ __('messages.rata_rata') }}
                </span>
                <div class="w-9 h-9 bg-blue-50 dark:bg-blue-500/10 rounded-xl
                            flex items-center justify-center">
                    <i class="fas fa-chart-bar text-blue-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-[#1B254B] dark:text-white">
                {{ number_format($rataRata, 1) }}
            </p>
            {{--
                "Grade A/B/C/D" — kata "Grade" sama di EN dan ID (sudah universal),
                huruf grade-nya juga tidak berubah, jadi hanya perlu satu key
                'grade_label' yang nilainya "Grade" di kedua bahasa.
                Alternatif: pakai key yang sudah ada yaitu 'grade' dengan parameter :letter.
            --}}
            <p class="text-xs text-gray-400 mt-1">
                {{ __('messages.grade', ['letter' =>
                    $rataRata >= 85 ? 'A' : ($rataRata >= 75 ? 'B' : ($rataRata >= 65 ? 'C' : 'D'))
                ]) }}
            </p>
        </div>

        {{-- Tugas Selesai --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm p-5
                    border border-gray-100 dark:border-[#1e2130] relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl
                        bg-gradient-to-r from-green-500 to-emerald-400"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                    {{ __('messages.tugas') }}
                </span>
                <div class="w-9 h-9 bg-green-50 dark:bg-green-500/10 rounded-xl
                            flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-[#1B254B] dark:text-white">{{ $tugasSelesai }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('messages.tugas_dikumpulkan') }}</p>
        </div>

        {{-- Kehadiran --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm p-5
                    border border-gray-100 dark:border-[#1e2130] relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl
                        bg-gradient-to-r from-yellow-500 to-amber-400"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                    {{ __('messages.kehadiran') }}
                </span>
                <div class="w-9 h-9 bg-yellow-50 dark:bg-yellow-500/10 rounded-xl
                            flex items-center justify-center">
                    <i class="fas fa-calendar-check text-yellow-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-[#1B254B] dark:text-white">
                {{ $persenHadir }}<span class="text-lg text-gray-400">%</span>
            </p>
            {{--
                ":done of :total day(s)" — dua parameter numerik dari variabel PHP.
                Key 'kehadiran_hari' sudah kita buat di sesi sebelumnya dengan
                parameter :done dan :total.
            --}}
            <p class="text-xs text-gray-400 mt-1">
                {{ __('messages.kehadiran_hari', [
                    'done'  => $totalHadir,
                    'total' => $totalAttendance,
                ]) }}
            </p>
        </div>

        {{-- Streak --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm p-5
                    border border-gray-100 dark:border-[#1e2130] relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl
                        bg-gradient-to-r from-purple-500 to-violet-400"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                    {{ __('messages.streak') }}
                </span>
                <div class="w-9 h-9 bg-purple-50 dark:bg-purple-500/10 rounded-xl
                            flex items-center justify-center">
                    <i class="fas fa-fire text-purple-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-[#1B254B] dark:text-white">
                {{--
                    Singkatan "hr" (hari) di samping angka — ini label satuan pendek
                    yang berbeda antara EN ("d" untuk day) dan ID ("hr" untuk hari).
                    Kita gunakan key 'hari_singkat' agar bisa berubah per bahasa.
                --}}
                {{ $currentStreak }}<span class="text-lg text-gray-400"> {{ __('messages.hari_singkat') }}</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">{{ __('messages.hari_berturut_aktif') }}</p>
        </div>
    </div>

    {{-- ── Progres per Mata Pelajaran ── --}}
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm
                border border-gray-100 dark:border-[#1e2130] p-6">
        <h3 class="text-base font-black text-[#1B254B] dark:text-white mb-5 flex items-center gap-2">
            <i class="fas fa-book-open text-blue-500"></i>
            {{ __('messages.progres_per_mapel') }}
        </h3>

        @if($nilaiPerMapel->isEmpty())
        <div class="text-center py-10">
            <i class="fas fa-inbox text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
            <p class="text-sm text-gray-400">{{ __('messages.belum_ada_nilai') }}</p>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($nilaiPerMapel as $mapel)
            @php $pct = min(100, round($mapel->rata_rata)); @endphp
            <div class="p-4 rounded-xl border border-gray-100 dark:border-[#252836]
                        bg-gray-50 dark:bg-[#111318]">
                <div class="flex justify-between items-center mb-3">
                    {{-- Nama mapel dari DB — tidak diterjemahkan --}}
                    <span class="text-sm font-bold text-[#1B254B] dark:text-gray-200 truncate mr-2">
                        {{ $mapel->mata_pelajaran }}
                    </span>
                    <span class="text-xs font-black px-2.5 py-1 rounded-full flex-shrink-0
                        {{ $mapel->grade === 'A' ? 'bg-green-50 text-green-600' :
                          ($mapel->grade === 'B' ? 'bg-blue-50 text-blue-600' :
                          ($mapel->grade === 'C' ? 'bg-yellow-50 text-yellow-600' :
                                                   'bg-red-50 text-red-600')) }}">
                        {{ $mapel->grade }} · {{ number_format($mapel->rata_rata, 0) }}
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-[#252836] rounded-full h-2 overflow-hidden">
                    <div class="{{ $mapel->fill }} h-2 rounded-full transition-all duration-1000"
                         style="width: {{ $pct }}%"></div>
                </div>
                {{-- ":count activities" — kata "aktivitas/activities" perlu diterjemahkan --}}
                <div class="flex justify-between mt-2 text-xs text-gray-400">
                    <span>{{ __('messages.jumlah_aktivitas', ['count' => $mapel->total]) }}</span>
                    <span>{{ $pct }}%</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── Charts Row ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Nilai 7 Hari Terakhir --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm
                    border border-gray-100 dark:border-[#1e2130] p-6">
            <h3 class="text-base font-black text-[#1B254B] dark:text-white mb-5 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-500"></i>
                {{ __('messages.nilai_7_hari') }}
            </h3>

            @if($nilaiMingguan->isEmpty())
            <div class="flex items-center justify-center h-24 text-sm text-gray-400">
                {{ __('messages.belum_ada_aktivitas_minggu') }}
            </div>
            @else
            <div class="flex items-end gap-2 h-24">
                @foreach($nilaiMingguan as $n)
                @php $h = max(4, round(($n->avg_nilai / 100) * 88)); @endphp
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-[10px] text-gray-400 font-mono">{{ round($n->avg_nilai) }}</span>
                    <div class="w-full bg-blue-500/80 rounded-t-sm" style="height: {{ $h }}px"></div>
                    {{--
                        Label hari di bawah bar chart sudah menggunakan Carbon isoFormat('ddd')
                        yang otomatis mengikuti locale Carbon yang aktif — bukan hardcoded.
                        Pastikan locale Carbon di-set sesuai bahasa aktif di AppServiceProvider
                        atau middleware: Carbon::setLocale(app()->getLocale())
                    --}}
                    <span class="text-[9px] text-gray-400">
                        {{ \Carbon\Carbon::parse($n->tgl)->isoFormat('ddd') }}
                    </span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Streak Heatmap --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm
                    border border-gray-100 dark:border-[#1e2130] p-6">
            <h3 class="text-base font-black text-[#1B254B] dark:text-white mb-5 flex items-center gap-2">
                <i class="fas fa-fire text-purple-500"></i>
                {{ __('messages.streak_belajar_4minggu') }}
            </h3>

            {{--
                Label hari heatmap: ['S','S','R','K','J','S','M'] adalah singkatan hari
                dalam bahasa Indonesia. Di Inggris: ['S','M','T','W','T','F','S'].
                Karena ini array dengan 7 elemen statis, solusi terbaik adalah membuat
                satu key translation yang berisi array-nya — tapi PHP translation tidak
                mendukung array. Alternatif: gunakan 7 key terpisah, atau lebih elegan:
                gunakan Carbon untuk generate label hari secara otomatis mengikuti locale.
                Kita pakai Carbon agar konsisten dengan bar chart di atas.
            --}}
            <div class="grid grid-cols-7 gap-1.5 mb-2">
                @foreach(\Carbon\CarbonPeriod::create('2024-01-01', '2024-01-07') as $d)
                <div class="text-[9px] text-center text-gray-400">
                    {{ $d->isoFormat('dd') }}
                </div>
                @endforeach
            </div>

            <div class="grid grid-cols-7 gap-1.5">
                @foreach($streakArray as $day)
                <div title="{{ $day['date'] }}"
                     class="aspect-square rounded-sm
                         {{ $day['today'] ? 'ring-2 ring-blue-500 ring-offset-1 ring-offset-white dark:ring-offset-[#1a1d28]' : '' }}
                         {{ $day['level'] === 0 ? 'bg-gray-100 dark:bg-[#252836]' :
                           ($day['level'] === 1 ? 'bg-blue-200 dark:bg-blue-900/40' :
                           ($day['level'] === 2 ? 'bg-blue-400 dark:bg-blue-600/60' :
                                                  'bg-blue-600 dark:bg-blue-500')) }}">
                </div>
                @endforeach
            </div>

            {{-- Legend streak: tiga level aktivitas --}}
            <div class="flex items-center gap-3 mt-3">
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-sm bg-gray-100 dark:bg-[#252836]"></div>
                    <span class="text-[10px] text-gray-400">{{ __('messages.streak_tidak_aktif') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-sm bg-blue-300 dark:bg-blue-700"></div>
                    <span class="text-[10px] text-gray-400">{{ __('messages.streak_sedikit') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-sm bg-blue-600 dark:bg-blue-500"></div>
                    <span class="text-[10px] text-gray-400">{{ __('messages.streak_intensif') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Aktivitas Terbaru ── --}}
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm
                border border-gray-100 dark:border-[#1e2130] p-6">
        <h3 class="text-base font-black text-[#1B254B] dark:text-white mb-5 flex items-center gap-2">
            <i class="fas fa-clock text-blue-500"></i>
            {{ __('messages.aktivitas_terbaru') }}
        </h3>

        @if($aktivitasTerbaru->isEmpty())
        <div class="text-center py-8">
            <i class="fas fa-inbox text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
            <p class="text-sm text-gray-400">{{ __('messages.belum_ada_aktivitas_tercatat') }}</p>
        </div>
        @else
        <div class="divide-y divide-gray-100 dark:divide-[#1e2130]">
            @foreach($aktivitasTerbaru as $ak)
            <div class="flex items-center gap-4 py-3 rounded-xl px-2 transition
                        hover:bg-gray-50 dark:hover:bg-[#111318]">

                {{-- Ikon tipe aktivitas --}}
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                    {{ $ak->tipe === 'quiz'     ? 'bg-blue-50 dark:bg-blue-500/10' :
                      ($ak->tipe === 'tugas'    ? 'bg-yellow-50 dark:bg-yellow-500/10' :
                      ($ak->tipe === 'ulangan'  ? 'bg-purple-50 dark:bg-purple-500/10' :
                                                  'bg-green-50 dark:bg-green-500/10')) }}">
                    <i class="text-sm
                        {{ $ak->tipe === 'quiz'    ? 'fas fa-question-circle text-blue-500' :
                          ($ak->tipe === 'tugas'   ? 'fas fa-tasks text-yellow-500' :
                          ($ak->tipe === 'ulangan' ? 'fas fa-graduation-cap text-purple-500' :
                                                     'fas fa-book text-green-500')) }}">
                    </i>
                </div>

                <div class="flex-1 min-w-0">
                    {{-- Judul aktivitas dari DB — tidak diterjemahkan --}}
                    <p class="text-sm font-bold text-[#1B254B] dark:text-gray-200 truncate">
                        {{ $ak->judul ?? $ak->mata_pelajaran }}
                    </p>
                    {{--
                        "Matematika · 2 hours ago" — nama mapel dari DB dibiarkan,
                        diffForHumans() otomatis mengikuti locale Carbon yang aktif.
                    --}}
                    <p class="text-xs text-gray-400">
                        {{ $ak->mata_pelajaran }} · {{ $ak->created_at->diffForHumans() }}
                    </p>
                </div>

                {{-- Nilai --}}
                <span class="text-sm font-black
                    {{ $ak->nilai >= 85 ? 'text-green-600' :
                      ($ak->nilai >= 75 ? 'text-blue-600' :
                      ($ak->nilai >= 65 ? 'text-yellow-600' : 'text-red-500')) }}">
                    {{ $ak->nilai }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection