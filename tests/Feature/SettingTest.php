<?php

use App\Services\SettingService;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('setting service retrieves configured values with dynamic defaults', function () {
    $settings = app(SettingService::class);

    // Verify initial fallbacks from settings configuration
    expect($settings->get('daily_request_limit'))->toBe('50');
    expect($settings->get('daily_word_limit'))->toBe('20000');
    expect($settings->get('site_title'))->toBe('QuillNova');

    // Update settings values
    $settings->set('site_title', 'QuillNova Advanced AI');
    
    // Retrieve updated key and assert correctness
    expect($settings->get('site_title'))->toBe('QuillNova Advanced AI');

    // Verify record exists in database
    $dbRecord = Setting::where('key', 'site_title')->first();
    expect($dbRecord)->not->toBeNull();
    expect($dbRecord->value)->toBe('QuillNova Advanced AI');
});
