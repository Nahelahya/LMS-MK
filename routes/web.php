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

// ─── WELCOME ──────────────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ─── GUEST ONLY ───────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// ─── OTP + LOGOUT ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/verify-otp',  [VerificationController::class, 'index'])->name('otp.index');
    Route::post('/verify-otp', [VerificationController::class, 'verify'])->name('otp.verify');
    Route::post('/logout',     [AuthController::class, 'logout'])->name('logout');
});

// ─── AUTHENTICATED + VERIFIED ─────────────────────────────────────────────────
Route::middleware(['auth', 'checkstatus'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Materi: semua bisa lihat & download
    Route::get('/materi',                   [MateriController::class, 'index'])->name('materi');
    Route::get('/materi/{materi}/download', [MateriController::class, 'download'])->name('materi.download');

    // Materi: admin & staff only
    Route::middleware('role:admin,staff')->group(function () {
        Route::post('/materi/upload',       [MateriController::class, 'store'])->name('materi.upload');
        Route::get('/materi/{materi}/edit', [MateriController::class, 'edit'])->name('materi.edit');
        Route::put('/materi/{materi}',      [MateriController::class, 'update'])->name('materi.update');
        Route::delete('/materi/{materi}',   [MateriController::class, 'destroy'])->name('materi.destroy');
    });

    // Jawaban
    Route::post('/tugas/upload', [JawabanController::class, 'store'])->name('tugas.upload');

    // ── KELAS: staff & admin ──
    Route::middleware('staff.only')->group(function () {
        Route::get('/kelas',            [KelasController::class, 'index'])->name('kelas.index');
        Route::get('/kelas/buat',       [KelasController::class, 'create'])->name('kelas.create');
        Route::post('/kelas',           [KelasController::class, 'store'])->name('kelas.store');
        Route::get('/kelas/{kelas}',    [KelasController::class, 'show'])->name('kelas.show');
        Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');
    });

    // ── KELAS: siswa ──
    Route::middleware('student.only')->group(function () {
        Route::get('/join-kelas',           [KelasController::class, 'joinForm'])->name('kelas.join');
        Route::post('/join-kelas',          [KelasController::class, 'join'])->name('kelas.join.post');
        Route::post('/leave-kelas/{kelas}', [KelasController::class, 'leave'])->name('kelas.leave');
    });

});

// ─── FORGOT / RESET PASSWORD ──────────────────────────────────────────────────
Route::get('forgot-password',        [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password',       [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password',        [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// ─── GOOGLE OAUTH ─────────────────────────────────────────────────────────────
Route::get('/auth/google/redirect', [SocialiteController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback']);
//  Kode UNIK KELAS
Route::patch('/kelas/{kelas}/kode', [KelasController::class, 'updateKode'])
    ->name('kelas.updateKode')
    ->middleware(['auth']);
 