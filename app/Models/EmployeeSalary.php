<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'salary_month', 'total_days', 'present_days', 'absent_days',
        'leave_days', 'working_days', 'basic_salary', 'allowance', 'deduction',
        'bonus', 'overtime', 'gross_salary', 'net_salary', 'status', 'notes',
        'calculated_by', 'calculated_at'
    ];

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'allowance' => 'decimal:2',
            'deduction' => 'decimal:2',
            'bonus' => 'decimal:2',
            'overtime' => 'decimal:2',
            'gross_salary' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'calculated_at' => 'datetime',
        ];
    }

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function calculatedBy()
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    public function salaryPayment()
    {
        return $this->hasOne(EmployeeSalaryPayment::class, 'salary_id');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCalculated()
    {
        return $this->status === 'calculated';
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

    public function scopeCalculated($query)
    {
        return $query->where('status', 'calculated');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
