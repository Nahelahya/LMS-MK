<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
    // STAFF: Update kode unik kelas
    // PATCH /kelas/{kelas}/kode
    // ───────────────────────────────────────────
    public function updateKode(Request $request, Kelas $kelas)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);

        $request->validate([
            'kode_kelas' => [
                'required',
                'string',
                'max:10',
                'regex:/^[A-Z0-9]+$/',
                Rule::unique('kelas', 'kode_kelas')->ignore($kelas->id),
            ],
        ], [
            'kode_kelas.required' => 'Kode kelas tidak boleh kosong.',
            'kode_kelas.max'      => 'Kode maksimal 10 karakter.',
            'kode_kelas.regex'    => 'Kode hanya boleh huruf besar dan angka.',
            'kode_kelas.unique'   => 'Kode ini sudah dipakai kelas lain. Pilih kode lain.',
        ]);

        $kelas->update(['kode_kelas' => $request->kode_kelas]);

        return back()->with('success', "Kode kelas berhasil diubah menjadi {$request->kode_kelas}.");
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
            // ✅ max:10 agar sesuai dengan kode yang sudah bisa diedit (bukan size:6)
            'kode_kelas' => 'required|string|max:10',
        ]);

        $kelas = Kelas::where('kode_kelas', strtoupper($request->kode_kelas))->first();

        if (!$kelas) {
            return back()->withErrors(['kode_kelas' => 'Kode kelas tidak ditemukan.']);
        }

        $user = auth()->user();

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