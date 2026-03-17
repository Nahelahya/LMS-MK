<?php

namespace App\Http\Controllers;

use App\Models\Jawaban;
use App\Models\Materi;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JawabanController extends Controller
{
    // Siswa upload jawaban per materi dalam konteks kelas
    public function store(Request $request, Kelas $kelas, Materi $materi)
    {
        abort_unless(auth()->user()->role === 'student', 403);

        // Pastikan materi ini milik kelas yang diikuti siswa
        // materi.kelas_id = bigint, materi.id = uuid
        abort_unless((string) $materi->kelas_id === (string) $kelas->id, 404);
        abort_unless(
            auth()->user()->kelas()->where('kelas_id', $kelas->id)->exists(), 403
        );

        // Cek apakah tenggat sudah lewat
        if ($materi->is_overdue) {
            return back()->withErrors(['file' => 'Tenggat waktu untuk materi ini sudah lewat.']);
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
        ]);

        $file = $request->file('file');
        $path = $file->storeAs(
            "tugas/kelas_{$kelas->id}/materi_{$materi->id}",
            time() . '_' . auth()->id() . '.' . $file->getClientOriginalExtension(),
            'local'
        );

        // Hapus & ganti jawaban lama jika sudah pernah upload
        // materi_id disimpan sebagai uuid string
        $existing = Jawaban::where('student_id', auth()->id())
                           ->where('materi_id', (string) $materi->id)
                           ->first();

        if ($existing) {
            Storage::disk('local')->delete($existing->file_path);
            $existing->update(['file_path' => $path]);
        } else {
            Jawaban::create([
                'materi_id'  => (string) $materi->id,
                'student_id' => auth()->id(),
                'file_path'  => $path,
            ]);
        }

        return back()->with('success', 'Jawaban berhasil dikumpulkan.');
    }

    // Upload jawaban dari halaman /materi global (tanpa konteks kelas di URL)
    public function storeFromMateri(Request $request, Materi $materi)
    {
        abort_unless(auth()->user()->role === "student", 403);

        if ($materi->is_overdue) {
            return back()->withErrors(["file" => "Tenggat waktu sudah lewat."]);
        }

        // Pastikan siswa terdaftar di kelas yang memiliki materi ini
        if ($materi->kelas_id) {
            abort_unless(
                auth()->user()->kelas()->where("kelas_id", $materi->kelas_id)->exists(), 403
            );
        }

        $request->validate([
            "file" => "required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480",
        ]);

        $file = $request->file("file");
        $path = $file->storeAs(
            "tugas/materi_" . $materi->id,
            time() . "_" . auth()->id() . "." . $file->getClientOriginalExtension(),
            "local"
        );

        $existing = Jawaban::where("student_id", auth()->id())
                           ->where("materi_id", (string) $materi->id)
                           ->first();

        if ($existing) {
            Storage::disk("local")->delete($existing->file_path);
            $existing->update(["file_path" => $path]);
        } else {
            Jawaban::create([
                "materi_id"  => (string) $materi->id,
                "student_id" => auth()->id(),
                "file_path"  => $path,
            ]);
        }

        return back()->with("success", "Jawaban berhasil dikumpulkan.");
    }
}