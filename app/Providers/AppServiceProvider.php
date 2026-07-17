<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settings = app(\App\Services\SettingService::class)->all();
                config([
                    'mail.mailers.smtp.host' => $settings['smtp_host'] ?? config('mail.mailers.smtp.host'),
                    'mail.mailers.smtp.port' => $settings['smtp_port'] ?? config('mail.mailers.smtp.port'),
                    'mail.mailers.smtp.username' => $settings['smtp_username'] ?? config('mail.mailers.smtp.username'),
                    'mail.mailers.smtp.password' => $settings['smtp_password'] ?? config('mail.mailers.smtp.password'),
                    'mail.mailers.smtp.encryption' => $settings['smtp_encryption'] == 'none' ? null : ($settings['smtp_encryption'] ?? config('mail.mailers.smtp.encryption')),
                    'mail.from.address' => $settings['smtp_from_address'] ?? config('mail.from.address'),
                    'mail.from.name' => $settings['smtp_from_name'] ?? config('mail.from.name'),
                ]);
            }
        } catch (\Exception $e) {
            // Avoid failing during setup, migrations, or tests
        }
    }
}
