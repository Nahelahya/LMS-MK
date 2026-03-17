@extends('layouts.dash')

@section('title', 'Dashboard Murid')

@section('content')

{{-- Greeting --}}
<div class="mb-6">
    <h2 class="text-2xl font-black text-[#1B254B]">Halo, {{ auth()->user()->name }} 👋</h2>
    <p class="text-sm text-gray-400 mt-1">Semangat belajar hari ini!</p>
</div>

{{-- Stat cards atas --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Skor Terakhir</p>
        <p class="text-3xl font-black text-[#1B254B]">{{ $my_progress->last_score ?? 0 }}</p>
        <p class="text-xs text-blue-400 mt-1 font-semibold">
            @if($my_progress && $my_progress->status_adaptif)
                {{ $my_progress->status_adaptif }}
            @else
                —
            @endif
        </p>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Streak Belajar</p>
        <p class="text-3xl font-black text-[#1B254B]">{{ $streak }}</p>
        <p class="text-xs text-orange-400 mt-1 font-semibold">hari berturut-turut</p>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Course Diikuti</p>
        <p class="text-3xl font-black text-[#1B254B]">{{ $my_courses->count() }}</p>
        <p class="text-xs text-green-400 mt-1 font-semibold">course aktif</p>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Waktu Minggu Ini</p>
        <p class="text-3xl font-black text-[#1B254B]">{{ round($weekly_hours->sum('hours'), 1) }}</p>
        <p class="text-xs text-purple-400 mt-1 font-semibold">jam belajar</p>
    </div>

</div>

{{-- Konten utama --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── KOLOM KIRI (2/3) ── --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Course Saya --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-black text-[#1B254B] text-base">Course Saya</h3>
                <span class="text-xs text-gray-400 font-semibold">{{ $my_courses->count() }} course</span>
            </div>

            <div class="space-y-5">
            @forelse($my_courses as $course)
            @php
                // ✅ Gunakan my_progress yang sudah di-eager load dari controller (tidak N+1)
                $progress = $course->my_progress;
                $pct = $progress->completion_percentage ?? 0;

                $barClass = match(true) {
                    $pct >= 100 => 'bg-green-500',
                    $pct >= 60  => 'bg-blue-500',
                    default     => 'bg-indigo-400',
                };

                $badgeClass = match(true) {
                    $pct >= 100 => 'bg-green-50 text-green-600',
                    $pct >= 60  => 'bg-blue-50 text-blue-600',
                    default     => 'bg-indigo-50 text-indigo-500',
                };
            @endphp

            <div class="group">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-bold text-[#1B254B] text-sm">{{ $course->nama_course }}</p>
                        <div class="flex items-center gap-3 mt-1">
                            {{-- Waktu tersisa --}}
                            <span class="flex items-center gap-1 text-xs text-gray-400">
                                <i class="fas fa-clock text-[10px]"></i>
                                @if($pct >= 100)
                                    Selesai
                                @elseif(isset($course->estimated_hours) && $course->estimated_hours > 0)
                                    @php
                                        $rem = $course->estimated_hours * (1 - $pct / 100);
                                        $h   = (int) $rem;
                                        $m   = (int) round(($rem - $h) * 60);
                                    @endphp
                                    {{ $h > 0 ? $h.' jam' : '' }}{{ $m > 0 ? ' '.$m.' mnt' : '' }} tersisa
                                @else
                                    Sedang berjalan
                                @endif
                            </span>

                            {{-- Tenggat --}}
                            @if(isset($course->deadline) && $course->deadline)
                            <span class="flex items-center gap-1 text-xs text-gray-400">
                                <i class="fas fa-calendar text-[10px]"></i>
                                Tenggat {{ \Carbon\Carbon::parse($course->deadline)->translatedFormat('d M') }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <span class="text-xs font-black px-3 py-1 rounded-full {{ $badgeClass }}">
                        {{ $pct }}%
                    </span>
                </div>

                {{-- Progress bar --}}
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div
                        class="progress-bar h-2 rounded-full transition-all duration-1000 ease-out {{ $barClass }}"
                        data-pct="{{ $pct }}"
                        style="width: 0%"
                    ></div>
                </div>

                {{-- Status adaptif & at risk --}}
                <div class="flex items-center gap-2 mt-2">
                    @if($progress && $progress->status_adaptif)
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                        {{ $progress->status_adaptif }}
                    </span>
                    @endif
                    @if($progress && $progress->is_at_risk)
                    <span class="text-[10px] font-bold bg-yellow-50 text-yellow-600 px-2 py-0.5 rounded-full">
                        ⚠ Perlu perhatian
                    </span>
                    @endif
                </div>
            </div>

            @empty
            <p class="text-sm text-gray-400 text-center py-4">Belum ada course yang diikuti.</p>
            @endforelse
            </div>
        </div>

        {{-- Bar chart mingguan --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h3 class="font-black text-[#1B254B] text-base mb-5">Aktivitas Belajar Minggu Ini</h3>
            <div class="flex items-end gap-2 h-24">
                @php $maxH = $weekly_hours->max('hours') ?: 1; @endphp
                @foreach($weekly_hours as $i => $day)
                @php
                    $heightPct = $day['hours'] > 0 ? round(($day['hours'] / $maxH) * 100) : 4;
                    $isToday   = $i === 6;
                @endphp
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-[10px] font-bold {{ $isToday ? 'text-blue-500' : 'text-gray-300' }}">
                        {{ $day['hours'] > 0 ? $day['hours'].'j' : '' }}
                    </span>
                    <div class="w-full rounded-t-lg weekly-bar {{ $isToday ? 'bg-blue-500' : 'bg-blue-100' }} transition-all duration-700"
                         style="height: 0%; min-height: 3px;"
                         data-height="{{ $heightPct }}">
                    </div>
                    <span class="text-[10px] font-bold {{ $isToday ? 'text-blue-500' : 'text-gray-400' }}">
                        {{ $day['label'] }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ── KOLOM KANAN (1/3) ── --}}
    <div class="space-y-6">

        {{-- Quiz Adaptif --}}
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-bolt text-white"></i>
            </div>
            <h3 class="font-black text-lg mb-1">Quiz Adaptif</h3>
            <p class="text-blue-100 text-xs mb-4">Soal disesuaikan dengan level belajarmu</p>
            <a href="#"
               class="block w-full bg-white text-blue-600 text-center text-sm font-black py-3 rounded-xl hover:bg-blue-50 transition">
                Mulai Sekarang
            </a>
        </div>

        {{-- Log Aktivitas --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h3 class="font-black text-[#1B254B] text-base mb-4">Log Aktivitas</h3>

            <div class="space-y-4">
            @forelse($activities as $log)
            <div class="flex gap-3 items-start">
                <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></div>
                <div>
                    <p class="text-sm font-semibold text-[#1B254B]">{{ $log->activity }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-2">Belum ada aktivitas.</p>
            @endforelse
            </div>
        </div>

        {{-- Progress ringkas --}}
        @if($my_progress && $my_progress->is_at_risk)
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-sm"></i>
                <p class="font-black text-yellow-700 text-sm">Perhatian Diperlukan</p>
            </div>
            <p class="text-xs text-yellow-600">
                Progresmu membutuhkan perhatian lebih. Yuk tingkatkan intensitas belajar!
            </p>
        </div>
        @endif

    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Animasi progress bar course
    document.querySelectorAll('.progress-bar').forEach(function (el) {
        setTimeout(function () {
            el.style.width = el.dataset.pct + '%';
        }, 100);
    });

    // Animasi bar chart mingguan
    document.querySelectorAll('.weekly-bar').forEach(function (bar, i) {
        setTimeout(function () {
            bar.style.height = bar.dataset.height + '%';
        }, 100 + i * 80);
    });

});
</script>
@endpush