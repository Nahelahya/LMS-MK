@extends('layouts.dash')
@section('title', __('messages.presensi_judul'))

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

            {{-- Badge status: jika sudah presensi tampilkan status, jika belum tampilkan "Not Yet Submitted" --}}
            @if(in_array($kelas->id, $sudahPresensi))
                @php
                    $p = \App\Models\Attendance::where('user_id', auth()->id())
                            ->where('kelas_id', $kelas->id)
                            ->where('tanggal', $today)
                            ->first();

                    // Map nilai status DB → translation key badge
                    $badgeKeyMap = [
                        'hadir' => 'badge_hadir',
                        'sakit' => 'badge_sakit',
                        'izin'  => 'badge_izin',
                        'alfa'  => 'badge_alfa',
                    ];
                    $badgeKey = $badgeKeyMap[$p->status] ?? 'badge_hadir';
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold
                    {{ $p->status === 'hadir' ? 'bg-green-100 text-green-700' :
                      ($p->status === 'sakit' ? 'bg-yellow-100 text-yellow-700' :
                      ($p->status === 'izin'  ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                    <i class="fas fa-check mr-1"></i>{{ __('messages.' . $badgeKey) }}
                </span>
            @else
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">
                    {{ __('messages.belum_presensi') }}
                </span>
            @endif
        </div>

        @if(!in_array($kelas->id, $sudahPresensi))
        <form action="{{ route('presensi.store') }}" method="POST">
            @csrf
            <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">

            {{--
                Setiap opsi status menyimpan:
                  - value  : nilai yang dikirim ke DB (tidak berubah, selalu bahasa Indonesia/key)
                  - color  : warna Tailwind
                  - icon   : class ikon FontAwesome
                  - label  : translation key untuk teks yang ditampilkan ke user
            --}}
            @php
                $statusOptions = [
                    'hadir' => ['color' => 'green',  'icon' => 'fa-check',            'label' => 'hadir'],
                    'sakit' => ['color' => 'yellow', 'icon' => 'fa-thermometer-half', 'label' => 'sakit'],
                    'izin'  => ['color' => 'blue',   'icon' => 'fa-file-alt',         'label' => 'izin'],
                    'alfa'  => ['color' => 'red',    'icon' => 'fa-times',            'label' => 'alfa'],
                ];
            @endphp

            <div class="grid grid-cols-4 gap-2 mb-4">
                @foreach($statusOptions as $val => $meta)
                <label class="cursor-pointer text-center">
                    <input type="radio" name="status" value="{{ $val }}" class="hidden peer" required>
                    <div class="p-3 rounded-xl border-2 border-gray-100 transition-all
                                peer-checked:border-{{ $meta['color'] }}-400 peer-checked:bg-{{ $meta['color'] }}-50">
                        <i class="fas {{ $meta['icon'] }} text-{{ $meta['color'] }}-500 block mb-1"></i>
                        {{-- Label diterjemahkan, value tetap key DB --}}
                        <span class="text-xs font-semibold text-gray-700">
                            {{ __('messages.' . $meta['label']) }}
                        </span>
                    </div>
                </label>
                @endforeach
            </div>

            <input type="text" name="keterangan"
                placeholder="{{ __('messages.keterangan_opsional') }}"
                class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-blue-400/50">

            <button type="submit"
                class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm transition">
                <i class="fas fa-paper-plane mr-2"></i>{{ __('messages.kirim_presensi') }}
            </button>
        </form>
        @endif
    </div>

    @empty
    {{-- Empty state: student belum terdaftar di kelas manapun --}}
    <div class="text-center p-12 bg-white dark:bg-[#1a1d28] rounded-2xl border border-gray-100">
        <i class="fas fa-chalkboard text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500">{{ __('messages.belum_terdaftar_kelas') }}</p>
    </div>
    @endforelse

    {{-- Riwayat Presensi --}}
    <div class="bg-white dark:bg-[#1a1d28] rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-4">
            <i class="fas fa-history text-blue-500 mr-2"></i>{{ __('messages.riwayat_presensi') }}
        </h3>
        <div class="space-y-2">
            @forelse($riwayat as $item)
            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-[#111318] rounded-xl text-sm">
                <div>
                    <p class="font-medium text-gray-800">{{ $item->tanggal->translatedFormat('d M Y') }}</p>
                    <p class="text-xs text-gray-500">{{ $item->kelas->nama_kelas ?? '-' }}</p>
                </div>

                {{-- Badge riwayat — sama dengan badge di atas, gunakan mapping yang sama --}}
                @php
                    $riwayatBadgeMap = [
                        'hadir' => ['key' => 'badge_hadir', 'class' => 'bg-green-100 text-green-700'],
                        'sakit' => ['key' => 'badge_sakit', 'class' => 'bg-yellow-100 text-yellow-700'],
                        'izin'  => ['key' => 'badge_izin',  'class' => 'bg-blue-100 text-blue-700'],
                        'alfa'  => ['key' => 'badge_alfa',  'class' => 'bg-red-100 text-red-700'],
                    ];
                    $riwayatBadge = $riwayatBadgeMap[$item->status]
                                    ?? ['key' => 'badge_hadir', 'class' => 'bg-gray-100 text-gray-700'];
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $riwayatBadge['class'] }}">
                    {{ __('messages.' . $riwayatBadge['key']) }}
                </span>
            </div>
            @empty
            <p class="text-gray-400 text-center text-sm py-4">{{ __('messages.tidak_ada_riwayat') }}</p>
            @endforelse
        </div>
        <div class="mt-3">{{ $riwayat->links() }}</div>
    </div>
</div>
@endsection