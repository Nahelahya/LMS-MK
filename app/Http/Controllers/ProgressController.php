<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return in_array($user->role, ['admin', 'staff'])
            ? $this->staffView($user)
            : $this->studentView($user);
    }

    /* ================= STUDENT ================= */
    private function studentView(User $user)
    {
        $userId = $user->id;

        // Nilai per mapel
        $nilaiPerMapel = Nilai::where('user_id', $userId)
            ->select(
                'mata_pelajaran',
                DB::raw('AVG(nilai) as rata_rata'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('mata_pelajaran')
            ->get()
            ->map(fn($m) => [
                ... (array) $m,
                'grade' => $this->getGrade($m->rata_rata),
                'fill'  => $this->getFillColor($m->rata_rata),
            ]);

        // Statistik nilai
        $rataRata = round((float) Nilai::where('user_id', $userId)->avg('nilai'), 1);

        // Attendance (1 query, bukan 2)
        $attendance = Attendance::where('user_id', $userId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir
            ")
            ->first();

        $totalAttendance = $attendance->total ?? 0;
        $totalHadir      = $attendance->hadir ?? 0;

        $persenHadir = $totalAttendance > 0
            ? round(($totalHadir / $totalAttendance) * 100)
            : 0;

        // Tugas
        $tugasSelesai = Nilai::where('user_id', $userId)
            ->where('tipe', 'tugas')
            ->count();

        // Aktivitas
        $aktivitasTerbaru = Nilai::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        // Grafik mingguan
        $nilaiMingguan = Nilai::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as tgl, AVG(nilai) as avg_nilai')
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        // Streak
        $rawStreak = Nilai::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(28))
            ->selectRaw('DATE(created_at) as tgl, COUNT(*) as n')
            ->groupBy('tgl')
            ->pluck('n', 'tgl');

        $streakArray = collect(range(0, 27))->map(function ($i) use ($rawStreak) {
            $date  = now()->subDays(27 - $i)->format('Y-m-d');
            $count = $rawStreak[$date] ?? 0;

            return [
                'date'  => $date,
                'level' => $count === 0 ? 0 : ($count <= 1 ? 1 : ($count <= 3 ? 2 : 3)),
                'today' => $i === 27,
            ];
        });

        $currentStreak = $streakArray->reverse()->takeWhile(fn($d) => $d['level'] > 0)->count();

        return view('progress.student', compact(
            'user',
            'nilaiPerMapel',
            'rataRata',
            'tugasSelesai',
            'totalHadir',
            'totalAttendance',
            'persenHadir',
            'aktivitasTerbaru',
            'nilaiMingguan',
            'streakArray',
            'currentStreak'
        ));
    }

    /* ================= STAFF ================= */
    private function staffView(User $user)
    {
        $siswaList  = User::where('role', 'student')->get();
        $siswaIds   = $siswaList->pluck('id');
        $totalSiswa = $siswaList->count();
        $kkm        = 75;

        // Statistik global
        $rataRataKelas = round((float) Nilai::whereIn('user_id', $siswaIds)->avg('nilai'), 1);

        $attendanceGlobal = Attendance::whereIn('user_id', $siswaIds)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir
            ")
            ->first();

        $totalAttendance = $attendanceGlobal->total ?? 0;
        $totalHadir      = $attendanceGlobal->hadir ?? 0;

        $kehadiranRata = $totalAttendance > 0
            ? round(($totalHadir / $totalAttendance) * 100)
            : 0;

        $tugasSelesai = Nilai::whereIn('user_id', $siswaIds)
            ->where('tipe', 'tugas')
            ->count();

        // Preload attendance per siswa (hemat query)
        $attendancePerUser = Attendance::whereIn('user_id', $siswaIds)
            ->select(
                'user_id',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status='hadir' THEN 1 ELSE 0 END) as hadir")
            )
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        // Data siswa
        $siswaData = $siswaList->map(function ($s) use ($attendancePerUser, $kkm) {
            $avg = (float) Nilai::where('user_id', $s->id)->avg('nilai');

            $att = $attendancePerUser[$s->id] ?? null;
            $total = $att->total ?? 0;
            $hadir = $att->hadir ?? 0;

            $kehadiran = $total > 0 ? round(($hadir / $total) * 100) : 0;

            $tugas = Nilai::where('user_id', $s->id)->where('tipe', 'tugas')->count();

            return [
                'id'        => $s->id,
                'nama'      => $s->name,
                'kelas'     => $s->kelas ?? '-',
                'avg'       => $avg,
                'grade'     => $this->getGrade($avg),
                'fill'      => $this->getFillColor($avg),
                'tugas'     => $tugas,
                'kehadiran' => $kehadiran . '%',
                'trend'     => 'stabil',
                'butuh_perhatian' => $avg < $kkm || $kehadiran < 75,
            ];
        })->sortByDesc('avg')->values();

        return view('progress.staff', compact(
            'user',
            'siswaData',
            'totalSiswa',
            'rataRataKelas',
            'kehadiranRata',
            'tugasSelesai',
            'kkm'
        ));
    }

    /* ================= HELPER ================= */
    private function getGrade(float $n): string
    {
        return match (true) {
            $n >= 85 => 'A',
            $n >= 75 => 'B',
            $n >= 65 => 'C',
            $n >= 55 => 'D',
            default  => 'E',
        };
    }

    private function getFillColor(float $n): string
    {
        return match (true) {
            $n >= 85 => 'bg-green-500',
            $n >= 75 => 'bg-blue-500',
            $n >= 65 => 'bg-yellow-500',
            default  => 'bg-red-500',
        };
    }
}