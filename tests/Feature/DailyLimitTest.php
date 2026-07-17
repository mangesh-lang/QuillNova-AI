<?php

use App\Models\User;
use App\Models\DailyLimit;
use App\Services\DailyLimitService;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('daily limit service can check and record user AI usage', function () {
    // 1. Arrange
    $user = User::factory()->create([
        'name' => 'Limit Tester',
        'email' => 'tester@quillnova.com',
        'status' => 'active',
    ]);
    
    $settings = app(SettingService::class);
    $settings->set('daily_request_limit', '5');
    $settings->set('daily_word_limit', '1000');

    $limitService = app(DailyLimitService::class);

    // 2. Act & Assert: Check initial limit status
    expect($limitService->hasExceededLimits($user))->toBeFalse();

    // Increment request count by recording usage
    $limitService->recordUsage($user, 200); // 1st request, 200 words
    $record = $limitService->getOrCreateRecord($user);
    
    expect($record->request_count)->toBe(1);
    expect($record->word_count)->toBe(200);
    expect($limitService->hasExceededLimits($user))->toBeFalse();

    // Trigger request limit exhaustion
    $limitService->recordUsage($user, 200); // 2nd
    $limitService->recordUsage($user, 200); // 3rd
    $limitService->recordUsage($user, 200); // 4th
    $limitService->recordUsage($user, 100); // 5th request, total 900 words
    
    expect($limitService->hasExceededLimits($user))->toBeTrue(); // Limit was set to 5 requests, now 5 requests made

    // Reset limit
    $limitService->resetUserLimit($user);
    expect($limitService->hasExceededLimits($user))->toBeFalse();
});
