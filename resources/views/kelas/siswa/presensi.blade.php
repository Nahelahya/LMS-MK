@extends('layouts.dash')
@section('title', 'Presensi')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-2xl text-green-700 text-sm">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Form Presensi per Kelas --}}
    @forelse($kelasSiswa as $kelas)
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl border border-gray-100 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-gray-900">{{ $kelas->nama_kelas }}</h3>
                <p class="text-xs text-gray-500">{{ $kelas->mata_pelajaran }}</p>
            </div>
            @if(in_array($kelas->id, $sudahPresensi))
                @php $p = \App\Models\Attendance::where('user_id', auth()->id())->where('kelas_id', $kelas->id)->where('tanggal', $today)->first(); @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold
                    {{ $p->status === 'hadir' ? 'bg-green-100 text-green-700' :
                      ($p->status === 'sakit' ? 'bg-yellow-100 text-yellow-700' :
                      ($p->status === 'izin'  ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                    <i class="fas fa-check mr-1"></i>{{ strtoupper($p->status) }}
                </span>
            @else
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">Belum Presensi</span>
            @endif
        </div>

        @if(!in_array($kelas->id, $sudahPresensi))
        <form action="{{ route('presensi.store') }}" method="POST">
            @csrf
            <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">

            <div class="grid grid-cols-4 gap-2 mb-4">
                @foreach(['hadir' => ['green','fa-check'], 'sakit' => ['yellow','fa-thermometer-half'], 'izin' => ['blue','fa-file-alt'], 'alfa' => ['red','fa-times']] as $val => $meta)
                <label class="cursor-pointer text-center">
                    <input type="radio" name="status" value="{{ $val }}" class="hidden peer" required>
                    <div class="p-3 rounded-xl border-2 border-gray-100 transition-all
                                peer-checked:border-{{ $meta[0] }}-400 peer-checked:bg-{{ $meta[0] }}-50">
                        <i class="fas {{ $meta[1] }} text-{{ $meta[0] }}-500 block mb-1"></i>
                        <span class="text-xs font-semibold text-gray-700 capitalize">{{ $val }}</span>
                    </div>
                </label>
                @endforeach
            </div>

            <input type="text" name="keterangan" placeholder="Keterangan (opsional)"
                class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-blue-400/50">

            <button type="submit"
                class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm transition">
                <i class="fas fa-paper-plane mr-2"></i>Kirim Presensi
            </button>
        </form>
        @endif
    </div>
    @empty
    <div class="text-center p-12 bg-white dark:bg-[#1a1d28] rounded-2xl border border-gray-100">
        <i class="fas fa-chalkboard text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500">Kamu belum terdaftar di kelas manapun.</p>
    </div>
    @endforelse

    {{-- Riwayat --}}
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-4"><i class="fas fa-history text-blue-500 mr-2"></i>Riwayat Presensi</h3>
        <div class="space-y-2">
            @forelse($riwayat as $item)
            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-[#111318] rounded-xl text-sm">
                <div>
                    <p class="font-medium text-gray-800">{{ $item->tanggal->translatedFormat('d M Y') }}</p>
                    <p class="text-xs text-gray-500">{{ $item->kelas->nama_kelas ?? '-' }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold
                    {{ $item->status === 'hadir' ? 'bg-green-100 text-green-700' :
                      ($item->status === 'sakit' ? 'bg-yellow-100 text-yellow-700' :
                      ($item->status === 'izin'  ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                    {{ strtoupper($item->status) }}
                </span>
            </div>
            @empty
            <p class="text-gray-400 text-center text-sm py-4">Belum ada riwayat presensi.</p>
            @endforelse
        </div>
        <div class="mt-3">{{ $riwayat->links() }}</div>
    </div>
</div>
@endsection