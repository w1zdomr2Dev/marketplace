<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'price_at_purchase'
    ];

    protected $casts = [
        'price_at_purchase' => 'decimal:2',
        'quantity'          => 'integer',
    ];

    // =============================
    // RELATIONSHIPS
    // =============================

    // Order → kung anong order ang item na ito
    // Usage: $orderItem->order
    //        $orderItem->order->status
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Product → kung anong product ang item na ito
    // Usage: $orderItem->product
    //        $orderItem->product->title
    //        $orderItem->product->image
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // =============================
    // HELPER METHODS
    // =============================

    // I-compute ang subtotal ng item
    // Usage: $orderItem->subtotal()
    //        "₱{{ $orderItem->subtotal() }}"
    public function subtotal(): float
    {
        return $this->quantity * $this->price_at_purchase;
    }
}