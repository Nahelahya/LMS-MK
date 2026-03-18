@extends('layouts.dash')

@section('title', 'Progres Belajar')

@section('content')
<div class="space-y-6">

    {{-- ── Greeting ── --}}
    <div>
        <h2 class="text-xl font-black text-[#1B254B] dark:text-white">
            Hai, {{ $user->name }} 👋
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Berikut ringkasan perkembangan belajarmu · Semester Genap {{ date('Y') - 1 }}/{{ date('Y') }}
        </p>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Rata-rata nilai --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm p-5 border border-gray-100 dark:border-[#1e2130] relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl bg-gradient-to-r from-blue-500 to-blue-400"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Rata-rata</span>
                <div class="w-9 h-9 bg-blue-50 dark:bg-blue-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-bar text-blue-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-[#1B254B] dark:text-white">{{ number_format($rataRata, 1) }}</p>
            <p class="text-xs text-gray-400 mt-1">
                Grade
                <span class="font-bold
                    @if($rataRata >= 85) text-green-600 @elseif($rataRata >= 75) text-blue-600
                    @elseif($rataRata >= 65) text-yellow-600 @else text-red-600 @endif">
                    {{ $rataRata >= 85 ? 'A' : ($rataRata >= 75 ? 'B' : ($rataRata >= 65 ? 'C' : 'D')) }}
                </span>
            </p>
        </div>

        {{-- Tugas selesai --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm p-5 border border-gray-100 dark:border-[#1e2130] relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl bg-gradient-to-r from-green-500 to-emerald-400"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Tugas</span>
                <div class="w-9 h-9 bg-green-50 dark:bg-green-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-[#1B254B] dark:text-white">{{ $tugasSelesai }}</p>
            <p class="text-xs text-gray-400 mt-1">tugas dikumpulkan</p>
        </div>

        {{-- Kehadiran --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm p-5 border border-gray-100 dark:border-[#1e2130] relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl bg-gradient-to-r from-yellow-500 to-amber-400"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Kehadiran</span>
                <div class="w-9 h-9 bg-yellow-50 dark:bg-yellow-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-check text-yellow-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-[#1B254B] dark:text-white">
                {{ $persenHadir }}<span class="text-lg text-gray-400">%</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">{{ $totalHadir }} dari {{ $totalAttendance }} hari</p>
        </div>

        {{-- Streak --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm p-5 border border-gray-100 dark:border-[#1e2130] relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl bg-gradient-to-r from-purple-500 to-violet-400"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Streak</span>
                <div class="w-9 h-9 bg-purple-50 dark:bg-purple-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-fire text-purple-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-[#1B254B] dark:text-white">
                {{ $currentStreak }}<span class="text-lg text-gray-400"> hr</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">hari berturut aktif 🔥</p>
        </div>
    </div>

    {{-- ── Progres per Mapel ── --}}
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm border border-gray-100 dark:border-[#1e2130] p-6">
        <h3 class="text-base font-black text-[#1B254B] dark:text-white mb-5 flex items-center gap-2">
            <i class="fas fa-book-open text-blue-500"></i> Progres per Mata Pelajaran
        </h3>

        @if($nilaiPerMapel->isEmpty())
            <div class="text-center py-10">
                <i class="fas fa-inbox text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-sm text-gray-400">Belum ada data nilai. Mulai kerjakan tugas!</p>
            </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($nilaiPerMapel as $mapel)
            @php $pct = min(100, round($mapel->rata_rata)); @endphp
            <div class="p-4 rounded-xl border border-gray-100 dark:border-[#252836] bg-gray-50 dark:bg-[#111318]">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm font-bold text-[#1B254B] dark:text-gray-200 truncate mr-2">
                        {{ $mapel->mata_pelajaran }}
                    </span>
                    <span class="text-xs font-black px-2.5 py-1 rounded-full flex-shrink-0
                        @if($mapel->grade === 'A') bg-green-50 text-green-600
                        @elseif($mapel->grade === 'B') bg-blue-50 text-blue-600
                        @elseif($mapel->grade === 'C') bg-yellow-50 text-yellow-600
                        @else bg-red-50 text-red-600 @endif">
                        {{ $mapel->grade }} · {{ number_format($mapel->rata_rata, 0) }}
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-[#252836] rounded-full h-2 overflow-hidden">
                    <div class="{{ $mapel->fill }} h-2 rounded-full transition-all duration-1000"
                         style="width: {{ $pct }}%"></div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-400">
                    <span>{{ $mapel->total }} aktivitas</span>
                    <span>{{ $pct }}%</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── Charts Row ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Nilai 7 hari --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm border border-gray-100 dark:border-[#1e2130] p-6">
            <h3 class="text-base font-black text-[#1B254B] dark:text-white mb-5 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-500"></i> Nilai 7 Hari Terakhir
            </h3>
            @if($nilaiMingguan->isEmpty())
                <div class="flex items-center justify-center h-24 text-sm text-gray-400">
                    Belum ada aktivitas minggu ini
                </div>
            @else
            <div class="flex items-end gap-2 h-24">
                @foreach($nilaiMingguan as $n)
                @php $h = max(4, round(($n->avg_nilai / 100) * 88)); @endphp
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-[10px] text-gray-400 font-mono">{{ round($n->avg_nilai) }}</span>
                    <div class="w-full bg-blue-500/80 rounded-t-sm" style="height: {{ $h }}px"></div>
                    <span class="text-[9px] text-gray-400">
                        {{ \Carbon\Carbon::parse($n->tgl)->isoFormat('ddd') }}
                    </span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Streak heatmap --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm border border-gray-100 dark:border-[#1e2130] p-6">
            <h3 class="text-base font-black text-[#1B254B] dark:text-white mb-5 flex items-center gap-2">
                <i class="fas fa-fire text-purple-500"></i> Streak Belajar (4 Minggu)
            </h3>
            <div class="grid grid-cols-7 gap-1.5 mb-2">
                @foreach(['S','S','R','K','J','S','M'] as $d)
                <div class="text-[9px] text-center text-gray-400">{{ $d }}</div>
                @endforeach
                @foreach($streakArray as $day)
                <div title="{{ $day['date'] }}"
                     class="aspect-square rounded-sm
                     @if($day['today']) ring-2 ring-blue-500 ring-offset-1 ring-offset-white dark:ring-offset-[#1a1d28] @endif
                     @if($day['level'] === 0) bg-gray-100 dark:bg-[#252836]
                     @elseif($day['level'] === 1) bg-blue-200 dark:bg-blue-900/40
                     @elseif($day['level'] === 2) bg-blue-400 dark:bg-blue-600/60
                     @else bg-blue-600 dark:bg-blue-500 @endif">
                </div>
                @endforeach
            </div>
            <div class="flex items-center gap-3 mt-3">
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-sm bg-gray-100 dark:bg-[#252836]"></div>
                    <span class="text-[10px] text-gray-400">Tidak aktif</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-sm bg-blue-300 dark:bg-blue-700"></div>
                    <span class="text-[10px] text-gray-400">Sedikit</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-sm bg-blue-600 dark:bg-blue-500"></div>
                    <span class="text-[10px] text-gray-400">Intensif</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Aktivitas Terbaru ── --}}
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm border border-gray-100 dark:border-[#1e2130] p-6">
        <h3 class="text-base font-black text-[#1B254B] dark:text-white mb-5 flex items-center gap-2">
            <i class="fas fa-clock text-blue-500"></i> Aktivitas Terbaru
        </h3>

        @if($aktivitasTerbaru->isEmpty())
            <div class="text-center py-8">
                <i class="fas fa-inbox text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-sm text-gray-400">Belum ada aktivitas tercatat.</p>
            </div>
        @else
        <div class="divide-y divide-gray-100 dark:divide-[#1e2130]">
            @foreach($aktivitasTerbaru as $ak)
            <div class="flex items-center gap-4 py-3 hover:bg-gray-50 dark:hover:bg-[#111318] rounded-xl px-2 transition">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                    @if($ak->tipe === 'quiz') bg-blue-50 dark:bg-blue-500/10
                    @elseif($ak->tipe === 'tugas') bg-yellow-50 dark:bg-yellow-500/10
                    @elseif($ak->tipe === 'ulangan') bg-purple-50 dark:bg-purple-500/10
                    @else bg-green-50 dark:bg-green-500/10 @endif">
                    <i class="
                        @if($ak->tipe === 'quiz') fas fa-question-circle text-blue-500
                        @elseif($ak->tipe === 'tugas') fas fa-tasks text-yellow-500
                        @elseif($ak->tipe === 'ulangan') fas fa-graduation-cap text-purple-500
                        @else fas fa-book text-green-500 @endif
                        text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-[#1B254B] dark:text-gray-200 truncate">
                        {{ $ak->judul ?? $ak->mata_pelajaran }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ $ak->mata_pelajaran }} · {{ $ak->created_at->diffForHumans() }}
                    </p>
                </div>
                <span class="text-sm font-black
                    @if($ak->nilai >= 85) text-green-600
                    @elseif($ak->nilai >= 75) text-blue-600
                    @elseif($ak->nilai >= 65) text-yellow-600
                    @else text-red-500 @endif">
                    {{ $ak->nilai }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection
