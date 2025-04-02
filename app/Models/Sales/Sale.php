<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Sale extends Model
{
    protected $guarded = [];

    const SALESTYPE = [
        0 => 'pos',
        1 => 'online'
    ];

    const SALESSTATUSTYPE = [
        0 => 'pending',
        1 => 'completed',
        2 => 'canceled',
        3 => 'refund'
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }

    public function sale_payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
