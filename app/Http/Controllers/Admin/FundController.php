<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FundTransaction;
use App\Models\FundTransactionLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FundController extends Controller
{
    /**
     * ফান্ড ড্যাশবোর্ড + হিস্টরি লিস্ট
     */
    public function index(Request $request)
    {
        $query = FundTransaction::orderBy('created_at', 'desc');

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->with('logs')->paginate(20)->withQueryString();

        // Compute totals more efficiently with a single query each (or you can combine into one)
        $total_in  = FundTransaction::where('direction', 'in')->sum('amount');
        $total_out = FundTransaction::where('direction', 'out')->sum('amount');
        $balance   = $total_in - $total_out;

        $now = Carbon::now();
        $currentYear  = $now->year;
        $currentMonth = $now->month;

        $yearlyAdded = FundTransaction::where('direction', 'in')
            ->whereYear('created_at', $currentYear)
            ->sum('amount');

        $monthlyAdded = FundTransaction::where('direction', 'in')
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('amount');

        return view('backEnd.fund.index', compact(
            'balance',
            'transactions',
            'total_in',
            'total_out',
            'yearlyAdded',
            'monthlyAdded',
            'currentYear',
            'currentMonth'
        ));
    }

    /**
     * ফান্ড Add
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'note'   => 'nullable|string|max:1000'
        ]);

        // Use DB transaction for safety (in case more ops are added later)
        DB::transaction(function () use ($validated) {
            FundTransaction::create([
                'direction'  => 'in',
                'source'     => 'manual_add',
                'source_id'  => null,
                // ensure decimal precision: cast to float or string decimal to avoid integer issues
                'amount'     => round((float)$validated['amount'], 2),
                'note'       => $validated['note'] ?? null,
                'created_by' => Auth::id(),
            ]);
        });

        return back()->with('success', 'Fund added successfully!');
    }

    /**
     * ফান্ড Withdraw
     */
    public function withdraw(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'note'   => 'nullable|string|max:1000'
        ]);

        // calculate balance inside transaction and lock rows if concurrent operations possible
        // simple approach: compute current balance, then create out tx
        return DB::transaction(function () use ($validated) {
            $total_in  = FundTransaction::where('direction', 'in')->sum('amount');
            $total_out = FundTransaction::where('direction', 'out')->sum('amount');
            $balance   = $total_in - $total_out;

            $amount = round((float)$validated['amount'], 2);

            if ($amount > $balance) {
                // throw ValidationException to redirect back with error
                return redirect()->back()->with('error', 'Not enough balance!');
            }

            FundTransaction::create([
                'direction'  => 'out',
                'source'     => 'withdraw',
                'source_id'  => null,
                'amount'     => $amount,
                'note'       => $validated['note'] ?? null,
                'created_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Withdraw successful!');
        });
    }

    /**
     * ফান্ড হিস্টরি Export (CSV)
     * filter = year | month | custom
     */
    public function export(Request $request)
    {
        $filter = $request->input('filter');

        $query = FundTransaction::orderBy('created_at', 'asc');

        if ($filter === 'year') {
            $year = (int) $request->input('year', now()->year);
            $query->whereYear('created_at', $year);
        } elseif ($filter === 'month') {
            $year  = (int) $request->input('year', now()->year);
            $month = (int) $request->input('month', now()->month);
            $query->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
        } else {
            $request->validate([
                'from_date' => 'nullable|date',
                'to_date'   => 'nullable|date',
            ]);

            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }
        }

        // File name
        $fileName = 'fund-history-'.now()->format('Y-m-d-H-i-s').'.csv';

        // Streamed response with chunking for large datasets
        $response = new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM so Excel can open correctly
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($handle, ['Date', 'Direction', 'Source', 'Amount', 'Note', 'Created By']);

            // chunk to avoid memory issues
            $query->chunk(500, function ($transactions) use ($handle) {
                foreach ($transactions as $tx) {
                    fputcsv($handle, [
                        // format datetime in app timezone
                        $tx->created_at->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                        $tx->direction == 'in' ? 'In (+)' : 'Out (-)',
                        $tx->source,
                        number_format((float)$tx->amount, 2, '.', ''), // normalized amount
                        $tx->note,
                        $tx->created_by,
                    ]);
                }
                // flush after each chunk
                if (function_exists('ob_flush')) ob_flush();
                if (function_exists('flush')) flush();
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', "attachment; filename=\"$fileName\"");

        return $response;
    }

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
     * Edit Fund Transaction (Admin only)
     */
    public function edit($id)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only Admin can edit fund transactions.');
        }

        $transaction = FundTransaction::findOrFail($id);
        return view('backEnd.fund.edit', compact('transaction'));
    }

    /**
     * Calculate current fund balance
     */
    private function calculateBalance()
    {
        $total_in  = FundTransaction::where('direction', 'in')->sum('amount');
        $total_out = FundTransaction::where('direction', 'out')->sum('amount');
        return $total_in - $total_out;
    }

    /**
     * Update Fund Transaction (Admin only)
     */
    public function update(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only Admin can update fund transactions.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'note'   => 'nullable|string|max:1000',
            'direction' => 'required|in:in,out'
        ]);

        return DB::transaction(function () use ($validated, $id) {
            $transaction = FundTransaction::findOrFail($id);

            // Save old values for logging
            $old_amount = $transaction->amount;
            $old_direction = $transaction->direction;
            $old_note = $transaction->note;

            // Calculate balance before update
            $balance_before = $this->calculateBalance();

            // Update transaction
            $new_amount = round((float)$validated['amount'], 2);
            $new_direction = $validated['direction'];
            $new_note = $validated['note'] ?? null;

            $transaction->update([
                'amount'    => $new_amount,
                'note'      => $new_note,
                'direction' => $new_direction,
                'updated_by' => Auth::id(),
            ]);

            // Calculate balance after update
            $balance_after = $this->calculateBalance();

            // Create log entry
            $description = $this->generateEditDescription(
                $old_direction, $old_amount, $old_note,
                $new_direction, $new_amount, $new_note,
                $balance_before, $balance_after
            );

            FundTransactionLog::create([
                'fund_transaction_id' => $transaction->id,
                'action' => 'edit',
                'old_direction' => $old_direction,
                'new_direction' => $new_direction,
                'old_amount' => $old_amount,
                'new_amount' => $new_amount,
                'balance_before' => $balance_before,
                'balance_after' => $balance_after,
                'old_note' => $old_note,
                'new_note' => $new_note,
                'description' => $description,
                'performed_by' => Auth::id(),
            ]);

            return redirect()->route('admin.fund.index')
                            ->with('success', 'Fund transaction updated successfully! Balance adjusted automatically.');
        });
    }

    /**
     * Generate description for edit log
     */
    private function generateEditDescription($old_dir, $old_amt, $old_note, $new_dir, $new_amt, $new_note, $bal_before, $bal_after)
    {
        $parts = [];
        
        if ($old_dir != $new_dir) {
            $parts[] = "Direction changed from {$old_dir} to {$new_dir}";
        }
        
        if ($old_amt != $new_amt) {
            $diff = $new_amt - $old_amt;
            $diff_sign = ($diff > 0) ? '+' : '';
            $parts[] = "Amount changed from {$old_amt} to {$new_amt} ({$diff_sign}{$diff})";
        }
        
        $balance_diff = $bal_after - $bal_before;
        $balance_sign = ($balance_diff > 0) ? '+' : '';
        $parts[] = "Balance changed from {$bal_before} to {$bal_after} ({$balance_sign}{$balance_diff})";
        
        return implode('. ', $parts);
    }

    /**
     * Delete Fund Transaction (Admin only)
     */
    public function destroy($id)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only Admin can delete fund transactions.');
        }

        return DB::transaction(function () use ($id) {
            $transaction = FundTransaction::findOrFail($id);

            // Save transaction data for logging
            $old_amount = $transaction->amount;
            $old_direction = $transaction->direction;
            $old_note = $transaction->note;

            // Calculate balance before delete
            $balance_before = $this->calculateBalance();

            // Calculate expected balance after delete (before actually deleting)
            // This helps us know what the balance will be
            $expected_balance_after = $balance_before;
            if ($old_direction == 'in') {
                $expected_balance_after = $balance_before - $old_amount; // Removing IN reduces balance
            } else {
                $expected_balance_after = $balance_before + $old_amount; // Removing OUT increases balance
            }

            // Create log entry BEFORE deleting (to avoid foreign key constraint violation)
            $balance_diff = $expected_balance_after - $balance_before;
            $balance_sign = ($balance_diff > 0) ? '+' : '';
            $description = "Transaction deleted: {$old_direction} {$old_amount}. Balance changed from {$balance_before} to {$expected_balance_after} ({$balance_sign}{$balance_diff})";

            FundTransactionLog::create([
                'fund_transaction_id' => $id, // Keep reference to transaction ID before deletion
                'action' => 'delete',
                'old_direction' => $old_direction,
                'new_direction' => null,
                'old_amount' => $old_amount,
                'new_amount' => null,
                'balance_before' => $balance_before,
                'balance_after' => $expected_balance_after,
                'old_note' => $old_note,
                'new_note' => null,
                'description' => $description,
                'performed_by' => Auth::id(),
            ]);

            // Now delete the transaction (log entry already created, so FK constraint won't fail)
            $transaction->delete();

            // Verify balance after delete (for confirmation)
            $actual_balance_after = $this->calculateBalance();

            return redirect()->route('admin.fund.index')
                            ->with('success', 'Fund transaction deleted successfully! Balance adjusted automatically.');
        });
    }

    /**
     * Fund Transaction Logs / Report
     */
    public function logs(Request $request)
    {
        $query = FundTransactionLog::with(['fundTransaction', 'performedBy'])
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
        $total_edits = FundTransactionLog::where('action', 'edit')->count();
        $total_deletes = FundTransactionLog::where('action', 'delete')->count();

        return view('backEnd.fund.logs', compact('logs', 'total_edits', 'total_deletes'));
    }
}
