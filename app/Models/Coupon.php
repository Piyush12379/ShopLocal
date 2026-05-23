<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value',
        'min_order', 'max_uses', 'used_count',
        'is_active', 'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
    ];

    /**
     * Check if coupon is valid to use
     */
    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->used_count >= $this->max_uses) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        return true;
    }

    /**
     * Calculate discount amount for a given cart total
     */
    public function calculateDiscount(float $cartTotal): float
    {
        if ($cartTotal < $this->min_order) return 0;

        if ($this->type === 'percent') {
            return round($cartTotal * ($this->value / 100), 2);
        }

        // Flat discount — cannot exceed cart total
        return min($this->value, $cartTotal);
    }
}