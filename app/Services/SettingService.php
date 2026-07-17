<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    protected const CACHE_KEY = 'system_settings';

    /**
     * Get a setting by key, with dynamic fallback.
     */
    public function get(string $key, $default = null)
    {
        $settings = $this->all();
        return $settings[$key] ?? $default;
    }

    /**
     * Set/update a setting value.
     */
    public function set(string $key, ?string $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        $this->clearCache();
    }

    /**
     * Get all settings as key-value pairs (cached).
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $defaults = [
                'openai_key' => '',
                'gemini_key' => '',
                'daily_request_limit' => '50',
                'daily_word_limit' => '20000',
                'site_title' => 'QuillNova',
                'logo' => '',
                'favicon' => '',
                'smtp_host' => '127.0.0.1',
                'smtp_port' => '2525',
                'smtp_username' => '',
                'smtp_password' => '',
                'smtp_encryption' => 'tls',
                'smtp_from_address' => 'hello@quillnova.com',
                'smtp_from_name' => 'QuillNova',
                'timezone' => 'UTC',
                'language' => 'en',
                'default_ai_provider' => 'openai',
                'maintenance_mode' => '0',
            ];

            $dbSettings = Setting::all()->pluck('value', 'key')->toArray();

            return array_merge($defaults, $dbSettings);
        });
    }

    /**
     * Clear cached settings.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
