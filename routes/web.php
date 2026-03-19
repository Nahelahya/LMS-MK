<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    VerificationController,
    ForgotPasswordController,
    SocialiteController,
    DashboardController,
    MateriController,
    JawabanController,
    KelasController,
    AttendanceController,
    ProgressController,
    SettingsController,
    StudentController,
    ChatbotController,
};
use App\Http\Controllers\Auth\GithubController;

// ── WELCOME ─────────────────────────────
Route::get('/', fn() => view('welcome'));

// ── GUEST ───────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// ── AUTH BASIC (OTP + LOGOUT) ───────────
Route::middleware('auth')->group(function () {
    Route::get('/verify-otp', [VerificationController::class, 'index'])->name('otp.index');
    Route::post('/verify-otp', [VerificationController::class, 'verify'])->name('otp.verify');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
     Route::get('/users', [StudentController::class, 'index'])->name('students.index');
    Route::get('/users/{user}', [StudentController::class, 'show'])->name('students.show');
    Route::delete('/users/{user}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::middleware(['auth'])->group(function () {
    Route::post('/chatbot/chat', [ChatbotController::class, 'chat'])
         ->name('chatbot.chat');
});
});

// ── MAIN APP (AUTH + VERIFIED) ──────────
Route::middleware(['auth', 'checkstatus'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // PROGRESS
    Route::get('/progres', [ProgressController::class, 'index'])->name('progres.index');

    // SETTINGS
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/preferences', [SettingsController::class, 'updatePreferences'])->name('settings.preferences');

    // ── KELAS STAFF ─────────────────────
    Route::middleware('staff.only')->group(function () {

        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
        Route::get('/kelas/buat', [KelasController::class, 'create'])->name('kelas.create');
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
        Route::get('/kelas/{kelas}', [KelasController::class, 'show'])->name('kelas.show');
        Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');
        Route::patch('/kelas/{kelas}/kode', [KelasController::class, 'updateKode'])->name('kelas.updateKode');

        // Materi
        Route::post('/kelas/{kelas}/materi', [KelasController::class, 'materiStore'])->name('kelas.materi.store');
        Route::get('/kelas/{kelas}/materi/{materi}/unduh', [KelasController::class, 'materiDownload'])->name('kelas.materi.download');
        Route::delete('/kelas/{kelas}/materi/{materi}', [KelasController::class, 'materiDestroy'])->name('kelas.materi.destroy');

        // Course
        Route::post('/kelas/{kelas}/course', [KelasController::class, 'courseStore'])->name('kelas.course.store');
        Route::delete('/kelas/{kelas}/course/{course}', [KelasController::class, 'courseDestroy'])->name('kelas.course.destroy');

        // Nilai
        Route::post('/kelas/{kelas}/nilai/{siswa}', [KelasController::class, 'nilaiStore'])->name('kelas.nilai.store');
    });

    // ── KELAS SISWA ─────────────────────
    Route::middleware('student.only')->group(function () {
        Route::get('/join-kelas', [KelasController::class, 'joinForm'])->name('kelas.join');
        Route::post('/join-kelas', [KelasController::class, 'join'])->name('kelas.join.post');
        Route::get('/kelas/{kelas}/detail', [KelasController::class, 'showSiswa'])->name('kelas.siswa.show');
        Route::post('/leave-kelas/{kelas}', [KelasController::class, 'leave'])->name('kelas.leave');

        Route::post('/kelas/{kelas}/materi/{materi}/jawaban', [JawabanController::class, 'store'])->name('kelas.jawaban.store');
        Route::get('/kelas/{kelas}/materi/{materi}/unduh', [KelasController::class, 'materiDownload'])->name('kelas.materi.download');
    });

    // ── MATERI ──────────────────────────
    Route::get('/materi', [MateriController::class, 'index'])->name('materi');
    Route::get('/materi/{materi}/download', [MateriController::class, 'download'])->name('materi.download');
    Route::post('/materi/{materi}/jawaban', [JawabanController::class, 'storeFromMateri'])->name('materi.jawaban.store');

    Route::middleware('role:admin,staff')->group(function () {
        Route::post('/materi/upload', [MateriController::class, 'store'])->name('materi.upload');
        Route::get('/materi/{materi}/edit', [MateriController::class, 'edit'])->name('materi.edit');
        Route::put('/materi/{materi}', [MateriController::class, 'update'])->name('materi.update');
        Route::delete('/materi/{materi}', [MateriController::class, 'destroy'])->name('materi.destroy');
    });
});

// ── PASSWORD RESET ──────────────────────
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// ── OAUTH ──────────────────────────────
Route::get('/auth/google/redirect', [SocialiteController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback']);

Route::get('/auth/github', [GithubController::class, 'redirect'])->name('auth.github');
Route::get('/auth/github/callback', [GithubController::class, 'callback'])->name('auth.github.callback');

// ── PRESENSI ───────────────────────────
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/presensi', [AttendanceController::class, 'index'])->name('presensi.index');
    Route::post('/presensi', [AttendanceController::class, 'store'])->name('presensi.store');
});

Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/admin/presensi', [AttendanceController::class, 'adminIndex'])->name('admin.presensi');
});

Route::middleware(['auth'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/',             [SettingsController::class, 'index'])             ->name('index');
    Route::post('/profile',     [SettingsController::class, 'updateProfile'])     ->name('profile');
    Route::post('/password',    [SettingsController::class, 'updatePassword'])    ->name('password');
    Route::post('/preferences', [SettingsController::class, 'updatePreferences']) ->name('preferences');
    Route::delete('/photo',     [SettingsController::class, 'deletePhoto'])       ->name('photo.delete');
});