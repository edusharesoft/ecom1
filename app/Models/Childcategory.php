<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Childcategory extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function subcategory()
    {
        return $this->hasOne(Subcategory::class, 'id','subcategory_id');
    }

}
