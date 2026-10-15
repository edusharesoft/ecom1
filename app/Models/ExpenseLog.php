<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseLog extends Model
{
    protected $fillable = [
        'expense_id',
        'action',
        'old_title',
        'new_title',
        'old_amount',
        'new_amount',
        'old_expense_date',
        'new_expense_date',
        'old_category',
        'new_category',
        'old_note',
        'new_note',
        'fund_balance_before',
        'fund_balance_after',
        'description',
        'performed_by',
    ];

    protected $casts = [
        'old_amount' => 'decimal:2',
        'new_amount' => 'decimal:2',
        'fund_balance_before' => 'decimal:2',
        'fund_balance_after' => 'decimal:2',
        'old_expense_date' => 'date',
        'new_expense_date' => 'date',
    ];

    /**
     * Get the expense that this log belongs to
     */
    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    /**
     * Get the user who performed this action
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'performed_by');
    }
}
