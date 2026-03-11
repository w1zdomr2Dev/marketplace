<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id', 'category_id', 'title', 'slug',
        'description', 'price', 'stock',
        'location', 'image', 'status', 'views'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'views' => 'integer',
    ];

    // =============================
    // RELATIONSHIPS
    // =============================

    // Seller → kung sino ang nagbenta ng product
    // Usage: $product->seller
    //        $product->seller->name
    //        $product->seller->email
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // Category → kung anong category ng product
    // Usage: $product->category
    //        $product->category->name
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Order Items → lahat ng order items na may product na ito
    // Usage: $product->orderItems
    //        $product->orderItems()->count()
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Favorited By → lahat ng users na nag-favorite ng product na ito
    // Usage: $product->favoritedBy
    //        $product->favoritedBy()->count()
    public function favoriteBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    // =============================
    // HELPER METHODS
    // =============================

    // Check kung available pa ang product
    // Usage: $product->isAvailable()
    //        @if($product->isAvailable()) ... @endif
    public function isAvailable(): bool
    {
        return $this->status === 'active' && $this->stock > 0;
    }

    // I-increment ang views kapag may nag-view ng product
    // Usage: $product->incrementViews()
    public function incrementViews(): void
    {
        $this->increment('views');
    }
}