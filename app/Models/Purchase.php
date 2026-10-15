<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id','invoice_no','purchase_date',
        'total_qty','subtotal','discount','shipping_cost',
        'grand_total','paid_amount','due_amount',
        'note','status','created_by','updated_by',
    ];

    protected $dates = ['purchase_date'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }
}
