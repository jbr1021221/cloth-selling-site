<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    protected $fillable = [
        'product_id',
        'sale_price',
        'starts_at',
        'ends_at',
        'max_quantity',
        'sold_count',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->whereRaw('sold_count < max_quantity');
    }

    public function isActiveNow()
    {
        return $this->is_active 
            && $this->starts_at <= now() 
            && $this->ends_at >= now() 
            && $this->sold_count < $this->max_quantity;
    }
}
