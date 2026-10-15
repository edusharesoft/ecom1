<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FundTransaction extends Model
{
    protected $fillable = [
        'direction', 'source', 'source_id', 'amount', 'note', 'created_by', 'updated_by',
    ];

    /**
     * Get all logs for this transaction
     */
    public function logs(): HasMany
    {
        return $this->hasMany(FundTransactionLog::class, 'fund_transaction_id');
    }

    /**
     * Check if this transaction has been edited
     */
    public function hasBeenEdited(): bool
    {
        return $this->logs()->where('action', 'edit')->exists();
    }

    /**
     * Check if this transaction has been deleted (if it still exists, it means delete was cancelled or restored)
     */
    public function hasDeleteLog(): bool
    {
        return $this->logs()->where('action', 'delete')->exists();
    }

    /**
     * Get the latest edit log
     */
    public function latestEditLog()
    {
        return $this->logs()->where('action', 'edit')->latest()->first();
    }
}
