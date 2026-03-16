<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\StudentProgress;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role;

        if ($role == 'admin' || $role == 'staff') {
            return view('dashboard.admin_staff', [
                'total_siswa'    => \App\Models\User::where('role', 'student')->count(),
                'daftar_murid'   => \App\Models\User::where('role', 'student')->with('progress')->get(),
                'siswa_beresiko' => \App\Models\StudentProgress::where('is_at_risk', true)->with('user')->take(5)->get(),
                'avg_score'      => \App\Models\StudentProgress::avg('last_score') ?? 0,
                'chart_labels'   => ['Kuis 1', 'Kuis 2', 'Kuis 3', 'Kuis 4', 'Kuis 5'],
                'chart_data'     => [45, 65, 80, 97, 80],
            ]);
        }

        if ($role == 'student') {

            // Bar chart: jumlah aktivitas per hari selama 7 hari terakhir
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

            // Streak: hitung hari berturut-turut yang ada aktivitasnya
            $streak = 0;
            for ($i = 0; $i < 30; $i++) {
                $ada = ActivityLog::where('user_id', $user->id)
                    ->whereDate('created_at', now()->subDays($i)->toDateString())
                    ->exists();
                if ($ada) $streak++;
                else break;
            }

            return view('dashboard.student', [
                'my_courses'   => Course::all(),
                'my_progress'  => StudentProgress::where('user_id', $user->id)->first(),
                'activities'   => ActivityLog::where('user_id', $user->id)->latest()->take(5)->get(),
                'weekly_hours' => $weekly_hours,
                'streak'       => $streak,
            ]);
        }

        abort(403, 'Role tidak dikenal: ' . $user->role);
    }
}