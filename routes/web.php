<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\JawabanController;

Route::get('/', function () {
    return view('welcome');
});

// --- RUTE GUEST (Belum Login) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// --- RUTE VERIFIKASI OTP ---
// User harus login tapi statusnya masih 'verify'
Route::middleware(['auth'])->group(function () {
    Route::get('/verify-otp', [VerificationController::class, 'index'])->name('otp.index');
    Route::post('/verify-otp', [VerificationController::class, 'verify'])->name('otp.verify');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'checkstatus'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/materi', [MateriController::class,'index'])->name('materi');

    Route::post('/materi/upload', [MateriController::class,'store'])->name('materi.upload');

    Route::post('/tugas/upload',[JawabanController::class,'store']);

});

// Form minta link
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Form input password baru
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// Route untuk lempar ke Google
Route::get('/auth/google/redirect', [SocialiteController::class, 'redirect'])->name('google.redirect');

// Route untuk terima data balik dari Google
Route::get('/auth/google/callback', [SocialiteController::class, 'callback']);



Route::get('/materi',[MateriController::class,'index']);
Route::post('/materi/upload',[MateriController::class,'store'])->name('materi.upload');;

Route::post('/tugas/upload',[JawabanController::class,'store']);