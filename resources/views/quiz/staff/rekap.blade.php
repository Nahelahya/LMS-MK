@extends('layouts.dash')
@section('title', 'Rekap Quiz — ' . $kelas->nama_kelas)

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">

    {{-- Header --}}
    <div>
        <a href="{{ route('kelas.show', $kelas) }}"
           class="text-gray-400 hover:text-blue-500 text-sm inline-flex items-center gap-1 mb-2 transition">
            <i class="fas fa-arrow-left text-xs"></i> {{ $kelas->nama_kelas }}
        </a>
        <h1 class="text-2xl font-black text-[#1B254B] dark:text-gray-100">Rekap Quiz Adaptif</h1>
        <p class="text-sm text-gray-400 mt-0.5">{{ $kelas->mata_pelajaran }}</p>
    </div>

    {{-- Stat rata-rata per mata pelajaran --}}
    @if($avgPerMapel->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($avgPerMapel as $avg)
        <div class="bg-white dark:bg-[#1a1d28] rounded-2xl p-5 border border-gray-50 dark:border-[#1e2130]">
            <p class="text-xs text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider mb-1">
                {{ $avg->mata_pelajaran }}
            </p>
            <p class="text-3xl font-black {{ $avg->avg_skor >= 70 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                {{ $avg->avg_skor }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $avg->jumlah_peserta }} siswa sudah quiz</p>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Tabel rekap per siswa --}}
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl border border-gray-50 dark:border-[#1e2130] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 dark:border-[#1e2130] flex justify-between items-center">
            <h2 class="font-black text-[#1B254B] dark:text-gray-100">
                <i class="fas fa-users text-indigo-500 mr-2"></i>Hasil Quiz per Siswa
            </h2>
            <span class="text-xs text-gray-400">{{ $siswaList->count() }} siswa terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-[11px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-[#1e2130]">
                        <th class="px-6 py-3">Nama Siswa</th>
                        <th class="px-6 py-3">Mata Pelajaran</th>
                        <th class="px-6 py-3">Skor Terbaik</th>
                        <th class="px-6 py-3">Jumlah Quiz</th>
                        <th class="px-6 py-3">Terakhir Quiz</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="text-[#1B254B] dark:text-gray-200">
                    @foreach($siswaList as $siswa)
                    @php
                        $sessions = $lastSessions->get($siswa->id, collect());
                    @endphp

                    @if($sessions->isEmpty())
                    {{-- Siswa belum pernah quiz --}}
                    <tr class="border-b border-gray-50 dark:border-[#1e2130] last:border-0">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-[#1e2130] flex items-center justify-center text-xs font-black text-gray-500 dark:text-gray-400">
                                    {{ substr($siswa->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-sm">{{ $siswa->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $siswa->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-400" colspan="4">—</td>
                        <td class="px-6 py-4">
                            <span class="text-[10px] font-black px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-[#1e2130] text-gray-400 dark:text-gray-500">
                                Belum Quiz
                            </span>
                        </td>
                    </tr>
                    @else
                    {{-- Siswa sudah quiz — tampilkan per mata pelajaran --}}
                    @foreach($sessions as $i => $sess)
                    <tr class="border-b border-gray-50 dark:border-[#1e2130] last:border-0 hover:bg-gray-50/50 dark:hover:bg-[#111318] transition">
                        <td class="px-6 py-4">
                            @if($i === 0)
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-xs font-black text-blue-600 dark:text-blue-400">
                                    {{ substr($siswa->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-sm">{{ $siswa->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $siswa->email }}</p>
                                </div>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400">
                            {{ $sess->mata_pelajaran }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-black text-lg {{ $sess->skor_terbaik >= 75 ? 'text-green-600 dark:text-green-400' : ($sess->skor_terbaik >= 60 ? 'text-blue-600 dark:text-blue-400' : 'text-red-500 dark:text-red-400') }}">
                                {{ $sess->skor_terbaik }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs">
                            {{ $sess->jumlah_quiz }}x
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-xs">
                            {{ \Carbon\Carbon::parse($sess->terakhir_quiz)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $skor = $sess->skor_terbaik;
                                [$label, $cls] = $skor >= 75
                                    ? ['Advance',  'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400']
                                    : ($skor >= 60
                                        ? ['Normal',   'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400']
                                        : ['Remedial', 'bg-red-100 dark:bg-red-900/30 text-red-500 dark:text-red-400']);
                            @endphp
                            <span class="text-[10px] font-black px-2.5 py-1 rounded-lg {{ $cls }}">
                                {{ $label }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection