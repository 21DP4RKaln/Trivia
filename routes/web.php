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

// Dashboard
Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth')->name('dashboard');

// Admin routes 
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
    Route::get('/questions', [AdminController::class, 'questions'])->name('questions');
    Route::get('/game-details/{gameSession}', [AdminController::class, 'gameDetails'])->name('game-details');
});

// Trivia game routes
Route::get('/', [TriviaController::class, 'index'])->name('trivia.index');
Route::post('/start', [TriviaController::class, 'start'])->name('trivia.start');
Route::get('/question', [TriviaController::class, 'nextQuestion'])->name('trivia.question');
Route::post('/answer', [TriviaController::class, 'submitAnswer'])->name('trivia.answer');
Route::post('/update-duration', [TriviaController::class, 'updateDuration'])->name('trivia.update-duration');
