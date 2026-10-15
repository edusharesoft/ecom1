<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    // টেবিলের নাম (safe side)
    protected $table = 'contact_messages';

    // Mass assignment এর জন্য অনুমোদিত ফিল্ড
    protected $fillable = [
        'full_name',
        'mobile',
        'email',
        'subject',
        'details',
        'status',
    ];

    // যদি তোমার টেবিলে created_at / updated_at না থাকে
    // তাহলে এই লাইনটা খুলে দাও
    // public $timestamps = false;
}
