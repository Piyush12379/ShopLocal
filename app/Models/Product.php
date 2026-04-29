<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'vendor_id', 'category_id', 'name',
        'description', 'price', 'old_price',
        'stock', 'image', 'emoji', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price'     => 'decimal:2',
        'old_price' => 'decimal:2',
    ];

    // Product belongs to one category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Product belongs to one vendor (user)
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    // Product has many reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Helper — is this product on sale?
    public function isOnSale(): bool
    {
        return !is_null($this->old_price) && $this->old_price > $this->price;
    }

    // Helper — is product in stock?
    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    // Helper — discount percentage
    public function discountPercent(): int
    {
        if (!$this->isOnSale()) return 0;
        return round((($this->old_price - $this->price) / $this->old_price) * 100);
    }
}