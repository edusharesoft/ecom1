<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Toastr;
use Carbon\Carbon;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EmployeeSalary::with('employee', 'calculatedBy')->orderBy('salary_month', 'DESC');

        // Filter by employee
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by month
        if ($request->month) {
            $query->where('salary_month', $request->month);
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $salaries = $query->paginate(20);
        $employees = Employee::where('status', 'active')->orderBy('name')->get();

        return view('backEnd.salaries.index', compact('salaries', 'employees'));
    }

    /**
     * Calculate salary for an employee for a specific month
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary_month' => 'required|date_format:Y-m',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $year = Carbon::parse($request->salary_month)->year;
        $month = Carbon::parse($request->salary_month)->month;

        // Check if already calculated
        $existing = EmployeeSalary::where('employee_id', $employee->id)
            ->where('salary_month', $request->salary_month)
            ->first();

        if ($existing && $existing->status !== 'pending') {
            Toastr::error('Salary already calculated for this month!');
            return back();
        }

        // Get attendance data
        $attendances = EmployeeAttendance::where('employee_id', $employee->id)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->get();

        $totalDays = Carbon::create($year, $month, 1)->daysInMonth;
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays = $attendances->where('status', 'absent')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $halfDays = $attendances->where('status', 'half_day')->count();

        // Get approved leaves for this month
        $leaves = EmployeeLeave::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where(function($q) use ($year, $month) {
                $q->where(function($q2) use ($year, $month) {
                    $q2->whereYear('start_date', $year)->whereMonth('start_date', $month);
                })->orWhere(function($q2) use ($year, $month) {
                    $q2->whereYear('end_date', $year)->whereMonth('end_date', $month);
                });
            })
            ->get();

        $leaveDays = 0;
        foreach ($leaves as $leave) {
            $start = Carbon::parse($leave->start_date);
            $end = Carbon::parse($leave->end_date);
            
            // Count days within this month
            $monthStart = Carbon::create($year, $month, 1);
            $monthEnd = Carbon::create($year, $month, $totalDays);
            
            $overlapStart = $start->greaterThan($monthStart) ? $start : $monthStart;
            $overlapEnd = $end->lessThan($monthEnd) ? $end : $monthEnd;
            
            if ($overlapStart->lessThanOrEqualTo($overlapEnd)) {
                $leaveDays += $overlapStart->diffInDays($overlapEnd) + 1;
            }
        }

        // Calculate working days (present + half days + approved leaves)
        $workingDays = $presentDays + ($halfDays * 0.5) + $leaveDays;

        // Calculate salary
        $basicSalary = $employee->basic_salary;
        $dailySalary = $basicSalary / $totalDays;
        $earnedSalary = $dailySalary * $workingDays;

        // Get bonuses for this month
        $bonuses = \App\Models\EmployeeBonus::where('employee_id', $employee->id)
            ->where('salary_month', $request->salary_month)
            ->where('status', 'approved')
            ->sum('amount');

        // Calculate gross and net
        $allowance = 0; // Can be added later
        $deduction = ($absentDays * $dailySalary); // Deduct for absent days
        $grossSalary = $earnedSalary + $allowance + $bonuses;
        $netSalary = $grossSalary - $deduction;

        // Create or update salary record
        $salaryData = [
            'employee_id' => $employee->id,
            'salary_month' => $request->salary_month,
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'leave_days' => $leaveDays,
            'working_days' => round($workingDays, 2),
            'basic_salary' => $basicSalary,
            'allowance' => $allowance,
            'deduction' => $deduction,
            'bonus' => $bonuses,
            'overtime' => 0,
            'gross_salary' => $grossSalary,
            'net_salary' => $netSalary,
            'status' => 'calculated',
            'calculated_by' => Auth::id(),
            'calculated_at' => now(),
        ];

        if ($existing) {
            $existing->update($salaryData);
        } else {
            EmployeeSalary::create($salaryData);
        }

        Toastr::success('Salary calculated successfully!');
        return redirect()->route('admin.salaries.index');
    }

    /**
     * Bulk calculate salaries for all active employees
     */
    public function bulkCalculate(Request $request)
    {
        $request->validate([
            'salary_month' => 'required|date_format:Y-m',
        ]);

        $employees = Employee::where('status', 'active')->get();
        $calculated = 0;
        $skipped = 0;

        foreach ($employees as $employee) {
            // Check if already calculated
            $existing = EmployeeSalary::where('employee_id', $employee->id)
                ->where('salary_month', $request->salary_month)
                ->where('status', '!=', 'pending')
                ->first();

            if ($existing) {
                $skipped++;
                continue;
            }

            // Calculate salary (same logic as calculate method)
            $year = Carbon::parse($request->salary_month)->year;
            $month = Carbon::parse($request->salary_month)->month;
            
            $attendances = EmployeeAttendance::where('employee_id', $employee->id)
                ->whereYear('attendance_date', $year)
                ->whereMonth('attendance_date', $month)
                ->get();

            $totalDays = Carbon::create($year, $month, 1)->daysInMonth;
            $presentDays = $attendances->where('status', 'present')->count();
            $absentDays = $attendances->where('status', 'absent')->count();
            $halfDays = $attendances->where('status', 'half_day')->count();

            $leaves = EmployeeLeave::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where(function($q) use ($year, $month) {
                    $q->where(function($q2) use ($year, $month) {
                        $q2->whereYear('start_date', $year)->whereMonth('start_date', $month);
                    })->orWhere(function($q2) use ($year, $month) {
                        $q2->whereYear('end_date', $year)->whereMonth('end_date', $month);
                    });
                })
                ->get();

            $leaveDays = 0;
            foreach ($leaves as $leave) {
                $start = Carbon::parse($leave->start_date);
                $end = Carbon::parse($leave->end_date);
                $monthStart = Carbon::create($year, $month, 1);
                $monthEnd = Carbon::create($year, $month, $totalDays);
                $overlapStart = $start->greaterThan($monthStart) ? $start : $monthStart;
                $overlapEnd = $end->lessThan($monthEnd) ? $end : $monthEnd;
                if ($overlapStart->lessThanOrEqualTo($overlapEnd)) {
                    $leaveDays += $overlapStart->diffInDays($overlapEnd) + 1;
                }
            }

            $workingDays = $presentDays + ($halfDays * 0.5) + $leaveDays;
            $basicSalary = $employee->basic_salary;
            $dailySalary = $basicSalary / $totalDays;
            $earnedSalary = $dailySalary * $workingDays;

            $bonuses = \App\Models\EmployeeBonus::where('employee_id', $employee->id)
                ->where('salary_month', $request->salary_month)
                ->where('status', 'approved')
                ->sum('amount');

            $deduction = ($absentDays * $dailySalary);
            $grossSalary = $earnedSalary + $bonuses;
            $netSalary = $grossSalary - $deduction;

            EmployeeSalary::create([
                'employee_id' => $employee->id,
                'salary_month' => $request->salary_month,
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'leave_days' => $leaveDays,
                'working_days' => round($workingDays, 2),
                'basic_salary' => $basicSalary,
                'allowance' => 0,
                'deduction' => $deduction,
                'bonus' => $bonuses,
                'overtime' => 0,
                'gross_salary' => $grossSalary,
                'net_salary' => $netSalary,
                'status' => 'calculated',
                'calculated_by' => Auth::id(),
                'calculated_at' => now(),
            ]);

            $calculated++;
        }

        Toastr::success("Salary calculated for {$calculated} employees. {$skipped} skipped (already calculated).");
        return redirect()->route('admin.salaries.index');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $salary = EmployeeSalary::with('employee', 'calculatedBy')->findOrFail($id);
        return view('backEnd.salaries.show', compact('salary'));
    }
}
