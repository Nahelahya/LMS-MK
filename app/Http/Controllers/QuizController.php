<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuizController extends Controller
{
    // ══════════════════════════════════════════════
    // STAFF — INPUT SOAL
    // ══════════════════════════════════════════════

    public function soalIndex(Kelas $kelas)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);

        $soals = DB::table('soals')
            ->where('kelas_id', $kelas->id)
            ->orderBy('mata_pelajaran')
            ->orderBy('level')
            ->get();

        return view('quiz.staff.soal-index', compact('kelas', 'soals'));
    }

    public function soalStore(Request $request, Kelas $kelas)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);

        $data = $request->validate([
            'mata_pelajaran' => 'required|string|max:100',
            'pertanyaan'     => 'required|string',
            'tipe'           => 'required|in:pilihan_ganda,essay',
            'level'          => 'required|in:mudah,sedang,sulit',
            'opsi_a'         => 'nullable|string',
            'opsi_b'         => 'nullable|string',
            'opsi_c'         => 'nullable|string',
            'opsi_d'         => 'nullable|string',
            'jawaban_benar'  => $request->tipe === 'pilihan_ganda'
                                    ? 'required|in:a,b,c,d'
                                    : 'nullable|string',
            'pembahasan'     => 'nullable|string',
        ]);

        if ($data['tipe'] === 'pilihan_ganda') {
            $request->validate([
                'opsi_a'        => 'required|string',
                'opsi_b'        => 'required|string',
                'jawaban_benar' => 'required|in:a,b,c,d',
            ]);
        }

        DB::table('soals')->insert([
            'kelas_id'       => $kelas->id,
            'staff_id'       => auth()->id(),
            'mata_pelajaran' => $data['mata_pelajaran'],
            'pertanyaan'     => $data['pertanyaan'],
            'tipe'           => $data['tipe'],
            'level'          => $data['level'],
            'opsi_a'         => $data['opsi_a'] ?? null,
            'opsi_b'         => $data['opsi_b'] ?? null,
            'opsi_c'         => $data['opsi_c'] ?? null,
            'opsi_d'         => $data['opsi_d'] ?? null,
            'jawaban_benar'  => $data['jawaban_benar'] ?? null,
            'pembahasan'     => $data['pembahasan'] ?? null,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return back()->with('success', 'Soal berhasil ditambahkan!');
    }

    public function soalDestroy(Kelas $kelas, $soalId)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);
        DB::table('soals')->where('id', $soalId)->where('kelas_id', $kelas->id)->delete();
        return back()->with('success', 'Soal dihapus.');
    }

    // ══════════════════════════════════════════════
    // STUDENT — MULAI QUIZ
    // ══════════════════════════════════════════════

    public function start(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'kelas_id'       => 'required|exists:kelas,id',
            'mata_pelajaran' => 'required|string',
        ]);

        $terdaftar = DB::table('kelas_siswa')
            ->where('user_id', $user->id)
            ->where('kelas_id', $request->kelas_id)
            ->exists();

        abort_unless($terdaftar, 403);

        $progress = DB::table('student_progress')
            ->join('courses as c', 'student_progress.course_id', '=', 'c.id')
            ->where('student_progress.user_id', $user->id)
            ->where('c.kelas_id', $request->kelas_id)
            ->orderBy('student_progress.updated_at', 'desc')
            ->select('student_progress.last_score', 'student_progress.status_adaptif')
            ->first();

        $levelAwal = 'sedang';
        if ($progress) {
            if ($progress->last_score >= 80)     $levelAwal = 'sulit';
            elseif ($progress->last_score < 60)  $levelAwal = 'mudah';
        }

        $jumlahSoal = DB::table('soals')
            ->where('kelas_id', $request->kelas_id)
            ->where('mata_pelajaran', $request->mata_pelajaran)
            ->count();

        if ($jumlahSoal === 0) {
            return back()->with('error', 'Belum ada soal untuk mata pelajaran ini.');
        }

        $sessionId = DB::table('quiz_sessions')->insertGetId([
            'user_id'        => $user->id,
            'kelas_id'       => $request->kelas_id,
            'mata_pelajaran' => $request->mata_pelajaran,
            'level_awal'     => $levelAwal,
            'total_soal'     => min(25, $jumlahSoal),
            'soal_ke'        => 0,
            'benar'          => 0,
            'salah'          => 0,
            'status'         => 'aktif',
            'soal_ids'       => json_encode([]),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return redirect()->route('quiz.soal', $sessionId);
    }

    // ══════════════════════════════════════════════
    // STUDENT — TAMPILKAN SOAL
    // ══════════════════════════════════════════════

    public function showSoal($sessionId)
    {
        $user    = auth()->user();
        $session = DB::table('quiz_sessions')
            ->where('id', $sessionId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($session->status === 'selesai') {
            return redirect()->route('quiz.hasil', $sessionId);
        }

        $level            = $this->hitungLevelAdaptif($session);
        $sudahDitampilkan = json_decode($session->soal_ids, true) ?? [];

        $soal = DB::table('soals')
            ->where('kelas_id', $session->kelas_id)
            ->where('mata_pelajaran', $session->mata_pelajaran)
            ->where('level', $level)
            ->whereNotIn('id', $sudahDitampilkan)
            ->inRandomOrder()
            ->first();

        // Fallback level lain kalau soal level ini habis
        if (!$soal) {
            $soal = DB::table('soals')
                ->where('kelas_id', $session->kelas_id)
                ->where('mata_pelajaran', $session->mata_pelajaran)
                ->whereNotIn('id', $sudahDitampilkan)
                ->inRandomOrder()
                ->first();
        }

        if (!$soal) {
            return $this->akhiriQuiz($session);
        }

        $nomorSoal = $session->soal_ke + 1;
        $progress  = round(($session->soal_ke / $session->total_soal) * 100);

        return view('quiz.student.soal', compact('session', 'soal', 'nomorSoal', 'progress', 'level'));
    }

    // ══════════════════════════════════════════════
    // STUDENT — SUBMIT JAWABAN
    // ══════════════════════════════════════════════

    public function submitJawaban(Request $request, $sessionId)
    {
        $user    = auth()->user();
        $session = DB::table('quiz_sessions')
            ->where('id', $sessionId)
            ->where('user_id', $user->id)
            ->where('status', 'aktif')
            ->firstOrFail();

        $request->validate([
            'soal_id' => 'required|exists:soals,id',
            'jawaban' => 'required|string',
        ]);

        $soal       = DB::table('soals')->where('id', $request->soal_id)->firstOrFail();
        $isBenar    = false;
        $feedbackAi = null;

        if ($soal->tipe === 'pilihan_ganda') {
            $isBenar = strtolower(trim($request->jawaban)) === strtolower(trim($soal->jawaban_benar));
        } else {
            $feedbackAi = $this->nilaiEssay($soal->pertanyaan, $soal->jawaban_benar, $request->jawaban);
            $isBenar    = str_starts_with(strtolower(trim($feedbackAi)), 'benar');
        }

        DB::table('quiz_jawabans')->insert([
            'session_id'    => $sessionId,
            'soal_id'       => $soal->id,
            'user_id'       => $user->id,
            'jawaban_siswa' => $request->jawaban,
            'is_benar'      => $isBenar,
            'feedback_ai'   => $feedbackAi,
            'level_soal'    => $soal->level,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $sudahDitampilkan   = json_decode($session->soal_ids, true) ?? [];
        $sudahDitampilkan[] = $soal->id;

        $benar  = $session->benar + ($isBenar ? 1 : 0);
        $salah  = $session->salah + ($isBenar ? 0 : 1);
        $soalKe = $session->soal_ke + 1;

        if ($soalKe >= $session->total_soal) {
            $skor = round(($benar / $session->total_soal) * 100, 2);

            DB::table('quiz_sessions')->where('id', $sessionId)->update([
                'soal_ke'    => $soalKe,
                'benar'      => $benar,
                'salah'      => $salah,
                'status'     => 'selesai',
                'skor_akhir' => $skor,
                'soal_ids'   => json_encode($sudahDitampilkan),
                'updated_at' => now(),
            ]);

            DB::table('nilais')->insert([
                'user_id'        => $user->id,
                'mata_pelajaran' => $session->mata_pelajaran,
                'judul'          => 'Quiz Adaptif - ' . $session->mata_pelajaran,
                'tipe'           => 'quiz',
                'nilai'          => $skor,
                'keterangan'     => 'Quiz adaptif ' . now()->format('d/m/Y'),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            $course = DB::table('courses')
                ->where('kelas_id', $session->kelas_id)
                ->first();

            if ($course) {
                DB::table('student_progress')->updateOrInsert(
                    ['user_id' => $user->id, 'course_id' => $course->id],
                    [
                        'last_score'     => $skor,
                        'is_at_risk'     => $skor < 60,
                        'status_adaptif' => match(true) {
                            $skor >= 75 => 'Advance',
                            $skor >= 60 => 'Normal',
                            default     => 'Remedial',
                        },
                        'updated_at' => now(),
                    ]
                );
            }

            return redirect()->route('quiz.hasil', $sessionId);
        }

        DB::table('quiz_sessions')->where('id', $sessionId)->update([
            'soal_ke'    => $soalKe,
            'benar'      => $benar,
            'salah'      => $salah,
            'soal_ids'   => json_encode($sudahDitampilkan),
            'updated_at' => now(),
        ]);

        return redirect()->route('quiz.soal', $sessionId)->with([
            'feedback_isBenar'    => $isBenar,
            'feedback_soal'       => $soal->pertanyaan,
            'feedback_jawaban'    => $request->jawaban,
            'feedback_benar'      => $soal->jawaban_benar,
            'feedback_pembahasan' => $soal->pembahasan,
            'feedback_ai'         => $feedbackAi,
        ]);
    }

    // ══════════════════════════════════════════════
    // STUDENT — HASIL QUIZ
    // ══════════════════════════════════════════════

    public function hasil($sessionId)
    {
        $user    = auth()->user();
        $session = DB::table('quiz_sessions')
            ->where('id', $sessionId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $jawabans = DB::table('quiz_jawabans as qj')
            ->join('soals as s', 'qj.soal_id', '=', 's.id')
            ->where('qj.session_id', $sessionId)
            ->select('qj.*', 's.pertanyaan', 's.tipe', 's.level', 's.pembahasan',
                     's.opsi_a', 's.opsi_b', 's.opsi_c', 's.opsi_d', 's.jawaban_benar')
            ->get();

        return view('quiz.student.hasil', compact('session', 'jawabans'));
    }

    // ══════════════════════════════════════════════
    // HELPER — Hitung level adaptif
    // ══════════════════════════════════════════════

    private function hitungLevelAdaptif($session): string
    {
        if ($session->soal_ke === 0) {
            return $session->level_awal;
        }

        $totalJawab = $session->benar + $session->salah;
        if ($totalJawab === 0) return $session->level_awal;

        $pctBenar  = $session->benar / $totalJawab;
        $levelUrut = ['mudah', 'sedang', 'sulit'];
        $idxSaat   = array_search($session->level_awal, $levelUrut);

        if ($pctBenar > 0.7 && $idxSaat < 2) return $levelUrut[$idxSaat + 1];
        if ($pctBenar < 0.4 && $idxSaat > 0) return $levelUrut[$idxSaat - 1];

        return $session->level_awal;
    }

    private function akhiriQuiz($session)
    {
        $skor = $session->total_soal > 0
            ? round(($session->benar / $session->total_soal) * 100, 2)
            : 0;

        DB::table('quiz_sessions')->where('id', $session->id)->update([
            'status'     => 'selesai',
            'skor_akhir' => $skor,
            'updated_at' => now(),
        ]);

        return redirect()->route('quiz.hasil', $session->id);
    }

    // ══════════════════════════════════════════════
    // HELPER — Nilai essay pakai Groq
    // ══════════════════════════════════════════════

    private function nilaiEssay(string $pertanyaan, ?string $kunciJawaban, string $jawabanSiswa): string
    {
        $apiKey = config('services.groq.key');
        if (empty($apiKey)) return 'Tidak dapat menilai jawaban otomatis.';

        $prompt = "Kamu adalah guru yang menilai jawaban essay siswa.\n\n"
                . "Pertanyaan: {$pertanyaan}\n"
                . ($kunciJawaban ? "Kunci jawaban: {$kunciJawaban}\n" : '')
                . "Jawaban siswa: {$jawabanSiswa}\n\n"
                . "PENTING: Jawaban pertamamu HARUS dimulai dengan salah satu kata ini saja: 'BENAR', atau 'SALAH'.\n"
                . "Contoh: 'BENAR. Jawaban siswa sudah tepat karena...'\n"
                . "Contoh: 'SALAH. Jawaban siswa keliru karena...'\n"
                . "Lanjutkan dengan feedback singkat 2-3 kalimat. Jangan gunakan kata lain di awal jawaban.";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(15)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'      => 'llama-3.1-8b-instant',
                'max_tokens' => 200,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            return $response->json('choices.0.message.content') ?? 'Tidak dapat menilai jawaban.';
        } catch (\Exception $e) {
            Log::error('Quiz essay grading error: ' . $e->getMessage());
            return 'Tidak dapat menilai jawaban otomatis.';
        }
    }
        public function rekapQuiz(Kelas $kelas)
    {
        abort_if($kelas->staff_id !== auth()->id(), 403);
        // Semua siswa di kelas ini
        $siswaList = DB::table('kelas_siswa as ks')
            ->join('users as u', 'ks.user_id', '=', 'u.id')
            ->where('ks.kelas_id', $kelas->id)
            ->where('u.role', 'student')
            ->select('u.id', 'u.name', 'u.email')
            ->orderBy('u.name')
            ->get();
         // Sesi quiz terakhir per siswa untuk kelas ini
        $lastSessions = DB::table('quiz_sessions')
            ->where('kelas_id', $kelas->id)
            ->where('status', 'selesai')
            ->select(
                'user_id',
                'mata_pelajaran',
                DB::raw('MAX(skor_akhir) as skor_terbaik'),
                DB::raw('COUNT(*) as jumlah_quiz'),
                DB::raw('MAX(created_at) as terakhir_quiz')
            )
            ->groupBy('user_id', 'mata_pelajaran')
            ->get()
            ->groupBy('user_id');
         // Jumlah soal tersedia per mata pelajaran
        $jumlahSoal = DB::table('soals')
            ->where('kelas_id', $kelas->id)
            ->select('mata_pelajaran', DB::raw('COUNT(*) as total'))
            ->groupBy('mata_pelajaran')
            ->get();
        // Rata-rata skor per mata pelajaran (semua siswa)
        $avgPerMapel = DB::table('quiz_sessions')
            ->where('kelas_id', $kelas->id)
            ->where('status', 'selesai')
            ->select(
                'mata_pelajaran',
                DB::raw('ROUND(AVG(skor_akhir)::numeric, 1) as avg_skor'),
                DB::raw('COUNT(DISTINCT user_id) as jumlah_peserta')
            )
            ->groupBy('mata_pelajaran')
            ->get();
        return view('quiz.staff.rekap', compact(
            'kelas',
            'siswaList',
            'lastSessions',
            'jumlahSoal',
            'avgPerMapel'
        ));
    }
}