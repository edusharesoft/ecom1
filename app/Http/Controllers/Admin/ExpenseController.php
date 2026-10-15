<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseLog;
use App\Models\FundTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Check if current user is Admin (Super Admin or has Admin role)
     */
    private function isAdmin()
    {
        $user = Auth::guard('admin')->user();
        if (!$user) {
            return false;
        }

        // Super Admin (id=1) is always admin
        if ($user->id == 1) {
            return true;
        }

        // Check if user has Admin role
        $spatieRoles = $user->getRoleNames()->map(function($role) {
            return strtolower($role);
        })->toArray();

        return in_array('admin', $spatieRoles);
    }

    /**
     * Calculate current fund balance
     */
    private function calculateFundBalance()
    {
        $total_in  = FundTransaction::where('direction', 'in')->sum('amount');
        $total_out = FundTransaction::where('direction', 'out')->sum('amount');
        return $total_in - $total_out;
    }
    // ✅ List + Summary
    public function index()
    {
        // ফান্ড ব্যালেন্স
        $total_in  = FundTransaction::where('direction', 'in')->sum('amount');
        $total_out = FundTransaction::where('direction', 'out')->sum('amount');
        $balance   = $total_in - $total_out;

        $today        = Carbon::today();
        $currentYear  = $today->year;
        $currentMonth = $today->month;

        // এই বছরে মোট খরচ
        $yearlyExpense = Expense::whereYear('expense_date', $currentYear)->sum('amount');

        // এই মাসে মোট খরচ
        $monthlyExpense = Expense::whereYear('expense_date', $currentYear)
                            ->whereMonth('expense_date', $currentMonth)
                            ->sum('amount');

        // আজকের খরচ
        $todayExpense = Expense::whereDate('expense_date', $today)->sum('amount');

        // হিস্টরি
        $expenses = Expense::orderBy('expense_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(20);

        return view('backEnd.expenses.index', compact(
            'balance',
            'currentYear',
            'currentMonth',
            'yearlyExpense',
            'monthlyExpense',
            'todayExpense',
            'expenses'
        ));
    }

    // ✅ Store Expense
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'category'     => 'nullable|string|max:100',
            'note'         => 'nullable|string',
        ]);

        // ব্যালেন্স চেক
        $total_in  = FundTransaction::where('direction', 'in')->sum('amount');
        $total_out = FundTransaction::where('direction', 'out')->sum('amount');
        $balance   = $total_in - $total_out;

        if ($validated['amount'] > $balance) {
            return back()->with('error', 'Not enough balance in fund!')
                         ->withInput();
        }

        // আগে expense এন্ট্রি
        $expense = Expense::create([
            'title'        => $validated['title'],
            'amount'       => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'category'     => $validated['category'] ?? null,
            'note'         => $validated['note'] ?? null,
            'created_by'   => Auth::id(),
        ]);

        // তারপর ফান্ড থেকে out ট্রানজ্যাকশন
        $fund = FundTransaction::create([
            'direction' => 'out',
            'source'    => 'expense',
            'source_id' => $expense->id,
            'amount'    => $expense->amount,
            'note'      => 'Expense: ' . $expense->title . ($expense->note ? ' - ' . $expense->note : ''),
            'created_by'=> Auth::id(),
        ]);

        // লিঙ্ক আপডেট
        $expense->update([
            'fund_transaction_id' => $fund->id,
        ]);

        return redirect()->route('admin.expenses.index')
                         ->with('success', 'Expense saved successfully!');
    }

    // ✅ Edit ফর্ম
    public function edit($id)
    {
        $expense = Expense::findOrFail($id);

        // উপরে summary একই থাকবে
        $total_in  = FundTransaction::where('direction', 'in')->sum('amount');
        $total_out = FundTransaction::where('direction', 'out')->sum('amount');
        $balance   = $total_in - $total_out;

        $today        = Carbon::today();
        $currentYear  = $today->year;
        $currentMonth = $today->month;

        $yearlyExpense = Expense::whereYear('expense_date', $currentYear)->sum('amount');
        $monthlyExpense = Expense::whereYear('expense_date', $currentYear)
                            ->whereMonth('expense_date', $currentMonth)
                            ->sum('amount');
        $todayExpense = Expense::whereDate('expense_date', $today)->sum('amount');

        $expenses = Expense::orderBy('expense_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(20);

        return view('backEnd.expenses.edit', compact(
            'expense',
            'balance',
            'currentYear',
            'currentMonth',
            'yearlyExpense',
            'monthlyExpense',
            'todayExpense',
            'expenses'
        ));
    }

    // ✅ Update Expense (Admin only)
    public function update(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only Admin can update expenses.');
        }

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'category'     => 'nullable|string|max:100',
            'note'         => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $id) {
            $expense = Expense::findOrFail($id);

            // Save old values for logging
            $old_title = $expense->title;
            $old_amount = $expense->amount;
            $old_expense_date = $expense->expense_date;
            $old_category = $expense->category;
            $old_note = $expense->note;

            // Calculate fund balance before update
            $fund_balance_before = $this->calculateFundBalance();

            // Update expense
            $expense->update([
                'title'        => $validated['title'],
                'amount'       => $validated['amount'],
                'expense_date' => $validated['expense_date'],
                'category'     => $validated['category'] ?? null,
                'note'         => $validated['note'] ?? null,
                'updated_by'   => Auth::id(),
            ]);

            // Update linked fund transaction
            if ($expense->fund_transaction_id) {
                $fund = FundTransaction::find($expense->fund_transaction_id);

                if ($fund) {
                    // Calculate balance difference
                    $amount_diff = $expense->amount - $old_amount;
                    
                    // Update fund transaction
                    $fund->amount = $expense->amount;
                    $fund->note   = 'Expense: ' . $expense->title . ($expense->note ? ' - ' . $expense->note : '');
                    $fund->updated_by = Auth::id();
                    $fund->save();
                }
            }

            // Calculate fund balance after update
            $fund_balance_after = $this->calculateFundBalance();

            // Create log entry
            $description = $this->generateEditDescription(
                $old_title, $old_amount, $old_expense_date, $old_category, $old_note,
                $validated['title'], $validated['amount'], $validated['expense_date'], 
                $validated['category'] ?? null, $validated['note'] ?? null,
                $fund_balance_before, $fund_balance_after
            );

            ExpenseLog::create([
                'expense_id' => $expense->id,
                'action' => 'edit',
                'old_title' => $old_title,
                'new_title' => $validated['title'],
                'old_amount' => $old_amount,
                'new_amount' => $validated['amount'],
                'old_expense_date' => $old_expense_date,
                'new_expense_date' => $validated['expense_date'],
                'old_category' => $old_category,
                'new_category' => $validated['category'] ?? null,
                'old_note' => $old_note,
                'new_note' => $validated['note'] ?? null,
                'fund_balance_before' => $fund_balance_before,
                'fund_balance_after' => $fund_balance_after,
                'description' => $description,
                'performed_by' => Auth::id(),
            ]);

            return redirect()->route('admin.expenses.index')
                             ->with('success', 'Expense updated successfully! Fund balance adjusted automatically.');
        });
    }

    /**
     * Generate description for edit log
     */
    private function generateEditDescription($old_title, $old_amt, $old_date, $old_cat, $old_note, 
                                             $new_title, $new_amt, $new_date, $new_cat, $new_note,
                                             $bal_before, $bal_after)
    {
        $parts = [];
        
        if ($old_title != $new_title) {
            $parts[] = "Title changed from '{$old_title}' to '{$new_title}'";
        }
        
        if ($old_amt != $new_amt) {
            $diff = $new_amt - $old_amt;
            $diff_sign = ($diff > 0) ? '+' : '';
            $parts[] = "Amount changed from {$old_amt} to {$new_amt} ({$diff_sign}{$diff})";
        }
        
        if ($old_date != $new_date) {
            $parts[] = "Date changed from {$old_date} to {$new_date}";
        }
        
        $balance_diff = $bal_after - $bal_before;
        $balance_sign = ($balance_diff > 0) ? '+' : '';
        $parts[] = "Fund balance changed from {$bal_before} to {$bal_after} ({$balance_sign}{$balance_diff})";
        
        return implode('. ', $parts);
    }

    /**
     * Delete Expense (Admin only)
     */
    public function destroy($id)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only Admin can delete expenses.');
        }

        return DB::transaction(function () use ($id) {
            $expense = Expense::findOrFail($id);

            // Save expense data for logging
            $old_title = $expense->title;
            $old_amount = $expense->amount;
            $old_expense_date = $expense->expense_date;
            $old_category = $expense->category;
            $old_note = $expense->note;
            $fund_transaction_id = $expense->fund_transaction_id;

            // Calculate fund balance before delete
            $fund_balance_before = $this->calculateFundBalance();

            // Calculate expected balance after delete
            // When expense is deleted, the linked fund transaction (OUT) should also be removed
            // So balance will increase by the expense amount
            $expected_balance_after = $fund_balance_before + $old_amount;

            // Create log entry BEFORE deleting
            $balance_diff = $expected_balance_after - $fund_balance_before;
            $balance_sign = ($balance_diff > 0) ? '+' : '';
            $description = "Expense deleted: '{$old_title}' ({$old_amount}). Fund balance changed from {$fund_balance_before} to {$expected_balance_after} ({$balance_sign}{$balance_diff})";

            ExpenseLog::create([
                'expense_id' => $id,
                'action' => 'delete',
                'old_title' => $old_title,
                'new_title' => null,
                'old_amount' => $old_amount,
                'new_amount' => null,
                'old_expense_date' => $old_expense_date,
                'new_expense_date' => null,
                'old_category' => $old_category,
                'new_category' => null,
                'old_note' => $old_note,
                'new_note' => null,
                'fund_balance_before' => $fund_balance_before,
                'fund_balance_after' => $expected_balance_after,
                'description' => $description,
                'performed_by' => Auth::id(),
            ]);

            // Delete linked fund transaction first (if exists)
            if ($fund_transaction_id) {
                $fund = FundTransaction::find($fund_transaction_id);
                if ($fund) {
                    $fund->delete();
                }
            }

            // Now delete the expense
            $expense->delete();

            // Verify balance after delete
            $actual_balance_after = $this->calculateFundBalance();

            return redirect()->route('admin.expenses.index')
                             ->with('success', 'Expense deleted successfully! Fund balance adjusted automatically.');
        });
    }

    /**
     * Expense Logs / Report
     */
    public function logs(Request $request)
    {
        $query = ExpenseLog::with(['expense', 'performedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logs = $query->paginate(20)->withQueryString();

        // Summary statistics
        $total_edits = ExpenseLog::where('action', 'edit')->count();
        $total_deletes = ExpenseLog::where('action', 'delete')->count();

        return view('backEnd.expenses.logs', compact('logs', 'total_edits', 'total_deletes'));
    }

    // ✅ Export CSV
    public function export(Request $request)
    {
        $from = $request->input('from_date');
        $to   = $request->input('to_date');

        $query = Expense::orderBy('expense_date', 'asc');

        if ($from) {
            $query->whereDate('expense_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('expense_date', '<=', $to);
        }

        $expenses = $query->get();

        $fileName = 'expenses_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($expenses) {
            $handle = fopen('php://output', 'w');

            // হেডার
            fputcsv($handle, ['Date', 'Title', 'Category', 'Amount', 'Note']);

            foreach ($expenses as $exp) {
                fputcsv($handle, [
                    $exp->expense_date,
                    $exp->title,
                    $exp->category,
                    $exp->amount,
                    $exp->note,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
