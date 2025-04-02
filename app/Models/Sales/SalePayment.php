<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    protected $guarded = [];

    const PAYMENTMETHODS = [
        'cash',
        'mpesa',
        'card',
        'bank_transfer'
    ];
    
    const PAYMENTSTATUSTYPE = [
        'pending',
        'confirmed',
        'failed',
        'reversed'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function electronic_payment()
    {
        return $this->hasOne(Payment::class, 'transaction_reference', 'transaction_reference');
    }
}
