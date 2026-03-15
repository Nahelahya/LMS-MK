<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\Request;

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
}