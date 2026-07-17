<?php

namespace App\Console\Commands;

use App\Services\DailyLimitService;
use Illuminate\Console\Command;

class ResetDailyLimits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'limits:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up daily limit usage log entries older than 60 days';

    /**
     * Execute the console command.
     */
    public function handle(DailyLimitService $limitService)
    {
        $this->info('Cleaning old daily limit logs...');
        $limitService->cleanOldRecords();
        $this->info('Daily limits cleanup completed successfully!');
    }
}
