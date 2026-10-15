<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Toastr;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EmployeeLeave::with('employee', 'approvedBy', 'createdBy')->orderBy('created_at', 'DESC');

        // Filter by employee
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by leave type
        if ($request->leave_type) {
            $query->where('leave_type', $request->leave_type);
        }

        $leaves = $query->paginate(20);
        $employees = Employee::where('status', 'active')->orderBy('name')->get();

        return view('backEnd.leaves.index', compact('leaves', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        return view('backEnd.leaves.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|in:sick,casual,annual,emergency,maternity,paternity,unpaid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        EmployeeLeave::create([
            'employee_id' => $request->employee_id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        Toastr::success('Leave request created successfully!');
        return redirect()->route('admin.leaves.index');
    }

    /**
     * Approve leave
     */
    public function approve($id)
    {
        $leave = EmployeeLeave::findOrFail($id);
        
        $leave->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        Toastr::success('Leave approved successfully!');
        return back();
    }

    /**
     * Reject leave
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'required|string',
        ]);

        $leave = EmployeeLeave::findOrFail($id);
        
        $leave->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        Toastr::success('Leave rejected!');
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $leave = EmployeeLeave::with('employee')->findOrFail($id);
        return view('backEnd.leaves.edit', compact('leave'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $leave = EmployeeLeave::findOrFail($id);

        $request->validate([
            'leave_type' => 'required|in:sick,casual,annual,emergency,maternity,paternity,unpaid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        $leave->update([
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
        ]);

        Toastr::success('Leave updated successfully!');
        return redirect()->route('admin.leaves.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $leave = EmployeeLeave::findOrFail($id);
        $leave->delete();

        Toastr::success('Leave deleted successfully!');
        return redirect()->route('admin.leaves.index');
    }
}
