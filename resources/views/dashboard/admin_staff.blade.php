@extends('layouts.dash')

@section('content')
<div class="space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-50 flex items-center space-x-4">
            <div class="p-4 bg-blue-50 text-blue-600 rounded-2xl"><i class="fas fa-users text-2xl"></i></div>
            <div>
                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Total Siswa</p>
                <h3 class="text-2xl font-black text-[#1B254B]">{{ $total_siswa }}</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-50 flex items-center space-x-4">
            <div class="p-4 bg-indigo-50 text-indigo-600 rounded-2xl"><i class="fas fa-chart-bar text-2xl"></i></div>
            <div>
                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Rata-rata Skor</p>
                <h3 class="text-2xl font-black text-[#1B254B]">{{ number_format($avg_score, 1) }}</h3>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-6 rounded-[2rem] shadow-xl shadow-blue-200 text-white flex items-center space-x-4">
            <div class="p-4 bg-white/20 rounded-2xl"><i class="fas fa-percentage text-2xl"></i></div>
            <div>
                <p class="text-blue-100 text-xs font-bold uppercase tracking-wider">Lulus AI</p>
                <h3 class="text-2xl font-black">82%</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-50">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-xl font-black text-[#1B254B]">Analisis Performa Kelas</h4>
                <select class="text-xs border-none bg-gray-50 rounded-lg p-2 outline-none font-bold text-gray-500">
                    <option>7 Hari Terakhir</option>
                </select>
            </div>
            <div class="h-[300px]">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        <div class="bg-[#111C44] p-8 rounded-[2.5rem] shadow-2xl text-white">
            <div class="flex items-center justify-between mb-8">
                <h4 class="text-xl font-bold">Siswa Beresiko</h4>
                <span class="bg-red-500 text-[10px] px-2 py-1 rounded-lg animate-pulse font-black uppercase tracking-tighter">AI Prediction</span>
            </div>
            <div class="space-y-6">
                @forelse($siswa_beresiko as $risk)
                <div class="flex items-center justify-between group">
                    <div class="flex items-center space-x-4">
                        <div class="w-11 h-11 bg-gradient-to-tr from-gray-700 to-gray-600 rounded-2xl flex items-center justify-center font-bold text-lg">
                            {{ substr($risk->user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-sm">{{ $risk->user->name }}</p>
                            <p class="text-[10px] text-gray-400">Score: {{ $risk->last_score }} | Level: Rendah</p>
                        </div>
                    </div>
                    <button class="text-red-400 hover:text-red-300 transition"><i class="fas fa-chevron-right"></i></button>
                </div>
                @empty
                <div class="text-center py-10 opacity-50">
                    <i class="fas fa-check-circle text-4xl mb-3"></i>
                    <p class="text-xs italic">Semua siswa terpantau aman oleh AI.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-50 overflow-x-auto">
        <h4 class="text-xl font-black text-[#1B254B] mb-8">Daftar Murid Terkini</h4>
        <table class="w-full text-left">
            <thead>
                <tr class="text-gray-400 text-[11px] font-black uppercase tracking-widest border-b border-gray-100">
                    <th class="pb-5 pl-2">Nama Murid</th>
                    <th class="pb-5">Progress Belajar</th>
                    <th class="pb-5">Skor Terakhir</th>
                    <th class="pb-5">Status Adaptif</th>
                    <th class="pb-5 text-right pr-2">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm font-bold text-[#1B254B]">
                @foreach($daftar_murid as $murid)
                <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-all">
                    <td class="py-5 pl-2">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xs">
                                {{ substr($murid->name, 0, 1) }}
                            </div>
                            <span>{{ $murid->name }}</span>
                        </div>
                    </td>
                    <td class="py-5 text-xs text-gray-500">
                        <div class="flex items-center space-x-2">
                            <div class="w-24 bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-blue-600 h-full" style="width: {{ $murid->progress->completion_percentage ?? 0 }}%"></div>
                            </div>
                            <span class="text-[10px]">{{ $murid->progress->completion_percentage ?? 0 }}%</span>
                        </div>
                    </td>
                    <td class="py-5">{{ $murid->progress->last_score ?? 0 }}</td>
                    <td class="py-5">
                        @if($murid->progress && $murid->progress->is_at_risk)
                            <span class="bg-red-100 text-red-600 px-3 py-1.5 rounded-xl text-[10px]">Waspada</span>
                        @else
                            <span class="bg-green-100 text-green-600 px-3 py-1.5 rounded-xl text-[10px]">Normal</span>
                        @endif
                    </td>
                    <td class="py-5 text-right pr-2">
                        <button class="text-blue-600 hover:underline text-xs">Detail</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chart_labels) !!},
            datasets: [{
                label: 'Rata-rata Skor',
                data: {!! json_encode($chart_data) !!},
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 4,
                fill: true,
                tension: 0.4,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { display: false }, border: { display: false } },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    });
</script>
@endsection