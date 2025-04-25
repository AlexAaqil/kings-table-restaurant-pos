<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getProductImageURL() {
        if(!empty($this->image)) {
            return url('storage/'.$this->image);
        }
        else {
            return asset('assets/images/default_image.jpg');
        }
    }
}
