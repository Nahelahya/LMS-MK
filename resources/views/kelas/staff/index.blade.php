@extends('layouts.dash')
@section('title', 'Kelas Saya')
@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-black text-[#1B254B] dark:text-gray-100">Kelas Saya</h2>
        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Kelola kelas dan materi yang kamu ampu</p>
    </div>
    <a href="{{ route('kelas.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition flex items-center gap-2">
        <i class="fas fa-plus text-xs"></i> Buat Kelas
    </a>
</div>

{{-- Flash --}}
@if(session('success'))
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800
            text-green-700 dark:text-green-400 text-sm font-semibold px-4 py-3 rounded-xl mb-4">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
</div>
@endif

@if($kelas->isEmpty())
{{-- Empty state --}}
<div class="bg-white dark:bg-[#1a1d28] rounded-2xl p-12 text-center shadow-sm border border-transparent dark:border-[#1e2130]">
    <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-chalkboard text-blue-400 dark:text-blue-500 text-2xl"></i>
    </div>
    <p class="text-[#1B254B] dark:text-gray-100 font-bold mb-1">Belum ada kelas</p>
    <p class="text-sm text-gray-400 dark:text-gray-500 mb-4">Buat kelas pertamamu sekarang</p>
    <a href="{{ route('kelas.create') }}" class="text-blue-600 dark:text-blue-400 text-sm font-bold hover:underline">
        + Buat Kelas Baru
    </a>
</div>

@else

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($kelas as $k)

    {{--
        x-data memanggil function kelasKode() dari <script> di bawah.
        Data kode_kelas dikirim via data attribute, aman dari konflik quote Blade.
    --}}
    <div class="relative bg-white dark:bg-[#1a1d28] rounded-2xl p-5 shadow-sm
                hover:shadow-md border border-transparent dark:border-[#1e2130] transition"
         x-data="kelasKode($el)">

        {{-- Card header --}}
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-chalkboard-teacher text-blue-500 dark:text-blue-400"></i>
            </div>

            {{-- Badge kode — klik untuk edit --}}
            <button @click="open = !open"
                    data-kode="{{ $k->kode_kelas }}"
                    class="kode-btn group flex items-center gap-1.5
                           bg-gray-100 dark:bg-[#111318]
                           hover:bg-blue-50 dark:hover:bg-blue-900/30
                           text-gray-500 dark:text-gray-400
                           hover:text-blue-600 dark:hover:text-blue-400
                           text-xs font-black tracking-widest px-3 py-1.5 rounded-lg font-mono
                           border border-transparent dark:border-[#1e2130] transition"
                    title="Klik untuk edit kode kelas">
                <span class="kode-label">{{ $k->kode_kelas }}</span>
                <i class="fas fa-pen text-[9px] opacity-0 group-hover:opacity-100 transition"></i>
            </button>
        </div>

        <h3 class="font-black text-[#1B254B] dark:text-gray-100 text-base mb-0.5">{{ $k->nama_kelas }}</h3>
        <p class="text-xs text-blue-500 dark:text-blue-400 font-semibold mb-3">{{ $k->mata_pelajaran }}</p>

        @if($k->deskripsi)
        <p class="text-xs text-gray-400 dark:text-gray-500 mb-3 line-clamp-2">{{ $k->deskripsi }}</p>
        @endif

        <div class="flex items-center gap-4 text-xs text-gray-400 dark:text-gray-500 mb-4">
            <span><i class="fas fa-users mr-1"></i>{{ $k->siswa_count }} siswa</span>
            <span><i class="fas fa-book mr-1"></i>{{ $k->courses->count() }} materi</span>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('kelas.show', $k) }}"
               class="flex-1 text-center bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50
                      text-blue-600 dark:text-blue-400 text-xs font-bold py-2 rounded-xl transition">
                Kelola
            </a>
            <form action="{{ route('kelas.destroy', $k) }}" method="POST"
                  onsubmit="return confirm('Hapus kelas {{ $k->nama_kelas }}?')">
                @csrf @method('DELETE')
                <button class="bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50
                               text-red-500 dark:text-red-400 text-xs font-bold px-3 py-2 rounded-xl transition">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>

        {{-- ══════════════════════════════
             POPOVER EDIT KODE KELAS
        ══════════════════════════════ --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             @click.outside="open = false"
             class="absolute right-5 top-14 z-50 w-72
                    bg-white dark:bg-[#1a1d28]
                    border border-gray-100 dark:border-[#1e2130]
                    rounded-2xl shadow-xl dark:shadow-black/40 p-5"
             style="display:none">

            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-black text-[#1B254B] dark:text-gray-100">Edit Kode Kelas</p>
                <button @click="open = false" type="button"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition text-xs">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Input row --}}
            <div class="flex gap-2 mb-2">
                <input x-model="kode"
                       type="text"
                       maxlength="10"
                       @input="kode = $event.target.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); error = ''"
                       class="flex-1 px-3 py-2.5 rounded-xl font-mono font-black tracking-widest text-sm uppercase
                              border border-gray-200 dark:border-[#1e2130]
                              bg-gray-50 dark:bg-[#111318]
                              text-[#1B254B] dark:text-gray-100
                              focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Contoh: ABC123">

                {{-- Salin --}}
                <button @click="copy()" type="button"
                        :title="copied ? 'Tersalin!' : 'Salin kode'"
                        class="px-3 py-2 rounded-xl text-xs
                               bg-gray-100 dark:bg-[#111318] hover:bg-gray-200 dark:hover:bg-[#1e2130]
                               text-gray-500 dark:text-gray-400 transition">
                    <i :class="copied ? 'fas fa-check text-green-500' : 'far fa-copy'"></i>
                </button>

                {{-- Generate acak --}}
                <button @click="generate()" type="button"
                        title="Generate kode acak"
                        class="px-3 py-2 rounded-xl text-xs
                               bg-gray-100 dark:bg-[#111318] hover:bg-gray-200 dark:hover:bg-[#1e2130]
                               text-gray-500 dark:text-gray-400 transition">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>

            {{-- Pesan error --}}
            <p x-show="error" x-text="error"
               class="text-red-500 dark:text-red-400 text-xs mb-2" style="display:none"></p>

            <p class="text-[10px] text-gray-400 dark:text-gray-600 mb-4">
                Maks 10 karakter, huruf besar &amp; angka. Siswa pakai kode ini untuk bergabung.
            </p>

            {{-- Tombol aksi --}}
            <div class="flex gap-2">
                <button @click="open = false" type="button"
                        class="flex-1 py-2 rounded-xl text-xs font-bold
                               bg-gray-100 dark:bg-[#111318] hover:bg-gray-200 dark:hover:bg-[#1e2130]
                               text-gray-500 dark:text-gray-400 transition">
                    Batal
                </button>

                <form action="{{ route('kelas.updateKode', $k->id) }}" method="POST" class="flex-1"
                      @submit.prevent="submit($el)">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="kode_kelas" :value="kode">
                    <button type="submit" :disabled="loading"
                            class="w-full py-2 rounded-xl text-xs font-bold
                                   bg-blue-600 hover:bg-blue-700 text-white transition
                                   disabled:opacity-60 flex items-center justify-center gap-1.5">
                        <i x-show="loading" class="fas fa-spinner fa-spin text-[10px]" style="display:none"></i>
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan'">Simpan</span>
                    </button>
                </form>
            </div>
        </div>
        {{-- END POPOVER --}}

    </div>
    @endforeach
</div>
@endif

{{-- ══════════════════════════════════════════════════════
     Alpine function — dipisah dari Blade agar tidak ada
     konflik quote. Dibaca dari data-kode attribute.
══════════════════════════════════════════════════════ --}}
<script>
function kelasKode(el) {
    // Baca kode awal dari data-kode attribute di tombol badge
    const btn    = el.querySelector('.kode-btn');
    const initial = btn ? btn.dataset.kode : '';

    return {
        open:    false,
        kode:    initial,
        loading: false,
        copied:  false,
        error:   '',

        generate() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            this.kode = Array.from({ length: 6 }, () =>
                chars[Math.floor(Math.random() * chars.length)]
            ).join('');
            this.error = '';
        },

        copy() {
            navigator.clipboard.writeText(this.kode).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 1500);
            });
        },

        submit(form) {
            if (!this.kode.trim()) {
                this.error = 'Kode tidak boleh kosong.';
                return;
            }
            if (!/^[A-Z0-9]+$/.test(this.kode)) {
                this.error = 'Hanya huruf besar & angka.';
                return;
            }
            this.loading = true;
            form.submit();
        }
    };
}
</script>

@endsection