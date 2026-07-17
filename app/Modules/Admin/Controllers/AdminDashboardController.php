<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\GeneratedContent;
use App\Models\DailyLimit;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Show Super Admin analytics dashboard.
     */
    public function index()
    {
        // 1. Core counters
        $totalUsers = User::count();
        $suspendedUsers = User::where('status', 'suspended')->count();
        $totalWords = (int) GeneratedContent::sum('word_count');
        
        // Count generated contents + chat messages as total requests
        $totalRequests = GeneratedContent::count() + DB::table('chat_messages')->count();

        // 2. Active users today
        $todayStr = Carbon::today()->format('Y-m-d');
        $activeUsersToday = DailyLimit::where('date', $todayStr)
            ->where(function($q) {
                $q->where('request_count', '>', 0)
                  ->orWhere('word_count', '>', 0);
            })
            ->distinct('user_id')
            ->count();

        // 3. Top Users by word generation (limit 5)
        $topUsers = User::select('users.id', 'users.name', 'users.email')
            ->join('generated_contents', 'users.id', '=', 'generated_contents.user_id')
            ->selectRaw('SUM(generated_contents.word_count) as total_words')
            ->selectRaw('COUNT(generated_contents.id) as total_generations')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_words', 'desc')
            ->limit(5)
            ->get();

        // 4. Usage chart data (last 15 days)
        $analytics = $this->getSystemAnalyticsData();

        return view('admin.dashboard', compact(
            'totalUsers',
            'suspendedUsers',
            'totalWords',
            'totalRequests',
            'activeUsersToday',
            'topUsers',
            'analytics'
        ));
    }

    /**
     * Show audit activity logs.
     */
    public function logs(Request $request)
    {
        $search = $request->query('search', '');
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if (!empty($search)) {
            $query->where('action', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
        }

        $logs = $query->paginate(15);

        return view('admin.logs', compact('logs', 'search'));
    }

    /**
     * Compile past 15 days of system usage statistics.
     */
    protected function getSystemAnalyticsData(): array
    {
        $labels = [];
        $words = [];
        $requests = [];

        for ($i = 14; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M d');
            $dateStr = $date->format('Y-m-d');

            // Sum limits for all users on this day
            $log = DailyLimit::where('date', $dateStr)
                ->selectRaw('SUM(word_count) as total_words, SUM(request_count) as total_requests')
                ->first();

            $words[] = (int) ($log->total_words ?? 0);
            $requests[] = (int) ($log->total_requests ?? 0);
        }

        return [
            'labels' => $labels,
            'words' => $words,
            'requests' => $requests,
        ];
    }
}
