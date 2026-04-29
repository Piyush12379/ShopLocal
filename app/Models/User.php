<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
   use HasApiTokens, HasFactory, Notifiable;

    // These fields can be filled via forms
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_approved',
    ];

    // These fields are hidden when you convert user to JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Automatically cast these columns to the right PHP type
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_approved'       => 'boolean',
    ];

    // Helper methods — call like: $user->isAdmin()
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isShopkeeper(): bool
    {
        return $this->role === 'shopkeeper';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isApproved(): bool
    {
        return $this->is_approved === true;
    }

    // Relationships — a shopkeeper has many products
    public function products()
    {
        return $this->hasMany(Product::class, 'vendor_id');
    }

    // A customer has many orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}