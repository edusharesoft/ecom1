<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\EmployeeSalaryPayment;
use App\Models\FundTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Toastr;

class SalaryPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EmployeeSalaryPayment::with('employee', 'salary', 'paidBy')->orderBy('payment_date', 'DESC');

        // Filter by employee
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by month
        if ($request->month) {
            $query->where('payment_month', $request->month);
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(20);
        $employees = Employee::where('status', 'active')->orderBy('name')->get();

        return view('backEnd.salary_payments.index', compact('payments', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        
        // If employee_id is provided, get their unpaid salaries
        $unpaidSalaries = [];
        if ($request->employee_id) {
            $unpaidSalaries = EmployeeSalary::where('employee_id', $request->employee_id)
                ->where('status', 'calculated')
                ->whereDoesntHave('salaryPayment')
                ->orderBy('salary_month', 'DESC')
                ->get();
        }

        return view('backEnd.salary_payments.create', compact('employees', 'unpaidSalaries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary_id' => 'nullable|exists:employee_salaries,id',
            'payment_month' => 'required|date_format:Y-m',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,bkash,nagad,rocket,check',
            'transaction_id' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $employee = Employee::findOrFail($request->employee_id);

        // Check fund balance
        $fundBalance = \App\Helpers\FundHelper::balance();
        if ($fundBalance < $request->amount) {
            Toastr::error('Insufficient fund balance! Current balance: ৳' . number_format($fundBalance, 2));
            return back();
        }

        // Create payment record
        $payment = EmployeeSalaryPayment::create([
            'employee_id' => $request->employee_id,
            'salary_id' => $request->salary_id,
            'payment_month' => $request->payment_month,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        // Deduct from fund
        FundTransaction::create([
            'direction' => 'out',
            'source' => 'employee_salary',
            'source_id' => $payment->id,
            'amount' => $request->amount,
            'note' => 'Salary payment for ' . $employee->name . ' (' . $employee->employee_id . ') - Month: ' . $request->payment_month . ' - Amount: ৳' . number_format($request->amount, 2),
            'created_by' => Auth::id(),
        ]);

        // Update payment status
        $payment->update([
            'status' => 'paid',
            'paid_by' => Auth::id(),
            'paid_at' => now(),
        ]);

        // Update salary status if linked
        if ($request->salary_id) {
            EmployeeSalary::where('id', $request->salary_id)->update(['status' => 'paid']);
        }

        Toastr::success('Salary paid successfully! Amount deducted from fund.');
        return redirect()->route('admin.salary_payments.index');
    }

    /**
     * Pay salary from calculated salary record
     */
    public function payFromSalary($salaryId)
    {
        $salary = EmployeeSalary::with('employee')->findOrFail($salaryId);

        if ($salary->status !== 'calculated') {
            Toastr::error('Salary must be calculated before payment!');
            return back();
        }

        // Check if already paid
        $existingPayment = EmployeeSalaryPayment::where('salary_id', $salaryId)->first();
        if ($existingPayment) {
            Toastr::error('Salary already paid!');
            return back();
        }

        // Check fund balance
        $fundBalance = \App\Helpers\FundHelper::balance();
        if ($fundBalance < $salary->net_salary) {
            Toastr::error('Insufficient fund balance! Current balance: ৳' . number_format($fundBalance, 2));
            return back();
        }

        // Create payment record
        $payment = EmployeeSalaryPayment::create([
            'employee_id' => $salary->employee_id,
            'salary_id' => $salary->id,
            'payment_month' => $salary->salary_month,
            'amount' => $salary->net_salary,
            'payment_method' => 'bank_transfer',
            'payment_date' => now(),
            'status' => 'pending',
        ]);

        // Deduct from fund
        FundTransaction::create([
            'direction' => 'out',
            'source' => 'employee_salary',
            'source_id' => $payment->id,
            'amount' => $salary->net_salary,
            'note' => 'Salary payment for ' . $salary->employee->name . ' (' . $salary->employee->employee_id . ') - Month: ' . $salary->salary_month . ' - Amount: ৳' . number_format($salary->net_salary, 2),
            'created_by' => Auth::id(),
        ]);

        // Update payment and salary status
        $payment->update([
            'status' => 'paid',
            'paid_by' => Auth::id(),
            'paid_at' => now(),
        ]);

        $salary->update(['status' => 'paid']);

        Toastr::success('Salary paid successfully! Amount deducted from fund.');
        return back();
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $payment = EmployeeSalaryPayment::with('employee', 'salary', 'paidBy')->findOrFail($id);
        return view('backEnd.salary_payments.show', compact('payment'));
    }
}
