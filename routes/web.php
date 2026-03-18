<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\JawabanController;
use App\Http\Controllers\KelasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GithubController;

// ── WELCOME ───────────────────────────────────────────────────────────────────
Route::get('/', fn() => view('welcome'));

// ── GUEST ONLY ────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get ('/login',    [AuthController::class, 'showLogin'])   ->name('login');
    Route::post('/login',    [AuthController::class, 'login'])       ->name('login.post');
    Route::get ('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])    ->name('register.post');
});

// ── OTP + LOGOUT ──────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get ('/verify-otp', [VerificationController::class, 'index']) ->name('otp.index');
    Route::post('/verify-otp', [VerificationController::class, 'verify'])->name('otp.verify');
    Route::post('/logout',     [AuthController::class, 'logout'])        ->name('logout');
});

// ── AUTHENTICATED + VERIFIED ──────────────────────────────────────────────────
Route::middleware(['auth', 'checkstatus'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── KELAS: staff & admin ──────────────────────────────────────────────────
    Route::middleware('staff.only')->group(function () {

        // Kelas CRUD
        Route::get   ('/kelas',              [KelasController::class, 'index'])    ->name('kelas.index');
        Route::get   ('/kelas/buat',         [KelasController::class, 'create'])   ->name('kelas.create');
        Route::post  ('/kelas',              [KelasController::class, 'store'])    ->name('kelas.store');
        Route::get   ('/kelas/{kelas}',      [KelasController::class, 'show'])     ->name('kelas.show');
        Route::delete('/kelas/{kelas}',      [KelasController::class, 'destroy'])  ->name('kelas.destroy');
        Route::patch ('/kelas/{kelas}/kode', [KelasController::class, 'updateKode'])->name('kelas.updateKode');

        // Materi dalam kelas
        Route::post  ('/kelas/{kelas}/materi',                   [KelasController::class, 'materiStore'])         ->name('kelas.materi.store');
        Route::get   ('/kelas/{kelas}/materi/{materi}/unduh',    [KelasController::class, 'materiDownload'])      ->name('kelas.materi.download');
        Route::delete('/kelas/{kelas}/materi/{materi}',          [KelasController::class, 'materiDestroy'])       ->name('kelas.materi.destroy');
        Route::patch ('/kelas/{kelas}/materi/{materi}/deadline', [KelasController::class, 'materiUpdateDeadline'])->name('kelas.materi.deadline');

        // Course CRUD  ← BARU
        Route::post  ('/kelas/{kelas}/course',          [KelasController::class, 'courseStore'])  ->name('kelas.course.store');
        Route::delete('/kelas/{kelas}/course/{course}', [KelasController::class, 'courseDestroy'])->name('kelas.course.destroy');

        // Penilaian siswa
        Route::post('/kelas/{kelas}/nilai/{siswa}', [KelasController::class, 'nilaiStore'])->name('kelas.nilai.store');

        // Jawaban siswa — download & preview  ← preview BARU
        Route::get('/kelas/{kelas}/jawaban/{jawaban}/unduh',   [KelasController::class, 'jawabanDownload'])->name('kelas.jawaban.download');
        Route::get('/kelas/{kelas}/jawaban/{jawaban}/preview', [KelasController::class, 'jawabanPreview']) ->name('kelas.jawaban.preview');
    });

    // ── KELAS: siswa ──────────────────────────────────────────────────────────
    Route::middleware('student.only')->group(function () {
        Route::get ('/join-kelas',           [KelasController::class, 'joinForm']) ->name('kelas.join');
        Route::post('/join-kelas',           [KelasController::class, 'join'])     ->name('kelas.join.post');
        Route::get ('/kelas/{kelas}/detail', [KelasController::class, 'showSiswa'])->name('kelas.siswa.show');
        Route::post('/leave-kelas/{kelas}',  [KelasController::class, 'leave'])    ->name('kelas.leave');

        // Upload jawaban per materi (dari halaman detail kelas)
        Route::post('/kelas/{kelas}/materi/{materi}/jawaban', [JawabanController::class, 'store'])->name('kelas.jawaban.store');

        // Download materi (siswa)
        Route::get('/kelas/{kelas}/materi/{materi}/unduh', [KelasController::class, 'materiDownload'])->name('kelas.materi.download');
    });

    // ── MATERI GLOBAL (halaman /materi) ───────────────────────────────────────
    Route::get ('/materi',                  [MateriController::class, 'index'])           ->name('materi');
    Route::get ('/materi/{materi}/download',[MateriController::class, 'download'])        ->name('materi.download');
    Route::post('/materi/{materi}/jawaban', [JawabanController::class, 'storeFromMateri'])->name('materi.jawaban.store');

    Route::middleware('role:admin,staff')->group(function () {
        Route::post  ('/materi/upload',        [MateriController::class, 'store'])  ->name('materi.upload');
        Route::get   ('/materi/{materi}/edit', [MateriController::class, 'edit'])   ->name('materi.edit');
        Route::put   ('/materi/{materi}',      [MateriController::class, 'update']) ->name('materi.update');
        Route::delete('/materi/{materi}',      [MateriController::class, 'destroy'])->name('materi.destroy');
    });

});

// ── FORGOT / RESET PASSWORD ───────────────────────────────────────────────────
Route::get ('/forgot-password',        [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password',        [ForgotPasswordController::class, 'sendResetLinkEmail']) ->name('password.email');
Route::get ('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])      ->name('password.reset');
Route::post('/reset-password',         [ForgotPasswordController::class, 'resetPassword'])      ->name('password.update');

// ── GOOGLE OAUTH ──────────────────────────────────────────────────────────────
Route::get('/auth/google/redirect', [SocialiteController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback']);
// ----------- GITHUB OAUTH -------------------------
Route::get('/auth/github', [GithubController::class, 'redirect'])->name('auth.github');
Route::get('/auth/github/callback', [GithubController::class, 'callback'])->name('auth.github.callback');