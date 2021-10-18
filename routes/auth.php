<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/**
 * Auth routes
 */
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->name('register');
});

Route::middleware('auth')->group(function () {
    Route::get('register/completed', [AuthController::class, 'showRegisterCompleted'])->name('register.completed');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('email/resend-verification', [AuthController::class, 'resendEmailVerification'])->name('verification.resend');
});

Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.forgot');
Route::post('forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.forgot');
Route::get('reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
