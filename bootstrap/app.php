<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'saved-game' => \App\Http\Middleware\CheckSavedGame::class,
            'check-ban' => \App\Http\Middleware\CheckBanStatus::class,
        ]);
        
        // Apply middleware to web routes
        $middleware->web(append: [
            \App\Http\Middleware\CheckSavedGame::class,
            \App\Http\Middleware\CheckBanStatus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
