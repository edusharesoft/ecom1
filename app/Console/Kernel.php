<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\CronJobSetting;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Courier status sync — frequency controlled from admin panel
        try {
            $setting = CronJobSetting::forKey('courier_status_sync');
        } catch (\Throwable $e) {
            // Table might not exist yet (fresh install), fall back to default
            $setting = null;
        }

        $enabled   = $setting ? $setting->is_enabled        : true;
        $frequency = $setting ? (int) $setting->frequency_minutes : 10;
        $limit     = $setting ? (int) $setting->order_limit       : 50;

        if ($enabled) {
            $job = $schedule->command("courier:check-status --limit={$limit}")
                ->withoutOverlapping()
                ->runInBackground();

            match (true) {
                $frequency <= 1  => $job->everyMinute(),
                $frequency <= 2  => $job->everyTwoMinutes(),
                $frequency <= 5  => $job->everyFiveMinutes(),
                $frequency <= 10 => $job->everyTenMinutes(),
                $frequency <= 15 => $job->everyFifteenMinutes(),
                $frequency <= 30 => $job->everyThirtyMinutes(),
                $frequency <= 60 => $job->hourly(),
                default          => $job->everyTwoHours(),
            };
        }
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
