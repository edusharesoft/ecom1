<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundTransactionLog extends Model
{
    protected $fillable = [
        'fund_transaction_id',
        'action',
        'old_direction',
        'new_direction',
        'old_amount',
        'new_amount',
        'balance_before',
        'balance_after',
        'old_note',
        'new_note',
        'description',
        'performed_by',
    ];

    protected $casts = [
        'old_amount' => 'decimal:2',
        'new_amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    /**
     * Get the fund transaction that this log belongs to
     */
    public function fundTransaction(): BelongsTo
    {
        return $this->belongsTo(FundTransaction::class);
    }

    /**
     * Get the user who performed this action
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'performed_by');
    }
}
