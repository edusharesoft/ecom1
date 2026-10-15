<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmployeeSalaryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'salary_id', 'payment_id', 'payment_month', 'amount',
        'payment_method', 'transaction_id', 'bank_name', 'account_number',
        'payment_date', 'notes', 'status', 'paid_by', 'paid_at'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($payment) {
            if (empty($payment->payment_id)) {
                $payment->payment_id = static::generatePaymentId();
            }
        });
    }

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function salary()
    {
        return $this->belongsTo(EmployeeSalary::class, 'salary_id');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    // Helper methods
    public static function generatePaymentId()
    {
        do {
            $paymentId = 'PAY-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (static::where('payment_id', $paymentId)->exists());
        return $paymentId;
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
