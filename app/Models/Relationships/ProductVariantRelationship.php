<?php


namespace App\Models\Relationships;


use App\Models\OrderItems;
use App\Models\Product;

trait ProductVariantRelationship
{
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItems::class, 'variant_id', 'id');
    }
}