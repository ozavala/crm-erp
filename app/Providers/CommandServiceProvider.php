<?php

namespace App\Providers;

use App\Console\Commands\SendAppointmentReminders;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('commands.appointments.send-reminders', function ($app) {
            return new SendAppointmentReminders();
        });

        $this->commands([
            'commands.appointments.send-reminders',
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

}