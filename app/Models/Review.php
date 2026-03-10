<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'reviewer_id', 'seller_id', 'rating', 'comment'
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // =============================
    // RELATIONSHIPS
    // =============================

    // Order → kung anong order ang na-review
    // Usage: $review->order
    //        $review->order->total_amount
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Reviewer → kung sino ang nag-review (buyer)
    // Usage: $review->reviewer
    //        $review->reviewer->name
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // Seller → kung sino ang na-review
    // Usage: $review->seller
    //        $review->seller->name
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}