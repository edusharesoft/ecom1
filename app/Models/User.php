<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    // ✅ এটা যোগ করো
    protected $guard_name = 'admin';  // 👈 গুরুত্বপূর্ণ লাইন

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'image',
        'vendor_id',
        'shop_name',
        'role',
        'wallet_balance',
        'verification_status',
        'voter_id_front',
        'voter_id_back',
        'self_image',
        'verification_note',
        'verified_at',
    ];

    /**
     * Get the vendor that owns the user.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Get the employee record for this user.
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Laravel 12: Use casts() method instead of $casts property
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
