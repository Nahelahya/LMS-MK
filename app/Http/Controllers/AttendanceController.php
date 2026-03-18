<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // Student: tampilkan form presensi
    public function index()
    {
        $user  = Auth::user();
        $today = today();

        // Ambil semua kelas milik siswa ini
        $kelasSiswa = Kelas::whereHas('siswa', fn($q) => $q->where('user_id', $user->id))->get();

        // Presensi hari ini per kelas
        $sudahPresensi = Attendance::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->pluck('kelas_id')
            ->toArray();

        $riwayat = Attendance::with('kelas')
            ->where('user_id', $user->id)
            ->orderByDesc('tanggal')
            ->paginate(10);

        return view('kelas.siswa.presensi', compact('kelasSiswa', 'sudahPresensi', 'riwayat', 'today'));
    }

    // Student: simpan presensi
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'   => 'required|exists:kelas,id',
            'status'     => 'required|in:hadir,sakit,izin,alfa',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $user  = Auth::user();
        $today = today();

        // Pastikan siswa memang terdaftar di kelas ini
        $terdaftar = Kelas::where('id', $request->kelas_id)
            ->whereHas('siswa', fn($q) => $q->where('user_id', $user->id))
            ->exists();

        if (!$terdaftar) {
            return back()->with('error', 'Kamu tidak terdaftar di kelas ini!');
        }

        // Cegah presensi ganda per kelas per hari
        $existing = Attendance::where('user_id', $user->id)
            ->where('kelas_id', $request->kelas_id)
            ->where('tanggal', $today)
            ->exists();

        if ($existing) {
            return back()->with('error', 'Kamu sudah presensi di kelas ini hari ini!');
        }

        Attendance::create([
            'user_id'    => $user->id,
            'kelas_id'   => $request->kelas_id,
            'tanggal'    => $today,
            'status'     => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Presensi berhasil disimpan!');
    }

    // Admin & Staff: lihat presensi siswa dari kelas mereka saja
    public function adminIndex(Request $request)
    {
        $user = Auth::user();

        // Ambil kelas milik staff ini (admin bisa lihat semua)
        $kelasQuery = Kelas::query();
        if ($user->role === 'staff') {
            $kelasQuery->where('staff_id', $user->id);
        }
        $kelasMilik = $kelasQuery->pluck('id');

        $query = Attendance::with(['user', 'kelas'])
            ->whereIn('kelas_id', $kelasMilik)
            ->orderByDesc('tanggal');

        if ($request->tanggal) {
            $query->where('tanggal', $request->tanggal);
        }
        if ($request->kelas_id) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }

        $attendances = $query->paginate(15)->withQueryString();

        // Daftar kelas untuk filter dropdown
        $kelasList = $kelasQuery->get();

        // Rekap hari ini (hanya kelas milik staff)
        $today = today();
        $rekap = [
            'hadir' => Attendance::whereIn('kelas_id', $kelasMilik)->where('tanggal', $today)->where('status', 'hadir')->count(),
            'sakit' => Attendance::whereIn('kelas_id', $kelasMilik)->where('tanggal', $today)->where('status', 'sakit')->count(),
            'izin'  => Attendance::whereIn('kelas_id', $kelasMilik)->where('tanggal', $today)->where('status', 'izin')->count(),
            'alfa'  => Attendance::whereIn('kelas_id', $kelasMilik)->where('tanggal', $today)->where('status', 'alfa')->count(),
        ];

        return view('kelas.staff.presensi', compact('attendances', 'rekap', 'kelasList', 'today'));
    }
}