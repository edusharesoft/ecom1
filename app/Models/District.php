<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    /** Legacy flat area/district rows (pre–delivery divisions); see migration 2026_05_19_100001. */
    protected $table = 'legacy_district_areas';
}
