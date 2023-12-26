<?php


namespace App\Models\Relationships;


use App\Models\OrderItems;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait OrderRelationship
{
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItems::class, 'order_id', 'id');
    }
}