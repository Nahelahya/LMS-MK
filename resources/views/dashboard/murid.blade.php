<h1 class="text-2xl font-bold">Materi Pembelajaran</h1>

@foreach($materi as $m)

<div class="bg-white p-4 rounded shadow">

<h2 class="font-semibold">
{{ $m->judul }}
</h2>

<a href="{{ asset('storage/'.$m->file_path) }}">
Download Materi
</a>

</div>

@endforeach