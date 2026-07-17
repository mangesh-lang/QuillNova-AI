<?php

namespace App\Services;

use App\Models\User;
use App\Models\DailyLimit;
use Carbon\Carbon;

class DailyLimitService
{
    protected SettingService $settings;

    public function __construct(SettingService $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get timezone for date comparison.
     */
    protected function getUserTimezone(User $user): string
    {
        return $user->profile->timezone ?? $this->settings->get('timezone', 'UTC');
    }

    /**
     * Get the current date in the user's timezone.
     */
    public function getTodayDate(User $user): string
    {
        $tz = $this->getUserTimezone($user);
        return Carbon::now($tz)->format('Y-m-d');
    }

    /**
     * Get or create the daily limit record for a user.
     */
    public function getOrCreateRecord(User $user): DailyLimit
    {
        $today = $this->getTodayDate($user);

        return DailyLimit::firstOrCreate(
            [
                'user_id' => $user->id,
                'date' => $today,
            ],
            [
                'request_count' => 0,
                'word_count' => 0,
            ]
        );
    }

    /**
     * Check if user has exceeded their daily limit.
     */
    public function hasExceededLimits(User $user, int $wordsToGenerate = 0): bool
    {
        // Super Admin bypasses limit check
        if ($user->isSuperAdmin()) {
            return false;
        }

        $record = $this->getOrCreateRecord($user);
        $requestLimit = (int) $this->settings->get('daily_request_limit', 50);
        $wordLimit = (int) $this->settings->get('daily_word_limit', 20000);

        if ($record->request_count >= $requestLimit) {
            return true;
        }

        if (($record->word_count + $wordsToGenerate) > $wordLimit) {
            return true;
        }

        return false;
    }

    /**
     * Record usage after successful content generation.
     */
    public function recordUsage(User $user, int $wordsGenerated): void
    {
        // Super Admin usage is recorded but doesn't block them
        $record = $this->getOrCreateRecord($user);
        $record->increment('request_count');
        $record->increment('word_count', max(0, $wordsGenerated));
    }

    /**
     * Reset the limit for a specific user (Admin capability).
     */
    public function resetUserLimit(User $user): void
    {
        $record = $this->getOrCreateRecord($user);
        $record->update([
            'request_count' => 0,
            'word_count' => 0,
        ]);
    }

    /**
     * Clear extremely old limit logs (e.g. older than 60 days) to keep DB performant.
     */
    public function cleanOldRecords(): void
    {
        DailyLimit::where('date', '<', Carbon::now()->subDays(60)->format('Y-m-d'))->delete();
    }
}
