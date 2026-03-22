<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Jawaban;
use App\Models\StudentProgress;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    // ══════════════════════════════════════════════
    // STAFF — KELAS CRUD
    // ══════════════════════════════════════════════

    public function index()
    {
        $kelas = Kelas::where('staff_id', auth()->id())
            ->withCount('siswa')
            ->with(['courses', 'materis'])
            ->latest()->get();

        return view('kelas.staff.index', compact('kelas'));
    }

    public function create()
    {
        return view('kelas.staff.create');
    }

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
        ]);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dibuat!');
    }

    public function show(Kelas $kelas)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);

        $kelas->load([
            'siswa',
            'materis'  => fn($q) => $q->latest(),
            'courses',
        ]);

        return view('kelas.staff.show', compact('kelas'));
    }

    public function destroy(Kelas $kelas)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);
        $kelas->delete();

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function updateKode(Request $request, Kelas $kelas)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);

        $request->validate([
            'kode_kelas' => [
                'required', 'string', 'max:10', 'regex:/^[A-Z0-9]+$/',
                Rule::unique('kelas', 'kode_kelas')->ignore($kelas->id),
            ],
        ], [
            'kode_kelas.required' => 'Kode tidak boleh kosong.',
            'kode_kelas.max'      => 'Kode maksimal 10 karakter.',
            'kode_kelas.regex'    => 'Hanya huruf besar dan angka.',
            'kode_kelas.unique'   => 'Kode sudah dipakai kelas lain.',
        ]);

        $kelas->update(['kode_kelas' => $request->kode_kelas]);

        return back()->with('success', "Kode diubah menjadi {$request->kode_kelas}.");
    }

    // ══════════════════════════════════════════════
    // STAFF — MATERI
    // materi.id = UUID, kelas_id = bigint
    // ══════════════════════════════════════════════

    public function materiStore(Request $request, Kelas $kelas)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);

        $request->validate([
            'judul'          => 'required|string|max:255',
            'deskripsi'      => 'nullable|string',
            'deadline'       => 'nullable|date|after_or_equal:today',
            'kunci_jawaban'  => 'nullable|string',
            'file'           => 'required|file|mimes:pdf,doc,docx,xls,xlsx,mp4,jpg,jpeg,png|max:51200',
        ], [
            'deadline.after_or_equal' => 'Tenggat tidak boleh sebelum hari ini.',
        ]);

        $file = $request->file('file');
        $path = $file->storeAs(
            "materi/kelas_{$kelas->id}",
            time() . '_' . $file->getClientOriginalName(),
            'local'
        );

        // UUID di-generate otomatis di Model boot()
        Materi::create([
            'kelas_id'       => $kelas->id,
            'judul'          => $request->judul,
            'deskripsi'      => $request->deskripsi,
            'deadline'       => $request->deadline ?: null,
            'kunci_jawaban'  => $request->kunci_jawaban ?: null,
            'file_path'      => $path,
            'original_name'  => $file->getClientOriginalName(),
            'tipe_file'      => strtolower($file->getClientOriginalExtension()),
            'uploaded_by'    => auth()->id(),
        ]);

        return back()->with('success', 'Materi berhasil diupload.');
    }

    public function materiDownload(Kelas $kelas, Materi $materi)
    {
        if (auth()->user()->role === 'student') {
            abort_unless(
                auth()->user()->kelas()->where('kelas_id', $kelas->id)->exists(), 403
            );
        } else {
            abort_if($kelas->staff_id !== auth()->id(), 403);
        }

        $path = storage_path('app/' . $materi->file_path);
        abort_unless(file_exists($path), 404);

        return response()->download($path, $materi->original_name);
    }

    public function materiDestroy(Kelas $kelas, Materi $materi)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);

        Storage::disk('local')->delete($materi->file_path);
        $materi->delete();

        return back()->with('success', 'Materi berhasil dihapus.');
    }

    // STAFF: Update tenggat waktu saja (tanpa ganti file)
    public function materiUpdateDeadline(Request $request, Kelas $kelas, Materi $materi)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);

        $request->validate([
            'deadline' => 'nullable|date|after_or_equal:today',
        ], [
            'deadline.after_or_equal' => 'Tenggat tidak boleh sebelum hari ini.',
        ]);

        $materi->update([
            'deadline' => $request->deadline ?: null,
        ]);

        return back()->with('success', 'Tenggat waktu berhasil diperbarui.');
    }

    // ══════════════════════════════════════════════
    // STAFF — PENILAIAN
    // ══════════════════════════════════════════════

    public function nilaiStore(Request $request, Kelas $kelas, $siswaId)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'nilai'     => 'required|numeric|min:0|max:100',
        ]);

        $nilai    = (float) $request->nilai;
        $progress = StudentProgress::firstOrCreate(
            ['user_id' => $siswaId, 'course_id' => $request->course_id],
            ['completion_percentage' => 0, 'is_at_risk' => false]
        );

        // ENUM: Remedial | Normal | Advance
        $progress->update([
            'last_score'     => $nilai,
            'is_at_risk'     => $nilai < 60,
            'status_adaptif' => match(true) {
                $nilai >= 75 => 'Advance',
                $nilai >= 60 => 'Normal',
                default      => 'Remedial',
            },
        ]);

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    // ══════════════════════════════════════════════
    // STAFF — DOWNLOAD JAWABAN
    // jawabans.materi_id = uuid, student_id = bigint
    // ══════════════════════════════════════════════

    public function jawabanDownload(Kelas $kelas, Jawaban $jawaban)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);

        // Pastikan jawaban ini untuk materi di kelas ini
        $materi = Materi::where('id', $jawaban->materi_id)
                        ->where('kelas_id', $kelas->id)
                        ->firstOrFail();

        $path = storage_path('app/' . $jawaban->file_path);
        abort_unless(file_exists($path), 404);

        $ext      = pathinfo($jawaban->file_path, PATHINFO_EXTENSION);
        $filename = ($jawaban->student->name ?? 'siswa') . '_' . $materi->judul . '.' . $ext;

        return response()->download($path, $filename);
    }

    // ══════════════════════════════════════════════
    // SISWA — JOIN / SHOW / LEAVE
    // ══════════════════════════════════════════════

    public function joinForm()
    {
        $myKelas = auth()->user()->kelas()->with('staff')->latest()->get();
        return view('kelas.siswa.join', compact('myKelas'));
    }

    public function join(Request $request)
    {
        $request->validate(['kode_kelas' => 'required|string|max:10']);

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
            ->with('success', 'Berhasil bergabung ke ' . $kelas->nama_kelas . '!');
    }

    public function showSiswa(Kelas $kelas)
    {
        abort_unless(
            auth()->user()->kelas()->where('kelas_id', $kelas->id)->exists(), 403
        );

        $kelas->load(['materis' => fn($q) => $q->latest(), 'staff', 'courses']);

        $courseIds   = $kelas->courses->pluck('id');
        $myProgress  = StudentProgress::where('user_id', auth()->id())
                           ->whereIn('course_id', $courseIds)
                           ->with('course')
                           ->get();

        // materi_id adalah uuid — cast ke string untuk perbandingan
        $materiIds   = $kelas->materis->pluck('id')->map(fn($id) => (string) $id);
        $jawabanSaya = Jawaban::where('student_id', auth()->id())
                           ->whereIn('materi_id', $materiIds)
                           ->with('materi')
                           ->latest()->get();

        return view('kelas.siswa.show', compact('kelas', 'myProgress', 'jawabanSaya'));
    }

    public function leave(Kelas $kelas)
    {
        auth()->user()->kelas()->detach($kelas->id);

        return redirect()->route('kelas.join')
            ->with('success', 'Kamu telah keluar dari ' . $kelas->nama_kelas . '.');
    }

    // ══════════════════════════════════════════════
    // STAFF — COURSE CRUD
    // ══════════════════════════════════════════════

    public function courseStore(Request $request, Kelas $kelas)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);

        $request->validate([
            'nama_course' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
        ]);

        // Generate kode_course unik
        $kode = strtoupper(
            substr(preg_replace('/[^A-Z0-9]/', '', strtoupper($request->nama_course)), 0, 4)
        ) . rand(100, 999) . '-' . $kelas->id;

        Course::create([
            'kelas_id'    => $kelas->id,
            'nama_course' => $request->nama_course,
            'kode_course' => $kode,
            'deskripsi'   => $request->deskripsi ?: null,
        ]);

        return back()->with('success', 'Course berhasil ditambahkan.');
    }

    public function courseDestroy(Kelas $kelas, Course $course)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);
        $course->delete();

        return back()->with('success', 'Course berhasil dihapus.');
    }

    // ══════════════════════════════════════════════
    // STAFF — PREVIEW JAWABAN (inline di modal)
    // ══════════════════════════════════════════════

    public function jawabanPreview(Kelas $kelas, Jawaban $jawaban)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);

        $path = storage_path('app/' . $jawaban->file_path);
        abort_unless(file_exists($path), 404);

        $ext      = strtolower(pathinfo($jawaban->file_path, PATHINFO_EXTENSION));
        $mimeMap  = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
        ];
        $mime = $mimeMap[$ext] ?? 'application/octet-stream';

        return response()->file($path, ['Content-Type' => $mime]);
    }


    public function showDetail(\App\Models\User $siswa)
    {
        // Hanya staff/admin yang boleh akses
        abort_unless(in_array(auth()->user()->role, ['staff', 'admin']), 403);
 
        // Nilai per mata pelajaran
        $nilais = \Illuminate\Support\Facades\DB::table('nilais')
            ->where('user_id', $siswa->id)
            ->select('mata_pelajaran', 'judul', 'tipe', 'nilai', 'keterangan', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
 
        // Rata-rata per mata pelajaran
        $avgPerMapel = \Illuminate\Support\Facades\DB::table('nilais')
            ->where('user_id', $siswa->id)
            ->select(
                'mata_pelajaran',
                \Illuminate\Support\Facades\DB::raw("ROUND(AVG(nilai)::numeric, 1) AS avg_nilai"),
                \Illuminate\Support\Facades\DB::raw("COUNT(*) AS jumlah"),
                \Illuminate\Support\Facades\DB::raw("MIN(nilai) AS terendah"),
                \Illuminate\Support\Facades\DB::raw("MAX(nilai) AS tertinggi")
            )
            ->groupBy('mata_pelajaran')
            ->orderBy('avg_nilai')
            ->get();
 
        // Kehadiran
        $attendance = \Illuminate\Support\Facades\DB::table('attendances')
            ->where('user_id', $siswa->id)
            ->select(
                \Illuminate\Support\Facades\DB::raw("COUNT(*) AS total"),
                \Illuminate\Support\Facades\DB::raw("COUNT(CASE WHEN status = 'hadir' THEN 1 END) AS hadir"),
                \Illuminate\Support\Facades\DB::raw("COUNT(CASE WHEN status = 'sakit' THEN 1 END) AS sakit"),
                \Illuminate\Support\Facades\DB::raw("COUNT(CASE WHEN status = 'izin'  THEN 1 END) AS izin"),
                \Illuminate\Support\Facades\DB::raw("COUNT(CASE WHEN status = 'alfa'  THEN 1 END) AS alfa")
            )
            ->first();
 
        $pctHadir = ($attendance->total ?? 0) > 0
            ? round($attendance->hadir / $attendance->total * 100, 1)
            : 0;
 
        // Kelas yang diikuti
        $kelasDiikuti = \Illuminate\Support\Facades\DB::table('kelas_siswa as ks')
            ->join('kelas as k', 'ks.kelas_id', '=', 'k.id')
            ->where('ks.user_id', $siswa->id)
            ->select('k.nama_kelas', 'k.mata_pelajaran')
            ->get();
 
        // Student progress (adaptif)
        $progress = \Illuminate\Support\Facades\DB::table('student_progress')
            ->where('user_id', $siswa->id)
            ->select('status_adaptif', 'last_score', 'completion_percentage', 'is_at_risk')
            ->get();
 
        // Riwayat kehadiran terbaru
        $riwayatHadir = \Illuminate\Support\Facades\DB::table('attendances')
            ->where('user_id', $siswa->id)
            ->orderBy('tanggal', 'desc')
            ->limit(10)
            ->get();
 
        return view('kelas.siswa.detail', compact(
            'siswa',
            'nilais',
            'avgPerMapel',
            'attendance',
            'pctHadir',
            'kelasDiikuti',
            'progress',
            'riwayatHadir'
        ));
    }

}