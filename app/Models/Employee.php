<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'employee_id', 'name', 'email', 'phone', 'designation', 'department',
        'joining_date', 'basic_salary', 'address', 'nid', 'bank_name', 'bank_account',
        'status', 'notes', 'created_by'
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'basic_salary' => 'decimal:2',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($employee) {
            if (empty($employee->employee_id)) {
                $employee->employee_id = static::generateEmployeeId();
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances()
    {
        return $this->hasMany(EmployeeAttendance::class, 'employee_id');
    }

    public function leaves()
    {
        return $this->hasMany(EmployeeLeave::class, 'employee_id');
    }

    public function salaries()
    {
        return $this->hasMany(EmployeeSalary::class, 'employee_id');
    }

    public function bonuses()
    {
        return $this->hasMany(EmployeeBonus::class, 'employee_id');
    }

    public function salaryPayments()
    {
        return $this->hasMany(EmployeeSalaryPayment::class, 'employee_id');
    }

    // Helper methods
    public static function generateEmployeeId()
    {
        do {
            $employeeId = 'EMP-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (static::where('employee_id', $employeeId)->exists());
        return $employeeId;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
