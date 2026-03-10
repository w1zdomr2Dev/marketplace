<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    // =============================
    // RELATIONSHIPS
    // =============================

    // Products → lahat ng products na nasa category na ito
    // Usage: $category->products
    //        $category->products()->where('status', 'active')->get()
    //        $category->products()->count()
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}