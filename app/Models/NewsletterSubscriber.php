<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $table = 'newsletter_subscribers';

    protected $fillable = [
        'email',
        'status',
    ];
}
