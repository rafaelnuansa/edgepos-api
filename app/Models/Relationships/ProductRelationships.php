<?php


namespace App\Models\Relationships;


use App\Models\OrderItems;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductGroup;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ProductRelationships
{
    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItems::class, 'variant_id', 'id');
    }

}