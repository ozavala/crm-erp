<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // This is where you register middleware aliases.
        // The 'can' alias is now registered to your CheckPermission middleware.
        $middleware->alias([
            'can' => \App\Http\Middleware\CheckPermission::class,
            'setlocale' => \App\Http\Middleware\SetLocale::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'ownercompany' => \App\Http\Middleware\SetOwnerCompany::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\SetOwnerCompany::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // Run the appointment reminders command every 5 minutes
        $schedule->command('appointments:send-reminders')
                 ->everyFiveMinutes()
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/appointment-reminders.log'));
    })
    ->create();