<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    protected $fillable = [
        'version',
        'release_date',
        'changelog',
        'file_size',
        'file_path',
        'is_active',
        'requires_migration',
    ];

    protected $casts = [
        'release_date' => 'date',
        'is_active' => 'boolean',
        'requires_migration' => 'boolean',
        'file_size' => 'integer',
    ];
}
