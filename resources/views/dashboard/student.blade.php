@extends('layouts.dash')

@section('content')
<div class="container-fluid p-4">
    <h2 class="fw-bold mb-4">Dashboard Murid</h2>

    <div class="row">
        <div class="col-md-7">
            <h5 class="fw-bold">Course Saya</h5>
            @foreach($my_courses as $course)
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold">{{ $course->nama_course }}</h6>
                    <div class="progress mt-2" style="height: 10px;">
                        <div class="progress-bar bg-primary" style="width: {{ $my_progress->completion_percentage ?? 0 }}%"></div>
                    </div>
                    <small class="text-muted">{{ $my_progress->completion_percentage ?? 0 }}% Selesai</small>
                </div>
            </div>
            @endforeach

            <div class="mt-4">
                <a href="#" class="btn btn-primary w-100 py-2 fw-bold">Mulai Quiz Adaptif</a>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm p-3 bg-light border-0">
                <h5 class="fw-bold mb-3">Log Aktivitas</h5>
                <div class="timeline">
                    @forelse($activities as $log)
                        <div class="border-start border-primary ps-3 mb-3">
                            <p class="mb-0 fw-bold">{{ $log->activity }}</p>
                            <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <p class="text-muted small">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </div>

            <div class="card mt-3 shadow-sm p-3 bg-primary text-white text-center">
                <h6>Skor Terakhir Anda</h6>
                <h1 class="fw-bold">{{ $my_progress->last_score ?? 0 }}</h1>
            </div>
        </div>
    </div>
</div>
@endsection