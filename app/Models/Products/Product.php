<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('image_ordering', 'asc');
    }

    public function getTranslatedFeatured()
    {
        return $this->featured ? 'Yes' : 'No';
    }

    public function getFirstImage()
    {
        $firstImage = $this->images->first();

        if ($firstImage && $firstImage->image && Storage::disk('public')->exists($firstImage->image)) {
            return Storage::url($firstImage->image);
        }

        return null;
    }

    public function calculateDiscount()
    {
        if ($this->discount_price && $this->discount_price < $this->selling_price) {
            return round((($this->selling_price - $this->discount_price) / $this->selling_price) * 100, 0);
        }

        return 0;
    }

    public function getEffectivePrice()
    {
        return ($this->discount_price && $this->discount_price < $this->selling_price)
            ? $this->discount_price
            : $this->selling_price;
    }
}
