<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VillaController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureEmailIsVerified;

/*
|--------------------------------------------------------------------------
| Web Routes - Perbaikan KawanPuncak.com
|--------------------------------------------------------------------------
*/

// --- 1. PUBLIK (Bisa diakses siapa saja) ---
Route::get('/', [HomeController::class, 'index'])->name('home');

// Villa Routes (Daftar & Detail)
Route::get('/villas', [VillaController::class, 'index'])->name('villas.index');
Route::get('/villas/{villa:slug}', [VillaController::class, 'show'])->name('villas.show');

// Redirect login agar konsisten
Route::redirect('/admin/login', '/login');
Route::redirect('/user/login', '/login');


// --- 2. GUEST (Hanya untuk yang BELUM login) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});


// --- 3. AUTH (Hanya untuk yang SUDAH login) ---
Route::middleware('auth')->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Email Verification Routes (Bawaan)
    Route::get('/email/verify', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware('signed')->name('verification.verify');
    Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail'])->middleware('throttle:6,1')->name('verification.send');

    // --- 4. VERIFIED (Hanya untuk yang sudah login & sudah verifikasi email) ---
    Route::middleware([EnsureEmailIsVerified::class])->group(function () {
        
        // 📅 Booking
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/booking/{id}', [BookingController::class, 'show'])->name('bookings.show');

        // 💳 Payment (Grup ganda dihapus, digabung di sini)
        Route::get('/payment/booking/{booking_id}', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payment/{id}', [PaymentController::class, 'show'])->name('payments.show');

        // 📊 Dashboard & Invoice
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::get('/invoice/{id}', [InvoiceController::class, 'show'])->name('invoice.show');

        // 👤 Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    });
});