@extends('layouts.dash')

@section('title', __('messages.siswa'))

@section('content')
<div class="p-6 space-y-6">

    {{-- ===== HEADER ===== --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-user-graduate text-blue-400"></i>
                {{ __('messages.siswa') }}
            </h1>
            <p class="text-gray-400 text-sm mt-1">
                {{ __('messages.siswa_desc') }}
            </p>
        </div>
        {{-- Badge total siswa --}}
        <div class="bg-blue-600/20 border border-blue-500/30 rounded-2xl px-5 py-3 text-center">
            <p class="text-2xl font-bold text-blue-400">{{ $students->total() }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('messages.total_siswa') }}</p>
        </div>
    </div>

    {{-- ===== NOTIFIKASI SUKSES / ERROR ===== --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 rounded-2xl px-5 py-4 text-sm">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-2xl px-5 py-4 text-sm">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- ===== FILTER & PENCARIAN ===== --}}
    <form method="GET" action="{{ route('students.index') }}"
          class="flex flex-col sm:flex-row gap-3">

        {{-- Input pencarian --}}
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="{{ __('messages.cari_siswa_placeholder') }}"
                   class="w-full bg-gray-800/60 border border-gray-700 text-white text-sm
                          rounded-xl pl-10 pr-4 py-3 placeholder-gray-500
                          focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                          transition">
        </div>

        {{-- Filter status --}}
        <select name="status"
                class="bg-gray-800/60 border border-gray-700 text-gray-300 text-sm
                       rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition">
            <option value="">{{ __('messages.semua_status') }}</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>
                {{ __('messages.status_aktif') }}
            </option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                {{ __('messages.status_tidak_aktif') }}
            </option>
        </select>

        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                       rounded-xl px-6 py-3 transition flex items-center gap-2">
            <i class="fas fa-filter"></i>
            {{ __('messages.filter') }}
        </button>

        {{-- Tombol reset filter --}}
        @if(request('search') || request('status'))
            <a href="{{ route('students.index') }}"
               class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm font-semibold
                      rounded-xl px-5 py-3 transition flex items-center gap-2">
                <i class="fas fa-times"></i>
                {{ __('messages.reset') }}
            </a>
        @endif
    </form>

    {{-- ===== TABEL DATA SISWA ===== --}}
    <div class="bg-gray-800/40 border border-gray-700/50 rounded-2xl overflow-hidden">

        {{-- Jika data kosong --}}
        @if($students->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-gray-500">
                <i class="fas fa-users text-5xl mb-4 opacity-30"></i>
                <p class="text-base font-medium">{{ __('messages.tidak_ada_siswa') }}</p>
                @if(request('search'))
                    <p class="text-sm mt-1">{{ __('messages.coba_kata_lain') }}</p>
                @endif
            </div>

        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-700/60 text-gray-400 text-xs uppercase tracking-wider">
                            <th class="text-left px-6 py-4 font-semibold">#</th>
                            <th class="text-left px-6 py-4 font-semibold">{{ __('messages.kolom_siswa') }}</th>
                            <th class="text-left px-6 py-4 font-semibold">{{ __('messages.kolom_email') }}</th>
                            <th class="text-left px-6 py-4 font-semibold">{{ __('messages.kolom_status') }}</th>
                            <th class="text-left px-6 py-4 font-semibold">{{ __('messages.kolom_bergabung') }}</th>
                            <th class="text-center px-6 py-4 font-semibold">{{ __('messages.aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/40">
                        @foreach($students as $index => $student)
                        <tr class="hover:bg-gray-700/20 transition group">

                            {{-- Nomor urut berdasarkan halaman pagination --}}
                            <td class="px-6 py-4 text-gray-500">
                                {{ $students->firstItem() + $index }}
                            </td>

                            {{-- Avatar + Nama --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    {{-- Tampilkan foto jika ada, atau inisial nama --}}
                                    @if($student->photo)
                                        <img src="{{ Storage::url($student->photo) }}"
                                             alt="{{ $student->name }}"
                                             class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-700">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-blue-600/30 ring-2 ring-gray-700
                                                    flex items-center justify-center text-blue-300 font-bold text-sm">
                                            {{ strtoupper(substr($student->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <span class="text-white font-medium">{{ $student->name }}</span>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-6 py-4 text-gray-400">
                                {{ $student->email }}
                            </td>

                            {{-- Badge status --}}
                            <td class="px-6 py-4">
                                @php
                                    $status = $student->status ?? 'active';
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $status === 'active'
                                        ? 'bg-green-500/15 text-green-400 border border-green-500/30'
                                        : 'bg-gray-600/30 text-gray-400 border border-gray-600/40' }}">
                                    <span class="w-1.5 h-1.5 rounded-full
                                        {{ $status === 'active' ? 'bg-green-400' : 'bg-gray-500' }}"></span>
                                    {{ $status === 'active'
                                        ? __('messages.status_aktif')
                                        : __('messages.status_tidak_aktif') }}
                                </span>
                            </td>

                            {{-- Tanggal bergabung --}}
                            <td class="px-6 py-4 text-gray-400">
                                {{ $student->created_at->format('d M Y') }}
                            </td>

                            {{-- Tombol aksi --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- Lihat detail --}}
                                    <a href="{{ route('students.show', $student) }}"
                                       class="p-2 rounded-xl bg-blue-600/20 hover:bg-blue-600/40
                                              text-blue-400 hover:text-blue-300 transition"
                                       title="{{ __('messages.lihat_detail') }}">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('students.destroy', $student) }}"
                                          method="POST"
                                          onsubmit="return confirm('{{ __('messages.konfirmasi_hapus_siswa', ['name' => addslashes($student->name)]) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 rounded-xl bg-red-600/20 hover:bg-red-600/40
                                                       text-red-400 hover:text-red-300 transition"
                                                title="{{ __('messages.hapus_siswa') }}">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ===== PAGINATION ===== --}}
            @if($students->hasPages())
                <div class="px-6 py-4 border-t border-gray-700/60 flex items-center justify-between text-sm text-gray-400">
                    <p>
                        {{ __('messages.pagination_info', [
                            'from'  => $students->firstItem(),
                            'to'    => $students->lastItem(),
                            'total' => $students->total(),
                        ]) }}
                    </p>
                    {{ $students->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection