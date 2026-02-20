<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'total_amount',
        'shipping_charge',
        'discount',
        'final_amount',
        'payment_method',
        'payment_status',
        'delivery_address',
        'status',
        'vendor_id',
        'notes',
    ];

    protected $casts = [
        'delivery_address' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
