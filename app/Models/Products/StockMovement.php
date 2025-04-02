<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    const STOCKMOVEMENTYPE = [
        'adjustment',
        'purchase', 
        'restock', 
        'sale', 
    ];
}
