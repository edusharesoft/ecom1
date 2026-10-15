<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function childcategories() {
        return $this->hasMany(Childcategory::class, 'subcategory_id')->where('status', 1);
    }
    public function category() {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
    
    public function menuchildcategories()
    {
        return $this->hasMany(Childcategory::class, 'subcategory_id')->select('id','slug','subcategory_id','childcategoryName')->where('status',1);
    }
    
    
}
