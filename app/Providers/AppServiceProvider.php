<?php

namespace App\Providers;

use App\Models\Payment;
use App\Observers\PaymentObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
                    $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
                }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Payment::observe(PaymentObserver::class);
        // Only attempt to read settings if the table exists
        if (\Schema::hasTable('settings')) {
            $defaultLocale = Setting::where('key', 'default_locale')->value('value') ?? config('app.locale');
            app()->setLocale($defaultLocale);
        }
    }
}
