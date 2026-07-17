<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    protected SettingService $settings;

    public function __construct(SettingService $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Show System Settings form.
     */
    public function index()
    {
        $configs = $this->settings->all();
        $timezones = \DateTimeZone::listIdentifiers();
        return view('admin.settings.index', compact('configs', 'timezones'));
    }

    /**
     * Update System Settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'openai_key' => 'nullable|string',
            'gemini_key' => 'nullable|string',
            'daily_request_limit' => 'required|integer|min:1',
            'daily_word_limit' => 'required|integer|min:1',
            'site_title' => 'required|string|max:255',
            'default_ai_provider' => 'required|in:openai,gemini',
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'nullable|string',
            'smtp_password' => 'nullable|string',
            'smtp_encryption' => 'required|string|in:ssl,tls,none',
            'smtp_from_address' => 'required|email',
            'smtp_from_name' => 'required|string',
            'timezone' => 'required|string',
            'language' => 'required|string|max:10',
            'maintenance_mode' => 'required|boolean',
        ]);

        $inputs = $request->only([
            'openai_key',
            'gemini_key',
            'daily_request_limit',
            'daily_word_limit',
            'site_title',
            'default_ai_provider',
            'smtp_host',
            'smtp_port',
            'smtp_username',
            'smtp_password',
            'smtp_encryption',
            'smtp_from_address',
            'smtp_from_name',
            'timezone',
            'language',
            'maintenance_mode',
        ]);

        foreach ($inputs as $key => $value) {
            $this->settings->set($key, $value);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_settings_update',
            'details' => 'Admin updated system settings.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'System settings updated successfully!');
    }
}
