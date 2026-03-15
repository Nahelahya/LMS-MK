@extends('layouts.dash')

@section('title','Materi')

@section('content')

<div class="space-y-8">

<!-- CARD UPLOAD MATERI -->

<div class="bg-white rounded-3xl shadow-sm p-6 lg:p-8">

<h2 class="text-lg font-bold text-[#1B254B] mb-6">
Upload Materi Pembelajaran
</h2>

<form action="{{ route('materi.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
@csrf

<div class="grid md:grid-cols-2 gap-6">

<div>
<label class="text-xs font-bold text-gray-400 uppercase">Judul Materi</label>
<input 
type="text"
name="judul"
class="w-full mt-2 p-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
placeholder="Masukkan judul materi">
</div>

<div>
<label class="text-xs font-bold text-gray-400 uppercase">Upload File</label>

<label class="flex flex-col items-center justify-center h-32 mt-2 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-blue-500 transition">

<i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>

<span class="text-xs text-gray-400 mt-2">
PDF, Word, Excel, Video, JPG
</span>

<input type="file" name="file" class="hidden">

</label>

</div>

</div>

<div>

<label class="text-xs font-bold text-gray-400 uppercase">Deskripsi</label>

<textarea
name="deskripsi"
class="w-full mt-2 p-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
rows="3"
placeholder="Tambahkan deskripsi materi"></textarea>

</div>

<button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl shadow">
Upload Materi
</button>

</form>

</div>


<!-- CARD DAFTAR MATERI -->

<div class="bg-white rounded-3xl shadow-sm p-6 lg:p-8">

<div class="flex justify-between items-center mb-6">

<h2 class="text-lg font-bold text-[#1B254B]">
Daftar Materi
</h2>

<span class="text-xs text-gray-400">
{{ count($materi) }} materi tersedia
</span>

</div>

<div class="overflow-x-auto">

<table class="w-full text-sm">

<thead>

<tr class="text-left text-gray-400 border-b">

<th class="pb-3">Judul</th>
<th class="pb-3">Tipe File</th>
<th class="pb-3">Aksi</th>

</tr>

</thead>

<tbody class="text-[#1B254B]">

@foreach($materi as $m)

<tr class="border-b hover:bg-gray-50 transition">

<td class="py-4 font-semibold">
{{ $m->judul }}
</td>

<td class="py-4 flex items-center space-x-2">

@if($m->tipe_file == 'pdf')
<i class="fas fa-file-pdf text-red-500 text-lg"></i>

@elseif($m->tipe_file == 'doc' || $m->tipe_file == 'docx')
<i class="fas fa-file-word text-blue-600 text-lg"></i>

@elseif($m->tipe_file == 'xls' || $m->tipe_file == 'xlsx')
<i class="fas fa-file-excel text-green-600 text-lg"></i>

@elseif($m->tipe_file == 'mp4')
<i class="fas fa-file-video text-purple-500 text-lg"></i>

@elseif($m->tipe_file == 'jpg' || $m->tipe_file == 'jpeg' || $m->tipe_file == 'png')
<i class="fas fa-file-image text-yellow-500 text-lg"></i>

@else
<i class="fas fa-file text-gray-400 text-lg"></i>
@endif

<span class="text-xs uppercase font-semibold text-gray-500">
{{ $m->tipe_file }}
</span>

</td>

<td class="py-4 space-x-2">

<a href="{{ asset('storage/'.$m->file_path) }}"
class="text-blue-600 hover:underline text-sm font-semibold">

Lihat

</a>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>

@endsection