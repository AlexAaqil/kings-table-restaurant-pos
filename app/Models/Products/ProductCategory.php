<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
