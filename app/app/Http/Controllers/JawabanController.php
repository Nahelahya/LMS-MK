<?php

namespace App\Http\Controllers;

use App\Models\Jawaban;
use Illuminate\Http\Request;

class JawabanController extends Controller
{
    public function store(Request $request)
    {
        // Only students submit answers
        abort_unless(auth()->user()->role === 'student', 403);

        $request->validate([
            'materi_id' => 'required|exists:materi,id',
            'file'      => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
        ]);

        $path = $request->file('file')->store('tugas', 'local');

        Jawaban::create([
            'materi_id' => $request->materi_id,
            'student_id'=> auth()->id(),
            'file_path' => $path,
        ]);

        return back()->with('success', 'Tugas berhasil dikumpulkan.');
    }
}
