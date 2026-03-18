@extends('layouts.dash')

@section('content')
<div class="w-full max-w-5xl p-8 m-4 bg-white/20 backdrop-blur-lg rounded-2xl border border-white/30 shadow-2xl text-white">

    <div class="mb-6">
        <h2 class="text-2xl font-bold flex items-center gap-2">
            <i class="fas fa-users text-blue-400"></i> Data Presensi Siswa
        </h2>
        <p class="text-gray-300 text-sm mt-1">{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Rekap Hari Ini --}}
    <div class="grid grid-cols-4 gap-3 mb-6">
        @foreach(['hadir' => ['green','fa-check-circle'], 'sakit' => ['yellow','fa-thermometer-half'], 'izin' => ['blue','fa-file-alt'], 'alfa' => ['red','fa-times-circle']] as $key => $meta)
        <div class="p-4 bg-{{ $meta[0] }}-500/20 border border-{{ $meta[0] }}-400/30 rounded-xl text-center">
            <i class="fas {{ $meta[1] }} text-2xl text-{{ $meta[0] }}-400"></i>
            <p class="text-2xl font-bold mt-1">{{ $rekap[$key] }}</p>
            <p class="text-xs text-gray-300 capitalize">{{ $key }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
            class="px-4 py-2 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-400/50 text-sm">

        <select name="status" class="px-4 py-2 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-400/50 text-sm">
            <option value="">Semua Status</option>
            @foreach(['hadir','sakit','izin','alfa'] as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>

        <select name="kelas_id" class="px-4 py-2 bg-white dark:bg-[#1a1d28] border border-gray-200 rounded-xl text-sm focus:outline-none">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $k)
            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                {{ $k->nama_kelas }}
            </option>
            @endforeach
        </select>

        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..."
            class="px-4 py-2 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400/50 text-sm flex-1">

        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 rounded-xl font-semibold text-sm transition">
            <i class="fas fa-search mr-1"></i> Filter
        </button>
        <a href="{{ route('admin.presensi') }}" class="px-5 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-sm transition">
            Reset
        </a>
    </form>

    {{-- Tabel --}}
    <div class="overflow-x-auto rounded-xl border border-white/20">
        <table class="w-full text-sm">
            <thead class="bg-white/10">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-200">#</th>
                    <th class="px-4 py-3 text-left text-gray-200">Nama Siswa</th>
                    <th class="px-4 py-3 text-left text-gray-500 text-xs font-bold uppercase">Kelas</th>
                    <th class="px-4 py-3 text-left text-gray-200">Tanggal</th>
                    <th class="px-4 py-3 text-left text-gray-200">Status</th>
                    <th class="px-4 py-3 text-left text-gray-200">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @forelse($attendances as $i => $item)
                <tr class="hover:bg-white/5 transition">
                    <td class="px-4 py-3 text-gray-300">{{ $attendances->firstItem() + $i }}</td>
                    <td class="px-4 py-3 font-medium">{{ $item->user->name }}</td>
                    <td class="px-4 py-3 text-gray-600 text-sm">{{ $item->kelas->nama_kelas ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-300">{{ $item->tanggal->translatedFormat('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            @if($item->status === 'hadir') bg-green-500/30 text-green-300
                            @elseif($item->status === 'sakit') bg-yellow-500/30 text-yellow-300
                            @elseif($item->status === 'izin') bg-blue-500/30 text-blue-300
                            @else bg-red-500/30 text-red-300 @endif">
                            {{ strtoupper($item->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-300">{{ $item->keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">Tidak ada data presensi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $attendances->links() }}</div>
</div>
@endsection