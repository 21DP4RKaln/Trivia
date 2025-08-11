<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TriviaController;

Route::get('/', [TriviaController::class, 'index'])->name('trivia.index');
Route::post('/start', [TriviaController::class, 'start'])->name('trivia.start');
Route::get('/question', [TriviaController::class, 'nextQuestion'])->name('trivia.question');
Route::post('/answer', [TriviaController::class, 'submitAnswer'])->name('trivia.answer');
