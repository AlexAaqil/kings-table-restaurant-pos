<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Models\Products\Product;

class SaleItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'title',
        'quantity',
        'buying_price',
        'selling_price',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
