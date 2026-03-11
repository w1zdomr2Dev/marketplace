<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id', 'seller_id', 'status', 'total_amount'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    // =============================
    // RELATIONSHIPS
    // =============================

    // Buyer → kung sino ang bumili
    // Usage: $order->buyer
    //        $order->buyer->name 
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // Seller → kung sino ang nagbenta
    // Usage: $order->seller
    //        $order->seller->name
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // Items → lahat ng items sa order
    // Usage: $order->items
    //        $order->items()->count()
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Review → ang review ng buyer pagkatapos ng order
    // Usage: $order->review
    //        $order->review->rating
    public function review()
    {
        return $this->hasOne(Review::class);
    }

    // =============================
    // HELPER METHODS
    // =============================

    // Check kung pwede pang i-cancel ang order
    // Usage: $order->isCancellable()
    //        @if($order->isCancellable()) ... 
    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    // Check kung pwede na mag-review
    // Usage: $order->canBeReviewed()
    //        @if($order->canBeReviewed()) ... 
    public function canBeReviewed(): bool
    {
        return $this->status === 'completed' && !$this->review;
    }
}