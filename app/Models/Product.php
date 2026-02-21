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

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function flashSales()
    {
        return $this->hasMany(FlashSale::class);
    }

    public function activeFlashSale()
    {
        return $this->hasOne(FlashSale::class)
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->whereRaw('sold_count < max_quantity')
            ->latest('starts_at');
    }

    public function getCurrentPrice()
    {
        if ($this->activeFlashSale) {
            return $this->activeFlashSale->sale_price;
        }

        return $this->discount_price ?? $this->price;
    }

    public function getHasDiscount()
    {
        return $this->activeFlashSale || $this->discount_price;
    }
}

