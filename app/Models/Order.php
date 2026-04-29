<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'payment_method',
        'address',
    ];

    // Order belongs to a customer
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Order has many items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Status badge color — used in Blade
    public function statusColor(): string
    {
        return match($this->status) {
            'pending'    => '#F57F17',
            'processing' => '#1565C0',
            'shipped'    => '#6A1B9A',
            'delivered'  => '#2E7D32',
            default      => '#666',
        };
    }

    // Status emoji
    public function statusEmoji(): string
    {
        return match($this->status) {
            'pending'    => '⏳',
            'processing' => '📦',
            'shipped'    => '🚚',
            'delivered'  => '✅',
            default      => '❓',
        };
    }
}