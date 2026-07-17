<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\GeneratedContent;
use App\Models\DailyLimit;
use App\Services\DailyLimitService;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected DailyLimitService $limitService;
    protected SettingService $settings;

    public function __construct(DailyLimitService $limitService, SettingService $settings)
    {
        $this->limitService = $limitService;
        $this->settings = $settings;
    }

    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Retrieve limits from settings
        $maxRequests = (int) $this->settings->get('daily_request_limit', 50);
        $maxWords = (int) $this->settings->get('daily_word_limit', 20000);

        // 2. Fetch today's usage row
        $usage = $this->limitService->getOrCreateRecord($user);
        
        $todayRequests = $usage->request_count;
        $todayWords = $usage->word_count;

        // 3. Compute remaining limits
        $remainingRequests = max(0, $maxRequests - $todayRequests);
        $remainingWords = max(0, $maxWords - $todayWords);

        // 4. Fetch recent generation history (limit 5)
        $recentHistory = GeneratedContent::where('user_id', $user->id)
            ->with('template')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 5. Count total generation records
        $totalGeneratedCount = GeneratedContent::where('user_id', $user->id)->count();

        // 6. Fetch favorite templates or default popular templates
        $favoriteTemplates = Template::where('is_active', true)
            ->whereIn('slug', ['blog-generator', 'email-writer', 'code-generator', 'instagram-caption'])
            ->get();

        // 7. Get 7-day usage chart data
        $chartData = $this->getWeeklyChartData($user);

        return view('dashboard.index', compact(
            'todayRequests',
            'todayWords',
            'maxRequests',
            'maxWords',
            'remainingRequests',
            'remainingWords',
            'recentHistory',
            'totalGeneratedCount',
            'favoriteTemplates',
            'chartData'
        ));
    }

    /**
     * Get weekly usage analytics for the user.
     */
    protected function getWeeklyChartData($user): array
    {
        $timezone = $user->profile->timezone ?? $this->settings->get('timezone', 'UTC');
        $labels = [];
        $words = [];
        $requests = [];

        // Generate past 7 days dates
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now($timezone)->subDays($i);
            $labels[] = $date->format('M d');
            
            // Find usage for this day
            $log = DailyLimit::where('user_id', $user->id)
                ->where('date', $date->format('Y-m-d'))
                ->first();

            $words[] = $log ? $log->word_count : 0;
            $requests[] = $log ? $log->request_count : 0;
        }

        return [
            'labels' => $labels,
            'words' => $words,
            'requests' => $requests,
        ];
    }
}
