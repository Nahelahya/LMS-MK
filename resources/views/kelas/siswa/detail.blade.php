@extends('layouts.dash')
@section('title', 'Detail Siswa — ' . $siswa->name)

@section('content')
<div class="space-y-6 max-w-5xl mx-auto pb-12">

    {{-- Breadcrumb --}}
    <div>
        <a href="{{ route('dashboard') }}"
           class="text-gray-400 hover:text-blue-500 text-sm inline-flex items-center gap-1 mb-2 transition">
            <i class="fas fa-arrow-left text-xs"></i> Dashboard
        </a>
        <h1 class="text-2xl font-black text-[#1B254B] dark:text-gray-100">Detail Siswa</h1>
    </div>

    {{-- Profile card --}}
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl p-6 border border-gray-50 dark:border-[#1e2130] flex items-center gap-5">
        <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-2xl font-black text-blue-600 dark:text-blue-400 flex-shrink-0">
            {{ substr($siswa->name, 0, 1) }}
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="text-xl font-black text-[#1B254B] dark:text-gray-100">{{ $siswa->name }}</h2>
            <p class="text-sm text-gray-400 dark:text-gray-500">{{ $siswa->email }}</p>
            <div class="flex flex-wrap gap-2 mt-2">
                @forelse($kelasDiikuti as $k)
                <span class="text-xs bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-2.5 py-1 rounded-lg font-semibold">
                    {{ $k->nama_kelas }} · {{ $k->mata_pelajaran }}
                </span>
                @empty
                <span class="text-xs text-gray-400">Belum terdaftar di kelas manapun</span>
                @endforelse
            </div>
        </div>
        @php $atRisk = $progress->contains('is_at_risk', true); @endphp
        <div class="flex-shrink-0">
            @if($atRisk)
            <span class="inline-flex items-center gap-1.5 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold px-3 py-1.5 rounded-xl">
                <i class="fas fa-exclamation-triangle"></i> Perlu Perhatian
            </span>
            @else
            <span class="inline-flex items-center gap-1.5 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-bold px-3 py-1.5 rounded-xl">
                <i class="fas fa-check-circle"></i> Normal
            </span>
            @endif
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl p-5 border border-gray-50 dark:border-[#1e2130]">
            <p class="text-xs text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider mb-1">Rata-rata Nilai</p>
            <p class="text-3xl font-black text-[#1B254B] dark:text-gray-100">
                {{ $avgPerMapel->isNotEmpty() ? number_format($avgPerMapel->avg('avg_nilai'), 1) : '-' }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">dari {{ $nilais->count() }} penilaian</p>
        </div>
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl p-5 border border-gray-50 dark:border-[#1e2130]">
            <p class="text-xs text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider mb-1">Kehadiran</p>
            <p class="text-3xl font-black {{ $pctHadir >= 75 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                {{ $pctHadir }}%
            </p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $attendance->hadir ?? 0 }}/{{ $attendance->total ?? 0 }} pertemuan</p>
        </div>
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl p-5 border border-gray-50 dark:border-[#1e2130]">
            <p class="text-xs text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider mb-1">Tidak Hadir</p>
            <p class="text-3xl font-black text-[#1B254B] dark:text-gray-100">{{ $attendance->alfa ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Sakit: {{ $attendance->sakit ?? 0 }} · Izin: {{ $attendance->izin ?? 0 }}</p>
        </div>
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl p-5 border border-gray-50 dark:border-[#1e2130]">
            <p class="text-xs text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider mb-1">Skor Terakhir</p>
            <p class="text-3xl font-black text-[#1B254B] dark:text-gray-100">
                {{ $progress->isNotEmpty() ? $progress->max('last_score') : '-' }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">
                {{ $progress->isNotEmpty() ? $progress->first()->status_adaptif : 'Belum ada data' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Nilai per mata pelajaran --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl border border-gray-50 dark:border-[#1e2130] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 dark:border-[#1e2130]">
                <h3 class="font-black text-[#1B254B] dark:text-gray-100 text-base">
                    <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>Nilai per Mata Pelajaran
                </h3>
            </div>
            @forelse($avgPerMapel as $mapel)
            <div class="px-6 py-4 border-b border-gray-50 dark:border-[#1e2130] last:border-0">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-bold text-[#1B254B] dark:text-gray-100">{{ $mapel->mata_pelajaran }}</span>
                    <span class="text-sm font-black {{ $mapel->avg_nilai >= 70 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                        {{ $mapel->avg_nilai }}
                    </span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-[#1e2130] rounded-full h-1.5 overflow-hidden">
                    <div class="h-full rounded-full {{ $mapel->avg_nilai >= 70 ? 'bg-green-500' : 'bg-red-400' }}"
                         style="width: {{ min($mapel->avg_nilai, 100) }}%"></div>
                </div>
                <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                    <span>Min: {{ $mapel->terendah }}</span>
                    <span>{{ $mapel->jumlah }} penilaian</span>
                    <span>Max: {{ $mapel->tertinggi }}</span>
                </div>
            </div>
            @empty
            <div class="py-10 text-center text-gray-400 dark:text-gray-600">
                <i class="fas fa-inbox text-3xl mb-2 block opacity-40"></i>
                <p class="text-sm">Belum ada data nilai</p>
            </div>
            @endforelse
        </div>

        {{-- Riwayat kehadiran --}}
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl border border-gray-50 dark:border-[#1e2130] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 dark:border-[#1e2130]">
                <h3 class="font-black text-[#1B254B] dark:text-gray-100 text-base">
                    <i class="fas fa-calendar-check text-green-500 mr-2"></i>Riwayat Kehadiran
                </h3>
            </div>
            @forelse($riwayatHadir as $h)
            @php
                $badgeMap = [
                    'hadir' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                    'sakit' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
                    'izin'  => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                    'alfa'  => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
                ];
                $badgeClass = $badgeMap[$h->status] ?? 'bg-gray-100 text-gray-500';
            @endphp
            <div class="flex items-center justify-between px-6 py-3 border-b border-gray-50 dark:border-[#1e2130] last:border-0">
                <span class="text-sm font-semibold text-[#1B254B] dark:text-gray-200">
                    {{ \Carbon\Carbon::parse($h->tanggal)->translatedFormat('d M Y') }}
                </span>
                <span class="text-[10px] font-black px-2.5 py-1 rounded-lg uppercase {{ $badgeClass }}">
                    {{ $h->status }}
                </span>
            </div>
            @empty
            <div class="py-10 text-center text-gray-400 dark:text-gray-600">
                <i class="fas fa-calendar text-3xl mb-2 block opacity-40"></i>
                <p class="text-sm">Belum ada riwayat kehadiran</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Tabel nilai lengkap --}}
    @if($nilais->isNotEmpty())
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl border border-gray-50 dark:border-[#1e2130] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 dark:border-[#1e2130]">
            <h3 class="font-black text-[#1B254B] dark:text-gray-100 text-base">
                <i class="fas fa-list text-blue-500 mr-2"></i>Riwayat Penilaian Lengkap
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-[11px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-[#1e2130]">
                        <th class="px-6 py-3">Mata Pelajaran</th>
                        <th class="px-6 py-3">Judul</th>
                        <th class="px-6 py-3">Tipe</th>
                        <th class="px-6 py-3">Nilai</th>
                        <th class="px-6 py-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="text-[#1B254B] dark:text-gray-200">
                    @foreach($nilais as $n)
                    <tr class="border-b border-gray-50 dark:border-[#1e2130] last:border-0 hover:bg-gray-50/50 dark:hover:bg-[#111318] transition">
                        <td class="px-6 py-3 font-semibold">{{ $n->mata_pelajaran }}</td>
                        <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $n->judul ?? '-' }}</td>
                        <td class="px-6 py-3">
                            <span class="text-[10px] font-black bg-gray-100 dark:bg-[#1e2130] text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-lg uppercase">
                                {{ $n->tipe }}
                            </span>
                        </td>
                        <td class="px-6 py-3 font-black {{ $n->nilai >= 70 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                            {{ $n->nilai }}
                        </td>
                        <td class="px-6 py-3 text-gray-400 text-xs">
                            {{ \Carbon\Carbon::parse($n->created_at)->translatedFormat('d M Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection