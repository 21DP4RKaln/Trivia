<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TriviaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Email Verification Routes
Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');
Route::get('/email/verify/check-status', [AuthController::class, 'checkVerificationStatus'])->middleware('auth');

// Terms of Service
Route::get('/terms-of-service', [AuthController::class, 'showTermsOfService'])->name('terms.service');
Route::get('/terms-of-service/check-updates', [AuthController::class, 'checkTermsUpdates'])->name('terms.check-updates');

// Dashboard
Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes 
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::post('/users/{user}/send-verification', [AdminController::class, 'sendVerificationEmail'])->name('users.send-verification');
    Route::get('/users/{user}/ban', [AdminController::class, 'showBanForm'])->name('users.ban-form');
    Route::post('/users/{user}/ban', [AdminController::class, 'banUser'])->name('users.ban');
    Route::post('/users/{user}/unban', [AdminController::class, 'unbanUser'])->name('users.unban');
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
    Route::get('/questions', [AdminController::class, 'questions'])->name('questions');
    Route::get('/terms-of-service', [AdminController::class, 'termsOfService'])->name('terms-of-service');
    Route::post('/terms-of-service', [AdminController::class, 'updateTermsOfService'])->name('terms-of-service.update');
    Route::get('/terms-of-service/analytics', [AdminController::class, 'getTermsAnalytics'])->name('terms-of-service.analytics');
    Route::get('/terms-of-service/history/{id}', [AdminController::class, 'getTermsHistory'])->name('terms-of-service.history');
    Route::get('/terms-of-service/export', [AdminController::class, 'exportTerms'])->name('terms-of-service.export');
    Route::get('/game-details/{gameSession}', [AdminController::class, 'gameDetails'])->name('game-details');
});

// Trivia game routes
Route::get('/', [TriviaController::class, 'index'])->name('trivia.index');
Route::get('/loader-demo', function () {
    return view('trivia.loader-demo');
})->name('trivia.loader-demo');
Route::post('/start', [TriviaController::class, 'start'])->name('trivia.start');
Route::post('/continue', [TriviaController::class, 'continueGame'])->name('trivia.continue');
Route::post('/abandon', [TriviaController::class, 'abandonGame'])->name('trivia.abandon');
Route::get('/question', [TriviaController::class, 'nextQuestion'])->name('trivia.question');
Route::post('/answer', [TriviaController::class, 'submitAnswer'])->name('trivia.answer');
Route::post('/update-duration', [TriviaController::class, 'updateDuration'])->name('trivia.update-duration');
