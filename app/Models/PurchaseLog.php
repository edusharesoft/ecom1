<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseLog extends Model
{
    protected $fillable = [
        'purchase_id',
        'action',
        'old_invoice_no',
        'new_invoice_no',
        'old_purchase_date',
        'new_purchase_date',
        'old_paid_amount',
        'new_paid_amount',
        'old_grand_total',
        'new_grand_total',
        'old_note',
        'new_note',
        'fund_balance_before',
        'fund_balance_after',
        'description',
        'performed_by',
    ];

    protected $casts = [
        'old_paid_amount' => 'decimal:2',
        'new_paid_amount' => 'decimal:2',
        'old_grand_total' => 'decimal:2',
        'new_grand_total' => 'decimal:2',
        'fund_balance_before' => 'decimal:2',
        'fund_balance_after' => 'decimal:2',
        'old_purchase_date' => 'date',
        'new_purchase_date' => 'date',
    ];

    /**
     * Get the purchase that this log belongs to
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Get the user who performed this action
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'performed_by');
    }
}
