<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookPageSetting extends Model
{
    protected $fillable = [
        'page_id',
        'page_access_token',
        'page_name',
        'auto_post_new_products',
        'post_template',
    ];

    protected $casts = [
        'auto_post_new_products' => 'boolean',
    ];

    public static function firstOrCreate(): self
    {
        return static::first() ?? static::create([]);
    }

    public function isConfigured(): bool
    {
        return !empty($this->page_id) && !empty($this->page_access_token);
    }
}
