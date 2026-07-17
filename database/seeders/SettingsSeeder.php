<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'openai_key' => env('OPENAI_API_KEY', ''),
            'gemini_key' => env('GEMINI_API_KEY', ''),
            'daily_request_limit' => '50',
            'daily_word_limit' => '20000',
            'site_title' => 'QuillNova',
            'logo' => '',
            'favicon' => '',
            'smtp_host' => env('MAIL_HOST', '127.0.0.1'),
            'smtp_port' => env('MAIL_PORT', '2525'),
            'smtp_username' => env('MAIL_USERNAME', ''),
            'smtp_password' => env('MAIL_PASSWORD', ''),
            'smtp_encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'smtp_from_address' => env('MAIL_FROM_ADDRESS', 'hello@quillnova.com'),
            'smtp_from_name' => env('MAIL_FROM_NAME', 'QuillNova'),
            'timezone' => 'UTC',
            'language' => 'en',
            'default_ai_provider' => 'openai',
            'maintenance_mode' => '0',
        ];

        foreach ($settings as $key => $value) {
            Setting::firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
