<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KelasController extends Controller
{
    // ───────────────────────────────────────────
    // STAFF: Lihat semua kelas milik sendiri
    // ───────────────────────────────────────────
    public function index()
    {
        $kelas = Kelas::where('staff_id', auth()->id())
            ->withCount('siswa')
            ->with('courses')
            ->latest()
            ->get();

        return view('kelas.staff.index', compact('kelas'));
    }

    // STAFF: Form buat kelas baru
    public function create()
    {
        return view('kelas.staff.create');
    }

    // STAFF: Simpan kelas baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas'     => 'required|string|max:255',
            'mata_pelajaran' => 'required|string|max:255',
            'deskripsi'      => 'nullable|string',
        ]);

        Kelas::create([
            'staff_id'       => auth()->id(),
            'nama_kelas'     => $request->nama_kelas,
            'mata_pelajaran' => $request->mata_pelajaran,
            'deskripsi'      => $request->deskripsi,
            // kode_kelas di-generate otomatis di Model boot()
        ]);

        return redirect()->route('kelas.index')
            ->with('success', 'Kelas berhasil dibuat!');
    }

    // STAFF: Detail kelas (lihat siswa & materi)
    public function show(Kelas $kelas)
    {
        // Pastikan hanya staff pemilik yang bisa lihat
        abort_if($kelas->staff_id !== auth()->id(), 403);

        $kelas->load(['siswa', 'courses']);

        return view('kelas.staff.show', compact('kelas'));
    }

    // STAFF: Hapus kelas
    public function destroy(Kelas $kelas)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);
        $kelas->delete();

        return redirect()->route('kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    // ───────────────────────────────────────────
    // SISWA: Halaman join kelas (form input kode)
    // ───────────────────────────────────────────
    public function joinForm()
    {
        $myKelas = auth()->user()->kelas()->with('staff')->latest()->get();
        return view('kelas.siswa.join', compact('myKelas'));
    }

    // SISWA: Proses join kelas pakai kode
    public function join(Request $request)
    {
        $request->validate([
            'kode_kelas' => 'required|string|size:6',
        ]);

        $kelas = Kelas::where('kode_kelas', strtoupper($request->kode_kelas))->first();

        if (!$kelas) {
            return back()->withErrors(['kode_kelas' => 'Kode kelas tidak ditemukan.']);
        }

        $user = auth()->user();

        // Cek sudah join belum
        if ($user->kelas()->where('kelas_id', $kelas->id)->exists()) {
            return back()->withErrors(['kode_kelas' => 'Kamu sudah terdaftar di kelas ini.']);
        }

        $user->kelas()->attach($kelas->id);

        return redirect()->route('kelas.join')
            ->with('success', 'Berhasil bergabung ke kelas ' . $kelas->nama_kelas . '!');
    }

    // SISWA: Keluar dari kelas
    public function leave(Kelas $kelas)
    {
        auth()->user()->kelas()->detach($kelas->id);

        return redirect()->route('kelas.join')
            ->with('success', 'Kamu telah keluar dari kelas ' . $kelas->nama_kelas . '.');
    }
}