<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_order', 'max_discount',
        'max_uses', 'times_used', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'expires_at' => 'datetime',
        'value'      => 'float',
        'min_order'  => 'float',
        'max_discount' => 'float',
    ];

    /**
     * Check if the coupon is currently valid (active, not expired, not exhausted).
     */
    public function isValid(): bool
    {
        if (! $this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses !== null && $this->times_used >= $this->max_uses) return false;
        return true;
    }

    /**
     * Calculate the actual discount for a given order subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'fixed') {
            return min($this->value, $subtotal);
        }

        // percentage
        $discount = ($subtotal * $this->value) / 100;
        if ($this->max_discount !== null) {
            $discount = min($discount, $this->max_discount);
        }
        return round($discount, 2);
    }

    /**
     * Increment the usage counter.
     */
    public function incrementUsage(): void
    {
        $this->increment('times_used');
    }
}
