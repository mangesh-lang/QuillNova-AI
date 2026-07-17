@extends('layouts.app')
@section('title', 'System Settings')

@section('content')
<div class="page-header">
    <h1 class="page-title">System Settings</h1>
    <p class="page-subtitle">Configure OpenAI/Gemini API keys, daily free usage limits, site title, timezone, and SMTP parameters.</p>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf

    <div class="row g-4">
        <!-- AI API Configuration & Limits Column -->
        <div class="col-12 col-xl-6">
            <!-- AI APIs Settings -->
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-cpu text-primary me-2"></i> AI API Integrations</h5>
                <hr class="border-custom mb-4">

                <div class="mb-3">
                    <label for="openai_key" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">OpenAI API Key</label>
                    <input type="password" id="openai_key" name="openai_key" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" value="{{ $configs['openai_key'] ?? '' }}" placeholder="sk-...">
                </div>

                <div class="mb-3">
                    <label for="gemini_key" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">Google Gemini API Key</label>
                    <input type="password" id="gemini_key" name="gemini_key" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" value="{{ $configs['gemini_key'] ?? '' }}" placeholder="AIzaSy...">
                </div>

                <div class="mb-0">
                    <label for="default_ai_provider" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">Default AI Provider</label>
                    <select id="default_ai_provider" name="default_ai_provider" class="form-select text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;">
                        <option value="openai" {{ ($configs['default_ai_provider'] ?? 'openai') === 'openai' ? 'selected' : '' }}>OpenAI (gpt-4o-mini)</option>
                        <option value="gemini" {{ ($configs['default_ai_provider'] ?? 'openai') === 'gemini' ? 'selected' : '' }}>Google Gemini (gemini-2.0-flash)</option>
                    </select>
                </div>
            </div>

            <!-- Daily Free Limit Configuration -->
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-bar-chart-steps text-success me-2"></i> Free Daily Usage Limits</h5>
                <hr class="border-custom mb-4">

                <div class="row g-3 mb-0">
                    <div class="col-12 col-md-6">
                        <label for="daily_request_limit" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">Daily AI Request Limit</label>
                        <input type="number" id="daily_request_limit" name="daily_request_limit" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" value="{{ $configs['daily_request_limit'] ?? 50 }}" required>
                        <div class="form-text text-muted-custom" style="font-size: 0.75rem;">Number of AI generations allowed per user per day.</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="daily_word_limit" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">Daily Generated Word Limit</label>
                        <input type="number" id="daily_word_limit" name="daily_word_limit" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" value="{{ $configs['daily_word_limit'] ?? 20000 }}" required>
                        <div class="form-text text-muted-custom" style="font-size: 0.75rem;">Number of words allowed to be generated per user per day.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Site Identity & SMTP Settings Column -->
        <div class="col-12 col-xl-6">
            <!-- System Identity Configurations -->
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-browser-chrome text-primary me-2"></i> System Identity & General</h5>
                <hr class="border-custom mb-4">

                <div class="mb-3">
                    <label for="site_title" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">Site Title / Name</label>
                    <input type="text" id="site_title" name="site_title" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" value="{{ $configs['site_title'] ?? 'QuillNova' }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="timezone" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">Timezone</label>
                        <select id="timezone" name="timezone" class="form-select text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;">
                            @foreach($timezones as $tz)
                                <option value="{{ $tz }}" {{ ($configs['timezone'] ?? 'UTC') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="language" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">System Language</label>
                        <select id="language" name="language" class="form-select text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;">
                            <option value="en" {{ ($configs['language'] ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ ($configs['language'] ?? 'en') === 'es' ? 'selected' : '' }}>Spanish</option>
                            <option value="fr" {{ ($configs['language'] ?? 'en') === 'fr' ? 'selected' : '' }}>French</option>
                            <option value="de" {{ ($configs['language'] ?? 'en') === 'de' ? 'selected' : '' }}>German</option>
                        </select>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">Maintenance Mode</label>
                    <select name="maintenance_mode" class="form-select text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;">
                        <option value="0" {{ ($configs['maintenance_mode'] ?? '0') === '0' ? 'selected' : '' }}>Disabled (Site online)</option>
                        <option value="1" {{ ($configs['maintenance_mode'] ?? '0') === '1' ? 'selected' : '' }}>Enabled (Site under maintenance)</option>
                    </select>
                </div>
            </div>

            <!-- SMTP Settings Card -->
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-mailbox text-warning me-2"></i> Mail Configuration (SMTP)</h5>
                <hr class="border-custom mb-4">

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-8">
                        <label for="smtp_host" class="form-label fw-semibold text-primary" style="font-size: 0.85rem;">SMTP Host</label>
                        <input type="text" id="smtp_host" name="smtp_host" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" value="{{ $configs['smtp_host'] ?? '127.0.0.1' }}" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="smtp_port" class="form-label fw-semibold text-primary" style="font-size: 0.85rem;">SMTP Port</label>
                        <input type="number" id="smtp_port" name="smtp_port" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" value="{{ $configs['smtp_port'] ?? 2525 }}" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="smtp_username" class="form-label fw-semibold text-primary" style="font-size: 0.85rem;">SMTP Username</label>
                        <input type="text" id="smtp_username" name="smtp_username" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" value="{{ $configs['smtp_username'] ?? '' }}">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="smtp_password" class="form-label fw-semibold text-primary" style="font-size: 0.85rem;">SMTP Password</label>
                        <input type="password" id="smtp_password" name="smtp_password" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" value="{{ $configs['smtp_password'] ?? '' }}">
                    </div>
                </div>

                <div class="row g-3 mb-0">
                    <div class="col-12 col-md-4">
                        <label for="smtp_encryption" class="form-label fw-semibold text-primary" style="font-size: 0.85rem;">SMTP Encryption</label>
                        <select id="smtp_encryption" name="smtp_encryption" class="form-select text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;">
                            <option value="tls" {{ ($configs['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ ($configs['smtp_encryption'] ?? 'tls') === 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="none" {{ ($configs['smtp_encryption'] ?? 'tls') === 'none' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-8">
                        <label for="smtp_from_address" class="form-label fw-semibold text-primary" style="font-size: 0.85rem;">Sender Email (From)</label>
                        <input type="email" id="smtp_from_address" name="smtp_from_address" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" value="{{ $configs['smtp_from_address'] ?? 'hello@quillnova.com' }}" required>
                    </div>
                </div>

                <div class="mb-0 mt-3">
                    <label for="smtp_from_name" class="form-label fw-semibold text-primary" style="font-size: 0.85rem;">Sender Name (From Name)</label>
                    <input type="text" id="smtp_from_name" name="smtp_from_name" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" value="{{ $configs['smtp_from_name'] ?? 'QuillNova' }}" required>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky footer saving bar -->
    <div class="glass-card p-3 mt-4 d-flex justify-content-end align-items-center gap-3">
        <button type="submit" class="btn btn-primary px-4 fw-semibold py-2.5" style="background-color: var(--accent-primary); border: none;">Save Configurations</button>
    </div>
</form>
@endsection
