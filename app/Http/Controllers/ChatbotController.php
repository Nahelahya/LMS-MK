<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $user    = Auth::user();
        $role    = $user->role;
        $message = $request->input('message');

        $context      = $this->buildContext($user, $role);
        $systemPrompt = $this->buildSystemPrompt($user, $role, $context);
        $reply        = $this->callGroq($systemPrompt, $message);

        return response()->json(['reply' => $reply]);
    }

    // ─── Router context ───────────────────────────────────────────────
    private function buildContext($user, string $role): array
    {
        if (in_array($role, ['admin', 'staff'])) return $this->buildStaffContext();
        if ($role === 'student') return $this->buildStudentContext($user->id);
        return [];
    }

    // ─── Context STAFF ────────────────────────────────────────────────
    private function buildStaffContext(): array
    {
        $atRiskByScore = DB::table('nilais as n')
            ->join('users as u', 'n.user_id', '=', 'u.id')
            ->where('u.role', 'student')
            ->select('u.id', 'u.name',
                DB::raw("ROUND(AVG(n.nilai)::numeric, 1) AS avg_nilai"),
                DB::raw("COUNT(n.id) AS jumlah_nilai"))
            ->groupBy('u.id', 'u.name')
            ->having(DB::raw('AVG(n.nilai)'), '<', 70)
            ->orderBy('avg_nilai')
            ->limit(20)->get()->toArray();

        $lowAttendance = DB::table('attendances as a')
            ->join('users as u', 'a.user_id', '=', 'u.id')
            ->where('u.role', 'student')
            ->select('u.id', 'u.name',
                DB::raw("COUNT(*) AS total_pertemuan"),
                DB::raw("COUNT(CASE WHEN a.status = 'hadir' THEN 1 END) AS hadir"),
                DB::raw("ROUND(COUNT(CASE WHEN a.status = 'hadir' THEN 1 END) * 100.0 / NULLIF(COUNT(*), 0)::numeric, 1) AS pct_hadir"))
            ->groupBy('u.id', 'u.name')
            ->havingRaw("COUNT(CASE WHEN a.status = 'hadir' THEN 1 END) * 100.0 / NULLIF(COUNT(*), 0) < 75")
            ->orderBy('pct_hadir')
            ->limit(20)->get()->toArray();

        $atRiskFlag = DB::table('student_progress as sp')
            ->join('users as u', 'sp.user_id', '=', 'u.id')
            ->where('u.role', 'student')
            ->where('sp.is_at_risk', true)
            ->select('u.id', 'u.name', 'sp.status_adaptif', 'sp.last_score', 'sp.completion_percentage')
            ->orderBy('sp.last_score')
            ->limit(20)->get()->toArray();

        $avgPerMapel = DB::table('nilais as n')
            ->join('users as u', 'n.user_id', '=', 'u.id')
            ->where('u.role', 'student')
            ->select('n.mata_pelajaran',
                DB::raw("ROUND(AVG(n.nilai)::numeric, 1) AS avg_nilai"),
                DB::raw("COUNT(DISTINCT n.user_id) AS jumlah_siswa"))
            ->groupBy('n.mata_pelajaran')
            ->orderBy('avg_nilai')
            ->get()->toArray();

        // Data hasil quiz adaptif per siswa
        $hasilQuiz = DB::table('quiz_sessions as qs')
            ->join('users as u', 'qs.user_id', '=', 'u.id')
            ->where('qs.status', 'selesai')
            ->select('u.name', 'qs.mata_pelajaran',
                DB::raw('MAX(qs.skor_akhir) as skor_terbaik'),
                DB::raw('COUNT(*) as jumlah_quiz'))
            ->groupBy('u.id', 'u.name', 'qs.mata_pelajaran')
            ->orderBy('u.name')
            ->limit(30)->get()->toArray();

        return compact('atRiskByScore', 'lowAttendance', 'atRiskFlag', 'avgPerMapel', 'hasilQuiz');
    }

    // ─── Context STUDENT ──────────────────────────────────────────────
    private function buildStudentContext(int $userId): array
    {
        $nilais = DB::table('nilais')
            ->where('user_id', $userId)
            ->select('mata_pelajaran', 'judul', 'tipe', 'nilai', 'keterangan', 'created_at')
            ->orderBy('created_at', 'desc')->limit(30)->get()->toArray();

        $avgPerMapel = DB::table('nilais')
            ->where('user_id', $userId)
            ->select('mata_pelajaran',
                DB::raw("ROUND(AVG(nilai)::numeric, 1) AS avg_nilai"),
                DB::raw("COUNT(*) AS jumlah_nilai"),
                DB::raw("MIN(nilai) AS nilai_terendah"),
                DB::raw("MAX(nilai) AS nilai_tertinggi"))
            ->groupBy('mata_pelajaran')->orderBy('avg_nilai')->get()->toArray();

        $kelasDiikuti = DB::table('kelas_siswa as ks')
            ->join('kelas as k', 'ks.kelas_id', '=', 'k.id')
            ->where('ks.user_id', $userId)
            ->select('k.id', 'k.nama_kelas', 'k.mata_pelajaran')->get();

        $kelasIds = $kelasDiikuti->pluck('id')->toArray();

        $tugasOverdue = [];
        if (!empty($kelasIds)) {
            $tugasOverdue = DB::table('materi as m')
                ->join('kelas as k', 'm.kelas_id', '=', 'k.id')
                ->leftJoin('jawabans as j', function ($join) use ($userId) {
                    $join->on('j.materi_id', '=', 'm.id')->where('j.student_id', '=', $userId);
                })
                ->whereIn('m.kelas_id', $kelasIds)
                ->whereNotNull('m.deadline')
                ->where('m.deadline', '<', now()->toDateString())
                ->whereNull('j.id')
                ->select('m.judul', 'm.deadline', 'k.mata_pelajaran', 'k.nama_kelas')
                ->orderBy('m.deadline')->limit(10)->get()->toArray();
        }

        $att = DB::table('attendances')->where('user_id', $userId)
            ->select(
                DB::raw("COUNT(*) AS total"),
                DB::raw("COUNT(CASE WHEN status = 'hadir' THEN 1 END) AS hadir"),
                DB::raw("COUNT(CASE WHEN status = 'sakit' THEN 1 END) AS sakit"),
                DB::raw("COUNT(CASE WHEN status = 'izin' THEN 1 END) AS izin"),
                DB::raw("COUNT(CASE WHEN status = 'alfa' THEN 1 END) AS alfa"))
            ->first();

        $progressAdaptif = DB::table('student_progress')->where('user_id', $userId)
            ->select('status_adaptif', 'last_score', 'completion_percentage', 'is_at_risk')
            ->get()->toArray();

        // Data quiz siswa ini
        $hasilQuizSiswa = DB::table('quiz_sessions')
            ->where('user_id', $userId)
            ->where('status', 'selesai')
            ->select('mata_pelajaran',
                DB::raw('MAX(skor_akhir) as skor_terbaik'),
                DB::raw('COUNT(*) as jumlah_quiz'),
                DB::raw('MAX(created_at) as terakhir_quiz'))
            ->groupBy('mata_pelajaran')
            ->orderBy('skor_terbaik')
            ->get()->toArray();

        return [
            'nilais'          => $nilais,
            'avgPerMapel'     => $avgPerMapel,
            'kelasDiikuti'    => $kelasDiikuti->toArray(),
            'tugasOverdue'    => $tugasOverdue,
            'attendance'      => $att,
            'progressAdaptif' => $progressAdaptif,
            'hasilQuizSiswa'  => $hasilQuizSiswa,
        ];
    }

    // ─── System prompt ────────────────────────────────────────────────
    private function buildSystemPrompt($user, string $role, array $context): string
    {
        $lang            = $user->language ?? 'id';
        $langInstruction = $lang === 'en' ? 'Always respond in English.' : 'Selalu jawab dalam Bahasa Indonesia.';

        if (in_array($role, ['admin', 'staff'])) {
            return $this->staffPrompt($langInstruction, $context);
        }

        return $this->studentPrompt($user, $langInstruction, $context);
    }

    private function staffPrompt(string $langInstruction, array $ctx): string
    {
        $scoreJson  = json_encode($ctx['atRiskByScore'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $attendJson = json_encode($ctx['lowAttendance'],  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $flagJson   = json_encode($ctx['atRiskFlag'],     JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $mapelJson  = json_encode($ctx['avgPerMapel'],    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $quizJson   = json_encode($ctx['hasilQuiz'],      JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return <<<PROMPT
Kamu adalah asisten akademik cerdas untuk sistem LMS sekolah. Tugasmu membantu guru/staf memantau perkembangan siswa berdasarkan data nyata. {$langInstruction}

DATA SISWA YANG PERLU DIAWASI:
[1] Nilai rata-rata di bawah KKM (< 70): {$scoreJson}
[2] Kehadiran di bawah 75%: {$attendJson}
[3] Siswa at-risk dari sistem adaptif: {$flagJson}
[4] Rata-rata nilai per mata pelajaran: {$mapelJson}
[5] Hasil quiz adaptif per siswa (skor_terbaik = nilai tertinggi yang pernah dicapai): {$quizJson}

INSTRUKSI: Gunakan data di atas sebagai satu-satunya referensi. Jangan mengarang data. Sebutkan nama spesifik jika ditanya siapa yang perlu diawasi. Prioritaskan siswa yang muncul di lebih dari satu kategori. Jawab ringkas dan langsung ke poin.
PROMPT;
    }

    private function studentPrompt($user, string $langInstruction, array $ctx): string
    {
        $avgJson      = json_encode($ctx['avgPerMapel'],    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $kelasJson    = json_encode($ctx['kelasDiikuti'],   JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $overdueJson  = json_encode($ctx['tugasOverdue'],   JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $adaptifJson  = json_encode($ctx['progressAdaptif'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $quizJson     = json_encode($ctx['hasilQuizSiswa'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $att   = $ctx['attendance'];
        $total = $att->total ?? 0;
        $hadir = $att->hadir ?? 0;
        $sakit = $att->sakit ?? 0;
        $izin  = $att->izin  ?? 0;
        $alfa  = $att->alfa  ?? 0;
        $pct   = $total > 0 ? round($hadir / $total * 100, 1) : 0;

        return <<<PROMPT
Kamu adalah asisten belajar pribadi untuk {$user->name} dalam sistem LMS sekolah. {$langInstruction}

DATA BELAJAR {$user->name}:
Kelas yang diikuti: {$kelasJson}
Rata-rata nilai per mata pelajaran: {$avgJson}
Tugas belum dikumpulkan (deadline lewat): {$overdueJson}
Kehadiran: {$hadir}/{$total} pertemuan ({$pct}%) | Sakit: {$sakit} | Izin: {$izin} | Alfa: {$alfa}
Status adaptif: {$adaptifJson}
Hasil quiz adaptif: {$quizJson}

INSTRUKSI: Gunakan data di atas sebagai satu-satunya referensi. Jika ditanya materi apa yang belum dikuasai, lihat avg_nilai terendah dan skor quiz terendah. Berikan saran belajar spesifik, positif, dan actionable. Gunakan bahasa ramah dan mudah dipahami siswa.
PROMPT;
    }

    // ─── Groq API ─────────────────────────────────────────────────────
    private function callGroq(string $systemPrompt, string $userMessage): string
    {
        $apiKey = config('services.groq.key');

        if (empty($apiKey)) {
            Log::error('ChatbotController: GROQ_API_KEY belum diset di .env');
            return __('messages.chatbot_error');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
            'model'       => 'llama-3.1-8b-instant',
            'max_tokens'  => 1024,
            'temperature' => 0.7,
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $userMessage],
            ],
        ]);

        if ($response->failed()) {
            Log::error('ChatbotController: Groq API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return __('messages.chatbot_error');
        }

        return $response->json('choices.0.message.content') ?? __('messages.chatbot_error');
    }
}