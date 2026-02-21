<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'subcategory',
        'price',
        'discount_price',
        'images',
        'sizes',
        'colors',
        'stock',
        'sku',
        'vendor_id',
        'is_active',
    ];

    protected $casts = [
        'images'    => 'array',
        'sizes'     => 'array',
        'colors'    => 'array',
        'is_active' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('approved', true)->latest();
    }
}

