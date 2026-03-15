<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class MateriController extends Controller
{
    // ─── INDEX ────────────────────────────────────────────────────────────────
    public function index()
    {
        $materi = Materi::latest()->get();
        return view('materi.index', compact('materi'));
    }

    // ─── STORE (upload) ───────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // Only admin/staff can upload
        abort_unless(in_array(auth()->user()->role, ['admin', 'staff']), 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file'  => 'required|file|mimes:pdf,doc,docx,xls,xlsx,mp4,jpg,jpeg,png|max:51200',
        ]);

        $file = $request->file('file');

        // Store in private storage (NOT public) so direct URL access is blocked
        $path = $file->storeAs(
            'materi',
            time() . '_' . $file->getClientOriginalName(),
            'local'
        );

        Materi::create([
            'judul'         => $request->judul,
            'deskripsi'     => $request->deskripsi,
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'tipe_file'     => $file->getClientOriginalExtension(),
            'uploaded_by'   => auth()->id(),
        ]);

        return back()->with('success', 'Materi berhasil diupload.');
    }

    // ─── EDIT ─────────────────────────────────────────────────────────────────
    public function edit(Materi $materi)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'staff']), 403);
        return view('materi.edit', compact('materi'));
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────
    public function update(Request $request, Materi $materi)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'staff']), 403);

        $request->validate([
            'judul'    => 'required|string|max:255',
            'deskripsi'=> 'nullable|string',
            'file'     => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,mp4,jpg,jpeg,png|max:51200',
        ]);

        // If a new file was uploaded, delete old one and store new
        if ($request->hasFile('file')) {
            Storage::disk('local')->delete($materi->file_path);

            $file = $request->file('file');
            $path = $file->storeAs(
                'materi',
                time() . '_' . $file->getClientOriginalName(),
                'local'
            );

            $materi->file_path     = $path;
            $materi->original_name = $file->getClientOriginalName();
            $materi->tipe_file     = $file->getClientOriginalExtension();
        }

        $materi->judul     = $request->judul;
        $materi->deskripsi = $request->deskripsi;
        $materi->save();

        return redirect()->route('materi')->with('success', 'Materi berhasil diperbarui.');
    }

    // ─── DESTROY (delete) ─────────────────────────────────────────────────────
    public function destroy(Materi $materi)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        // Delete the physical file
        Storage::disk('local')->delete($materi->file_path);

        $materi->delete();

        return back()->with('success', 'Materi berhasil dihapus.');
    }

    // ─── DOWNLOAD ─────────────────────────────────────────────────────────────
    public function download(Materi $materi)
    {
        // Any authenticated & verified user can download
        abort_unless(auth()->check(), 403);

        $path = storage_path('app/' . $materi->file_path);

        abort_unless(file_exists($path), 404);

        return response()->download($path, $materi->original_name);
    }
}
