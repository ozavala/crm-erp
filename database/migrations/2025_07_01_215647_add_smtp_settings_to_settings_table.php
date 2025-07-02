<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new settings for SMTP
        $smtpSettings = [
            ['key' => 'mail_mailer', 'value' => 'smtp'],
            ['key' => 'mail_host', 'value' => 'smtp.mailtrap.io'],
            ['key' => 'mail_port', 'value' => '2525'],
            ['key' => 'mail_username', 'value' => null],
            ['key' => 'mail_password', 'value' => null],
            ['key' => 'mail_encryption', 'value' => 'tls'],
            ['key' => 'mail_from_address', 'value' => 'hello@example.com'],
            ['key' => 'mail_from_name', 'value' => 'Example'],
        ];

        foreach ($smtpSettings as $setting) {
            DB::table('settings')->updateOrInsert(['key' => $setting['key']], ['value' => $setting['value']]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the SMTP settings
        $smtpKeys = [
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
        ];

        DB::table('settings')->whereIn('key', $smtpKeys)->delete();
    }
};
