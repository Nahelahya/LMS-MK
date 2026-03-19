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
                ...(array) $m,
                'grade' => $this->getGrade($m->rata_rata),
                'fill'  => $this->getFillColor($m->rata_rata),
            ]);

        $rataRata = round((float) Nilai::where('user_id', $userId)->avg('nilai'), 1);

        $attendance = Attendance::where('user_id', $userId)
            ->selectRaw("COUNT(*) as total, SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir")
            ->first();

        $totalAttendance = $attendance->total ?? 0;
        $totalHadir      = $attendance->hadir ?? 0;
        $persenHadir     = $totalAttendance > 0 ? round(($totalHadir / $totalAttendance) * 100) : 0;

        // Nama variabel $tugasSelesai — cocok dengan compact() di bawah
        $tugasSelesai = Nilai::where('user_id', $userId)->where('tipe', 'tugas')->count();

        $aktivitasTerbaru = Nilai::where('user_id', $userId)->latest()->limit(5)->get();

        $nilaiMingguan = Nilai::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as tgl, AVG(nilai) as avg_nilai')
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

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
            'user', 'nilaiPerMapel', 'rataRata',
            'tugasSelesai',         // sama dengan nama deklarasi di atas
            'totalHadir', 'totalAttendance', 'persenHadir',
            'aktivitasTerbaru', 'nilaiMingguan', 'streakArray', 'currentStreak'
        ));
    }

    /* ================= STAFF ================= */
    private function staffView(User $user)
    {
        $siswaList  = User::where('role', 'student')->get();
        $siswaIds   = $siswaList->pluck('id');
        $totalSiswa = $siswaList->count();
        $kkm        = 75;

        // ── Statistik global (4 query) ────────────────────────────────

        $rataRataKelas = round((float) Nilai::whereIn('user_id', $siswaIds)->avg('nilai'), 1);

        $attendanceGlobal = Attendance::whereIn('user_id', $siswaIds)
            ->selectRaw("COUNT(*) as total, SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir")
            ->first();

        $kehadiranRata = ($attendanceGlobal->total ?? 0) > 0
            ? round(($attendanceGlobal->hadir / $attendanceGlobal->total) * 100)
            : 0;

        // Nama variabel $tugasSelesaiSemua — cocok dengan compact() di bawah
        $tugasSelesaiSemua = Nilai::whereIn('user_id', $siswaIds)->where('tipe', 'tugas')->count();

        // ── Preload per siswa (3 query untuk semua) ───────────────────

        $avgPerUser = Nilai::whereIn('user_id', $siswaIds)
            ->selectRaw('user_id, AVG(nilai) as avg_nilai')
            ->groupBy('user_id')
            ->get()->keyBy('user_id');

        $tugasPerUser = Nilai::whereIn('user_id', $siswaIds)
            ->where('tipe', 'tugas')
            ->selectRaw('user_id, COUNT(*) as total_tugas')
            ->groupBy('user_id')
            ->get()->keyBy('user_id');

        $attendancePerUser = Attendance::whereIn('user_id', $siswaIds)
            ->selectRaw("user_id, COUNT(*) as total, SUM(CASE WHEN status='hadir' THEN 1 ELSE 0 END) as hadir")
            ->groupBy('user_id')
            ->get()->keyBy('user_id');

        // ── Lulus KKM ────────────────────────────────────────────────

        $lulusKkm = $avgPerUser->filter(fn($i) => (float) $i->avg_nilai >= $kkm)->count();

        // ── Distribusi A/B/C/D — dibutuhkan view untuk grafik bar ────
        // Dihitung dari $avgPerUser yang sudah ada (0 query tambahan)

        $distribusiA = $avgPerUser->filter(fn($i) => (float) $i->avg_nilai >= 85)->count();
        $distribusiB = $avgPerUser->filter(fn($i) => (float) $i->avg_nilai >= 75 && (float) $i->avg_nilai < 85)->count();
        $distribusiC = $avgPerUser->filter(fn($i) => (float) $i->avg_nilai >= 65 && (float) $i->avg_nilai < 75)->count();
        $distribusiD = $avgPerUser->filter(fn($i) => (float) $i->avg_nilai < 65)->count();

        // ── Rata-rata per mata pelajaran — dibutuhkan view (section bawah) ──

        $rataPerMapel = Nilai::whereIn('user_id', $siswaIds)
            ->select('mata_pelajaran', DB::raw('AVG(nilai) as rata_rata'))
            ->groupBy('mata_pelajaran')
            ->orderByDesc('rata_rata')
            ->get()
            ->map(function ($m) use ($kkm, $siswaIds) {
                // Hitung jumlah siswa lulus KKM di mapel ini
                $lulus = Nilai::whereIn('user_id', $siswaIds)
                    ->where('mata_pelajaran', $m->mata_pelajaran)
                    ->selectRaw('user_id, AVG(nilai) as avg_mapel')
                    ->groupBy('user_id')
                    ->havingRaw('AVG(nilai) >= ?', [$kkm])
                    ->count();

                return (object) [
                    'mata_pelajaran' => $m->mata_pelajaran,
                    'rata_rata'      => round($m->rata_rata, 1),
                    'grade'          => $this->getGrade($m->rata_rata),
                    'fill'           => $this->getFillColor($m->rata_rata),
                    'lulus'          => $lulus,
                ];
            });

        // ── Data siswa — 0 query di dalam loop ───────────────────────

        $siswaData = $siswaList->map(function ($s) use ($avgPerUser, $tugasPerUser, $attendancePerUser, $kkm) {
            $avg   = (float) ($avgPerUser[$s->id]->avg_nilai ?? 0);
            $tugas = $tugasPerUser[$s->id]->total_tugas ?? 0;

            $att          = $attendancePerUser[$s->id] ?? null;
            $total        = $att->total ?? 0;
            $hadir        = $att->hadir ?? 0;
            $kehadiranPct = $total > 0 ? round(($hadir / $total) * 100) : 0;

            return [
                'id'              => $s->id,
                'nama'            => $s->name,
                'kelas'           => $s->kelas ?? '-',
                'avg'             => round($avg, 1),
                'grade'           => $this->getGrade($avg),
                'fill'            => $this->getFillColor($avg),
                'tugas'           => $tugas,
                'kehadiran'       => $kehadiranPct . '%',   // string untuk tampilan teks
                'kehadiran_pct'   => $kehadiranPct,          // angka untuk logika warna di view
                'trend'           => 'stabil',
                'butuh_perhatian' => $avg < $kkm || $kehadiranPct < 75,
            ];
        })->sortByDesc('avg')->values();

        // ── Siswa butuh perhatian — dibutuhkan view (kartu kanan atas) ──

        $perhatianSiswa = $siswaData
            ->filter(fn($s) => $s['butuh_perhatian'])
            ->map(fn($s) => array_merge($s, ['kehadiran_pct' => (int) $s['kehadiran_pct']]))
            ->values();

        return view('progress.staff', compact(
            'user',
            'siswaData',
            'totalSiswa',
            'rataRataKelas',
            'kehadiranRata',
            'tugasSelesaiSemua',    // ← deklarasi di atas = 'tugasSelesaiSemua' ✓
            'lulusKkm',
            'kkm',
            'distribusiA',          // ← baru: grafik distribusi
            'distribusiB',
            'distribusiC',
            'distribusiD',
            'perhatianSiswa',       // ← baru: kartu perhatian khusus
            'rataPerMapel'          // ← baru: section rata-rata per mapel
        ));
    }

    /* ================= HELPER ================= */
    // PENTING: Kedua method ini dipanggil oleh studentView() DAN staffView()
    // Jangan hapus saat mengedit method di atas!

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