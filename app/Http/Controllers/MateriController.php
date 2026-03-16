<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    public function index()
    {
        $materi = Materi::latest()->get();
        return view('materi.index', compact('materi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'file' => 'required|mimes:pdf,doc,docx,xls,xlsx,mp4,jpg,jpeg,png|max:20000'
        ]);

        $file = $request->file('file');

        $path = $file->store('materi','public');

        Materi::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'file_path' => $path,
            'tipe_file' => $file->getClientOriginalExtension(),
            'uploaded_by' => auth()->user()->id,
        ]);

        return back();
    }

public function download($id)
{
    $materi = Materi::findOrFail($id);

    return response()->download(
        storage_path('app/public/' . $materi->file_path),
        $materi->judul . '.' . $materi->tipe_file
    );
}

public function edit($id)
{
    $materi = Materi::findOrFail($id);
    return view('materi.edit', compact('materi'));
}

public function update(Request $request, $id)
{
    $materi = Materi::findOrFail($id);

    $materi->update([
        'judul' => $request->judul,
        'deskripsi' => $request->deskripsi
    ]);

    return redirect()->route('materi');
}

public function destroy($id)
{
    $materi = Materi::findOrFail($id);

    if($materi->file_path){
        \Storage::disk('public')->delete($materi->file_path);
    }

    $materi->delete();

    return back();
}}