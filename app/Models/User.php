<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// /implements MustVerifyEmail
class User extends Authenticatable 
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'role', 'avatar', 'phone', 'location', 'is_active'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // =============================
    // RELATIONSHIPS
    // =============================

    // Seller Products → lahat ng products ng user bilang seller
    // Usage: $user->sellerProducts
    //        $user->sellerProducts()->where('status', 'active')->get()
    //        $user->sellerProducts()->count()
    public function sellerProducts()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    // Orders bilang Buyer → lahat ng orders ng user bilang buyer
    // Usage: $user->buyerOrders
    //        $user->buyerOrders()->where('status', 'pending')->get()
    //        $user->buyerOrders()->latest()->first()
    public function buyerOrders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    // Orders bilang Seller → lahat ng orders na natanggap ng seller
    // Usage: $user->sellerOrders
    //        $user->sellerOrders()->where('status', 'pending')->get()
    //        $user->sellerOrders()->count()
    public function sellerOrders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    // Favorite Products → lahat ng paboritong products ng user
    // Usage: $user->favoriteProducts
    //        $user->favoriteProducts()->where('status', 'active')->get()
    //        $user->favoriteProducts()->attach($productId)   → mag-add ng favorite
    //        $user->favoriteProducts()->detach($productId)   → mag-remove ng favorite
    //        $user->favoriteProducts()->toggle($productId)   → add kung wala, remove kung meron
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites');
    }

    // User Reviews → lahat ng reviews na ginawa ng user bilang buyer
    // Usage: $user->userReviews
    //        $user->userReviews()->where('rating', 5)->get()
    //        $user->userReviews()->avg('rating')
    public function userReviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    // User Activity Logs → lahat ng activity ng user sa system
    // Usage: $user->userActivityLogs
    //        $user->userActivityLogs()->where('action', 'placed_order')->get()
    //        $user->userActivityLogs()->latest()->limit(10)->get()
    public function userActivityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // =============================
    // HELPER METHODS
    // =============================

    // Check kung seller ang user
    // Usage: $user->isSeller()
    //        @if(auth()->user()->isSeller()) ... @endif
    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    // Check kung buyer ang user
    // Usage: $user->isBuyer()
    //        @if(auth()->user()->isBuyer()) ... @endif
    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    // Check kung admin ang user
    // Usage: $user->isAdmin()
    //        @if(auth()->user()->isAdmin()) ... @endif
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}