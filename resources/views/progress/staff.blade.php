@extends('layouts.dash')

@section('title', 'Progres Kelas')

@section('content')
<div class="space-y-6">

    {{-- ── Greeting ── --}}
    <div>
        <h2 class="text-xl font-black text-[#1B254B] dark:text-white">
            Progres Kelas 
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Pantau perkembangan seluruh siswa ·
            <span class="font-bold text-[#1B254B] dark:text-gray-300">{{ $totalSiswa }} siswa aktif</span>
        </p>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm p-5 border border-gray-100 dark:border-[#1e2130] relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl bg-gradient-to-r from-blue-500 to-blue-400"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Rata-rata Kelas</span>
                <div class="w-9 h-9 bg-blue-50 dark:bg-blue-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-bar text-blue-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-[#1B254B] dark:text-white">{{ number_format($rataRataKelas, 1) }}</p>
            <p class="text-xs text-gray-400 mt-1">Dari seluruh mata pelajaran</p>
        </div>

        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm p-5 border border-gray-100 dark:border-[#1e2130] relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl bg-gradient-to-r from-green-500 to-emerald-400"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Lulus KKM</span>
                <div class="w-9 h-9 bg-green-50 dark:bg-green-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-check text-green-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-[#1B254B] dark:text-white">
                {{ $lulusKkm }}<span class="text-lg text-gray-400">/{{ $totalSiswa }}</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">
                {{ $totalSiswa > 0 ? round(($lulusKkm / $totalSiswa) * 100) : 0 }}% siswa lulus KKM {{ $kkm }}
            </p>
        </div>

        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm p-5 border border-gray-100 dark:border-[#1e2130] relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl bg-gradient-to-r from-yellow-500 to-amber-400"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Kehadiran Rata</span>
                <div class="w-9 h-9 bg-yellow-50 dark:bg-yellow-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-check text-yellow-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-[#1B254B] dark:text-white">
                {{ $kehadiranRata }}<span class="text-lg text-gray-400">%</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">Rata-rata seluruh kelas</p>
        </div>

        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm p-5 border border-gray-100 dark:border-[#1e2130] relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl bg-gradient-to-r from-purple-500 to-violet-400"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Tugas Masuk</span>
                <div class="w-9 h-9 bg-purple-50 dark:bg-purple-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-purple-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-[#1B254B] dark:text-white">{{ $tugasSelesaiSemua }}</p>
            <p class="text-xs text-gray-400 mt-1">total tugas dikumpulkan</p>
        </div>
    </div>

    {{-- ── Distribusi & Perhatian ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Distribusi nilai --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm border border-gray-100 dark:border-[#1e2130] p-6">
            <h3 class="text-base font-black text-[#1B254B] dark:text-white mb-5 flex items-center gap-2">
                <i class="fas fa-chart-pie text-blue-500"></i> Distribusi Nilai Kelas
            </h3>
            @php
                $total = $distribusiA + $distribusiB + $distribusiC + $distribusiD;
                $bars  = [
                    ['label' => 'A (≥ 85)',   'count' => $distribusiA, 'color' => 'bg-green-500',  'text' => 'text-green-600',  'bg' => 'bg-green-50 dark:bg-green-500/10'],
                    ['label' => 'B (75 – 84)', 'count' => $distribusiB, 'color' => 'bg-blue-500',   'text' => 'text-blue-600',   'bg' => 'bg-blue-50 dark:bg-blue-500/10'],
                    ['label' => 'C (65 – 74)', 'count' => $distribusiC, 'color' => 'bg-yellow-500', 'text' => 'text-yellow-600', 'bg' => 'bg-yellow-50 dark:bg-yellow-500/10'],
                    ['label' => 'D (< 65)',    'count' => $distribusiD, 'color' => 'bg-red-500',    'text' => 'text-red-600',    'bg' => 'bg-red-50 dark:bg-red-500/10'],
                ];
            @endphp
            <div class="space-y-3">
                @foreach($bars as $b)
                @php $pct = $total > 0 ? round(($b['count'] / $total) * 100) : 0; @endphp
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-bold text-gray-600 dark:text-gray-300">Nilai {{ $b['label'] }}</span>
                        <span class="text-xs font-black {{ $b['text'] }}">{{ $b['count'] }} siswa ({{ $pct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-[#252836] rounded-full h-2 overflow-hidden">
                        <div class="{{ $b['color'] }} h-2 rounded-full transition-all duration-1000"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Perhatian khusus --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm border border-gray-100 dark:border-[#1e2130] p-6">
            <h3 class="text-base font-black text-[#1B254B] dark:text-white mb-5 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-yellow-500"></i> Perhatian Khusus
            </h3>
            @if($perhatianSiswa->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 gap-2">
                    <i class="fas fa-check-circle text-3xl text-green-500"></i>
                    <p class="text-sm text-gray-400">Semua siswa dalam kondisi baik! 🎉</p>
                </div>
            @else
            <div class="space-y-2">
                @foreach($perhatianSiswa as $s)
                @php $isDanger = $s['avg'] < 65 || $s['kehadiran_pct'] < 75; @endphp
                <div class="flex items-start gap-3 p-3 rounded-xl border
                    @if($isDanger) bg-red-50 dark:bg-red-500/5 border-red-200 dark:border-red-500/20
                    @else bg-yellow-50 dark:bg-yellow-500/5 border-yellow-200 dark:border-yellow-500/20 @endif">
                    <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0
                        @if($isDanger) bg-red-500 @else bg-yellow-500 @endif"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-[#1B254B] dark:text-gray-200">{{ $s['nama'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Rata-rata {{ $s['avg'] }}
                            · Kehadiran {{ $s['kehadiran'] }}
                            @if($s['trend'] === 'turun')
                                <span class="text-red-500 font-bold">· Menurun ↓</span>
                            @endif
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ── Tabel Ranking Siswa ── --}}
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm border border-gray-100 dark:border-[#1e2130] overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-[#1e2130]">
            <h3 class="text-base font-black text-[#1B254B] dark:text-white flex items-center gap-2">
                <i class="fas fa-trophy text-yellow-500"></i> Peringkat Siswa
            </h3>
        </div>

        @if($siswaData->isEmpty())
            <div class="text-center py-10">
                <i class="fas fa-users text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-sm text-gray-400">Belum ada data siswa.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-[#1e2130]">
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">#</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Siswa</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Rata-rata</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Tugas</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Kehadiran</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Progress</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Trend</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#1e2130]">
                    @foreach($siswaData as $index => $s)
                    <tr class="hover:bg-gray-50 dark:hover:bg-[#111318] transition">
                        <td class="px-6 py-3">
                            @if($index === 0)
                                <span class="w-7 h-7 rounded-lg bg-yellow-50 dark:bg-yellow-500/10 text-yellow-600 text-xs font-black flex items-center justify-center">🥇</span>
                            @elseif($index === 1)
                                <span class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-500/10 text-gray-500 text-xs font-black flex items-center justify-center">🥈</span>
                            @elseif($index === 2)
                                <span class="w-7 h-7 rounded-lg bg-orange-50 dark:bg-orange-500/10 text-orange-600 text-xs font-black flex items-center justify-center">🥉</span>
                            @else
                                <span class="w-7 h-7 rounded-lg bg-gray-50 dark:bg-[#252836] text-gray-400 text-xs font-bold flex items-center justify-center">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ substr($s['nama'], 0, 1) }}
                                </div>
                                <span class="text-sm font-bold text-[#1B254B] dark:text-gray-200">{{ $s['nama'] }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-black px-2.5 py-1 rounded-full
                                @if($s['grade'] === 'A') bg-green-50 text-green-600
                                @elseif($s['grade'] === 'B') bg-blue-50 text-blue-600
                                @elseif($s['grade'] === 'C') bg-yellow-50 text-yellow-600
                                @else bg-red-50 text-red-600 @endif">
                                {{ $s['avg'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">
                            {{ $s['tugas'] }}
                        </td>
                        <td class="px-4 py-3 text-sm font-mono
                            @if($s['kehadiran_pct'] >= 85) text-green-600
                            @elseif($s['kehadiran_pct'] >= 75) text-blue-600
                            @else text-red-500 @endif">
                            {{ $s['kehadiran'] }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-20 bg-gray-100 dark:bg-[#252836] rounded-full h-1.5 overflow-hidden">
                                    <div class="{{ $s['fill'] }} h-1.5 rounded-full"
                                         style="width: {{ min(100, $s['avg']) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-400">{{ $s['avg'] }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($s['trend'] === 'naik')
                                <span class="text-green-500 font-black text-base" title="Meningkat">↑</span>
                            @elseif($s['trend'] === 'turun')
                                <span class="text-red-500 font-black text-base" title="Menurun">↓</span>
                            @else
                                <span class="text-blue-400 font-black text-base" title="Stabil">→</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ── Rata-rata per Mapel (Kelas) ── --}}
    @if($rataPerMapel->isNotEmpty())
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl shadow-sm border border-gray-100 dark:border-[#1e2130] p-6">
        <h3 class="text-base font-black text-[#1B254B] dark:text-white mb-5 flex items-center gap-2">
            <i class="fas fa-book-open text-blue-500"></i> Rata-rata Nilai per Mata Pelajaran
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($rataPerMapel as $index => $m)
            @php $pct = min(100, round($m->rata_rata)); @endphp
            <div class="p-4 rounded-xl border border-gray-100 dark:border-[#252836] bg-gray-50 dark:bg-[#111318]">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm font-bold text-[#1B254B] dark:text-gray-200 truncate mr-2">
                        {{ $m->mata_pelajaran }}
                    </span>
                    <span class="text-xs font-black px-2.5 py-1 rounded-full flex-shrink-0
                        @if($m->grade === 'A') bg-green-50 text-green-600
                        @elseif($m->grade === 'B') bg-blue-50 text-blue-600
                        @elseif($m->grade === 'C') bg-yellow-50 text-yellow-600
                        @else bg-red-50 text-red-600 @endif">
                        {{ number_format($m->rata_rata, 1) }}
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-[#252836] rounded-full h-2 overflow-hidden">
                    <div class="{{ $m->fill }} h-2 rounded-full transition-all duration-1000"
                         style="width: {{ $pct }}%"></div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-400">
                    <span>{{ $m->lulus ?? 0 }}/{{ $totalSiswa }} lulus KKM</span>
                    <span>{{ $pct }}%</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
