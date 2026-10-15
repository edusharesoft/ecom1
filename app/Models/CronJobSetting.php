<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CronJobSetting extends Model
{
    protected $table = 'cron_job_settings';

    protected $fillable = [
        'job_key', 'job_title', 'job_description',
        'is_enabled', 'frequency_minutes', 'order_limit',
        'last_run_at', 'last_run_status', 'last_run_result',
        'last_updated_count', 'last_failed_count',
    ];

    protected $casts = [
        'is_enabled'  => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public static function forKey(string $key): ?self
    {
        return static::where('job_key', $key)->first();
    }
}
