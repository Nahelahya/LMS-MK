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

    public function download(Materi $materi)
    {
        $path = storage_path('app/public/'.$materi->file_path);

        if(!file_exists($path)){
            abort(404);
        }

        $namaFile = $materi->judul.'.'.$materi->tipe_file;

        return response()->download($path,$namaFile);
    }

    public function edit(Materi $materi)
    {
        return view('materi.edit',compact('materi'));
    }

    public function update(Request $request, Materi $materi)
    {
        $request->validate([
            'judul' => 'required',
            'file' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,mp4,jpg,jpeg,png|max:20000'
        ]);

        $data = [
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi
        ];

        if($request->hasFile('file')){

            if($materi->file_path){
                Storage::disk('public')->delete($materi->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('materi','public');

            $data['file_path'] = $path;
            $data['tipe_file'] = $file->getClientOriginalExtension();
        }

        $materi->update($data);

        return redirect()->route('materi');
    }

    public function destroy(Materi $materi)
    {
        if($materi->file_path){
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        return back();
    }

}