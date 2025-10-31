<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\UpdateLastActivity;
use App\Http\Middleware\EnsureEmailIsVerifiedApi;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'verified' => \App\Http\Middleware\EnsureEmailIsVerifiedApi::class,
        'last_activity' => \App\Http\Middleware\UpdateLastActivity::class,
    ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('lawyers:mark-inactive')->everyMinute();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();