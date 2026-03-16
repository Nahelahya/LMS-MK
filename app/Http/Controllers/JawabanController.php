<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JawabanMurid;

class JawabanController extends Controller
{

    public function store(Request $request)
    {

        $file = $request->file('file')->store('tugas','public');

        JawabanMurid::create([
            'file_path' => $file,
            'user_id' => auth()->id()
        ]);

        return back()->with('success','Tugas berhasil diupload');

    }

}