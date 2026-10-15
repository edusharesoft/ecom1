<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $fillable = [
        'supplier_id','purchase_id','amount',
        'payment_date','method','note',
        'fund_transaction_id','created_by',
    ];

    protected $dates = ['payment_date'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function fundTransaction()
    {
        return $this->belongsTo(\App\Models\FundTransaction::class);
    }
}
