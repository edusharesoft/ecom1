<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'title',
        'amount',
        'expense_date',
        'category',
        'note',
        'fund_transaction_id',
        'created_by',
        'updated_by',
    ];

    public function fundTransaction()
    {
        return $this->belongsTo(FundTransaction::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
