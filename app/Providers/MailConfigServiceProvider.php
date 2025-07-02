<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
            try {
                $settings = DB::table('settings')
                    ->whereIn('key', [
                        'mail_mailer',
                        'mail_host',
                        'mail_port',
                        'mail_username',
                        'mail_password',
                        'mail_encryption',
                        'mail_from_address',
                        'mail_from_name',
                    ])
                    ->pluck('value', 'key');

                if ($settings->has('mail_host') && $settings->get('mail_host')) {
                    $config = config('mail');

                    $config['mailers']['smtp'] = array_merge($config['mailers']['smtp'] ?? [], [
                        'transport' => $settings->get('mail_mailer', 'smtp'),
                        'host' => $settings->get('mail_host'),
                        'port' => $settings->get('mail_port'),
                        'encryption' => $settings->get('mail_encryption'),
                        'username' => $settings->get('mail_username'),
                        'password' => $settings->get('mail_password'),
                    ]);

                    $config['from']['address'] = $settings->get('mail_from_address');
                    $config['from']['name'] = $settings->get('mail_from_name');

                    Config::set('mail', $config);
                }
            } catch (\Exception $e) {
                // Fails silently if the database is not available (e.g., during migrations)
            }
        }
    }
}
