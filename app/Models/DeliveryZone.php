<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    protected $fillable = [
        'district_name',
        'delivery_charge',
        'estimated_days',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
