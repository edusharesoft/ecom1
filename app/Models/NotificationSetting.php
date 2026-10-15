<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $table   = 'notification_settings';
    protected $guarded = [];
    protected $casts   = [
        'is_enabled'       => 'boolean',
        'show_real_orders' => 'boolean',
        'show_fake_orders' => 'boolean',
    ];

    public static function instance(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'is_enabled'       => 1,
            'show_real_orders' => 1,
            'show_fake_orders' => 1,
            'display_duration' => 5,
            'interval_min'     => 8,
            'interval_max'     => 15,
        ]);
    }
}
