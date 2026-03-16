<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Kelas;
use App\Models\StudentProgress;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role;

        // ══════════════════════════════════════════
        // ADMIN & STAFF
        // ══════════════════════════════════════════
        if ($role === 'admin' || $role === 'staff') {

            // ✅ chart_data dari database, bukan hardcode
            // ✅ PostgreSQL: cast ke ::numeric agar ROUND() bisa terima 2 argumen
            //              DATE() diganti ::date
            $quizScores = StudentProgress::select(
                    DB::raw('ROUND(AVG(last_score)::numeric, 1) as avg'),
                    DB::raw('updated_at::date as tanggal')
                )
                ->whereNotNull('last_score')
                ->groupBy('tanggal')
                ->orderBy('tanggal')
                ->take(5)
                ->get();

            $chart_labels = $quizScores->pluck('tanggal')
                ->map(fn($d) => \Carbon\Carbon::parse($d)->translatedFormat('d M'))
                ->toArray();

            $chart_data = $quizScores->pluck('avg')->toArray();

            // Fallback kalau belum ada data
            if (empty($chart_labels)) {
                $chart_labels = ['Kuis 1', 'Kuis 2', 'Kuis 3', 'Kuis 4', 'Kuis 5'];
                $chart_data   = [0, 0, 0, 0, 0];
            }

            return view('dashboard.admin_staff', [
                'total_siswa'    => User::where('role', 'student')->count(),
                // ✅ eager load progress sekaligus supaya tidak N+1
                'daftar_murid'   => User::where('role', 'student')
                                        ->with('progress')
                                        ->latest()
                                        ->get(),
                'siswa_beresiko' => StudentProgress::where('is_at_risk', true)
                                        ->with('user')
                                        ->latest()
                                        ->take(5)
                                        ->get(),
                'avg_score'      => round(StudentProgress::avg('last_score') ?? 0, 1),
                'chart_labels'   => $chart_labels,
                'chart_data'     => $chart_data,
            ]);
        }

        // ══════════════════════════════════════════
        // STUDENT
        // ══════════════════════════════════════════
        if ($role === 'student') {

            // ✅ FIX: Course milik siswa saja (lewat pivot kelas_user),
            //    bukan Course::all() yang ambil semua course di database
            $kelasSiswa = $user->kelas()->pluck('kelas.id'); // pivot: kelas_siswa ✅
            $my_courses = Course::whereIn('kelas_id', $kelasSiswa)
                                ->with(['progress' => fn($q) => $q->where('user_id', $user->id)])
                                ->get();

            // ✅ Bar chart: aktivitas nyata per hari (7 hari terakhir)
            $dayLabels    = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            $weekly_hours = collect(range(6, 0))->map(function ($daysAgo) use ($user, $dayLabels) {
                $date  = now()->subDays($daysAgo)->toDateString();
                $count = ActivityLog::where('user_id', $user->id)
                                    ->whereDate('created_at', $date)
                                    ->count();
                return [
                    'label' => $dayLabels[now()->subDays($daysAgo)->dayOfWeek],
                    'hours' => round($count * 0.5, 1),
                ];
            });

            // ✅ Streak: hari berturut-turut aktif (mulai dari hari ini)
            $streak = 0;
            for ($i = 0; $i < 30; $i++) {
                $ada = ActivityLog::where('user_id', $user->id)
                                  ->whereDate('created_at', now()->subDays($i)->toDateString())
                                  ->exists();
                if ($ada) $streak++;
                else      break;
            }

            return view('dashboard.student', [
                'my_courses'   => $my_courses,
                'my_progress'  => StudentProgress::where('user_id', $user->id)->first(),
                'activities'   => ActivityLog::where('user_id', $user->id)
                                             ->latest()
                                             ->take(5)
                                             ->get(),
                'weekly_hours' => $weekly_hours,
                'streak'       => $streak,
            ]);
        }

        abort(403, 'Role tidak dikenal: ' . $user->role);
    }
}