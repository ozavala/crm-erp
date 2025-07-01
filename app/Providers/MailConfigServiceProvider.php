<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('settings')) {
            $settings = Setting::all()->pluck('value', 'key');

            if ($settings->has('mail_mailer')) {
                $config = [
                    'driver' => $settings->get('mail_mailer'),
                    'host' => $settings->get('mail_host'),
                    'port' => $settings->get('mail_port'),
                    'encryption' => $settings->get('mail_encryption'),
                    'username' => $settings->get('mail_username'),
                    'password' => $settings->get('mail_password'),
                    'from' => [
                        'address' => $settings->get('mail_from_address'),
                        'name' => $settings->get('mail_from_name'),
                    ],
                ];

                Config::set('mail', array_merge(Config::get('mail'), $config));
            }
        }
    }
}
