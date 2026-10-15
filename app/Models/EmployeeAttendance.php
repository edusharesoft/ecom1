<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'attendance_date', 'check_in', 'check_out', 'status', 'notes', 'marked_by'
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'check_in' => 'datetime',
            'check_out' => 'datetime',
        ];
    }

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    // Helper methods
    public function isPresent()
    {
        return $this->status === 'present';
    }

    public function isAbsent()
    {
        return $this->status === 'absent';
    }

    public function isLate()
    {
        return $this->status === 'late';
    }

    // Scopes
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month);
    }
}
