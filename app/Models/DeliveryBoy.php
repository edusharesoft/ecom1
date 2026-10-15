<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class DeliveryBoy extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'delivery_boys';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'commission_per_delivery' => 'decimal:2',
            'monthly_salary_amount'  => 'decimal:2',
            'wallet_balance'         => 'decimal:2',
            'status'                 => 'integer',
        ];
    }

    public function assignedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_boy_id');
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(DeliveryBoyWalletTransaction::class, 'delivery_boy_id');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(DeliveryBoyWithdrawal::class, 'delivery_boy_id');
    }

    public function salaryPayments(): HasMany
    {
        return $this->hasMany(DeliveryBoySalaryPayment::class, 'delivery_boy_id');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        $path = $this->attributes['image'] ?? null;
        if (empty($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        $path = ltrim($path, '/');

        return asset(Str::startsWith($path, 'public/') ? $path : 'public/'.$path);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
