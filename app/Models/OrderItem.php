<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
    ];

    // Item belongs to an order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Item belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Subtotal for this item
    public function subtotal(): float
    {
        return $this->unit_price * $this->quantity;
    }
}