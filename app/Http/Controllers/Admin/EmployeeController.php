<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\FundTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Toastr;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::with('user', 'createdBy')->orderBy('id', 'DESC');

        // Search
        if ($request->keyword) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->keyword . '%')
                  ->orWhere('email', 'LIKE', '%' . $request->keyword . '%')
                  ->orWhere('employee_id', 'LIKE', '%' . $request->keyword . '%')
                  ->orWhere('phone', 'LIKE', '%' . $request->keyword . '%');
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by department
        if ($request->department) {
            $query->where('department', $request->department);
        }

        $employees = $query->paginate(20);
        $departments = Employee::distinct()->whereNotNull('department')->pluck('department');

        return view('backEnd.employees.index', compact('employees', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get users who don't have employee record yet
        $employeeUserIds = Employee::whereNotNull('user_id')->pluck('user_id')->toArray();
        $availableUsers = User::whereNull('vendor_id')
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['vendor', 'reseller']);
            })
            ->whereNotIn('id', $employeeUserIds)
            ->select('id', 'name', 'email')
            ->get();

        // Get all roles for creating new user
        $roles = Role::where('guard_name', 'admin')->get();

        return view('backEnd.employees.create', compact('availableUsers', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:50',
            'designation' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'joining_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'address' => 'nullable|string',
            'nid' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'create_user' => 'nullable|boolean',
            'user_role' => 'nullable|exists:roles,id',
        ]);

        $data = $request->except(['create_user', 'user_role']);
        $data['created_by'] = Auth::id();

        // If user_id is provided, use it
        // If create_user is checked, create new user
        if ($request->create_user && $request->user_role) {
            // Create new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('password123'), // Default password
                'status' => 1,
            ]);

            // Assign role
            $role = Role::find($request->user_role);
            if ($role) {
                $user->assignRole($role);
            }

            $data['user_id'] = $user->id;
        }

        Employee::create($data);

        Toastr::success('Employee created successfully!');
        return redirect()->route('admin.employees.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $employee = Employee::with([
            'user',
            'attendances' => function($q) {
                $q->orderBy('attendance_date', 'DESC')->limit(30);
            },
            'leaves' => function($q) {
                $q->orderBy('start_date', 'DESC')->limit(10);
            },
            'salaries' => function($q) {
                $q->orderBy('salary_month', 'DESC')->limit(12);
            },
            'bonuses' => function($q) {
                $q->orderBy('created_at', 'DESC')->limit(10);
            },
            'salaryPayments' => function($q) {
                $q->orderBy('payment_date', 'DESC')->limit(10);
            }
        ])->findOrFail($id);

        return view('backEnd.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        $employeeUserIds = Employee::whereNotNull('user_id')->pluck('user_id')->toArray();
        $availableUsers = User::whereNull('vendor_id')
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['vendor', 'reseller']);
            })
            ->whereNotIn('id', $employeeUserIds)
            ->select('id', 'name', 'email')
            ->get();

        return view('backEnd.employees.edit', compact('employee', 'availableUsers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'nullable|string|max:50',
            'designation' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'joining_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'address' => 'nullable|string',
            'nid' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,terminated',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $employee->update($request->all());

        Toastr::success('Employee updated successfully!');
        return redirect()->route('admin.employees.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        // Check if employee has any records
        if ($employee->attendances()->count() > 0 || 
            $employee->leaves()->count() > 0 || 
            $employee->salaries()->count() > 0) {
            Toastr::error('Cannot delete employee. Employee has attendance, leave, or salary records.');
            return back();
        }

        $employee->delete();

        Toastr::success('Employee deleted successfully!');
        return redirect()->route('admin.employees.index');
    }

    /**
     * Import user as employee
     */
    public function importFromUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'designation' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'joining_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
        ]);

        $user = User::findOrFail($request->user_id);

        // Check if user already has employee record
        if (Employee::where('user_id', $user->id)->exists()) {
            Toastr::error('This user is already an employee!');
            return back();
        }

        Employee::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'designation' => $request->designation,
            'department' => $request->department,
            'joining_date' => $request->joining_date,
            'basic_salary' => $request->basic_salary,
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);

        Toastr::success('User imported as employee successfully!');
        return redirect()->route('admin.employees.index');
    }
}
