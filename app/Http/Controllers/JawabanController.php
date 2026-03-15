<?php

namespace App\Http\Controllers;

use App\Models\JawabanMurid;

public function store(Request $request)
{
    $file = $request->file('file')->store('tugas','public');

    JawabanMurid::create([
        'materi_id'=>$request->materi_id,
        'user_id'=>auth()->id(),
        'file_jawaban'=>$file
    ]);

    return back();
}