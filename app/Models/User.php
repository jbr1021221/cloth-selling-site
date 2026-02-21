<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'dob',
        'referred_by_id',
        'tier',
        'total_spent',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'address' => 'array',
        ];
    }
    
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function smsCampaigns()
    {
        return $this->hasMany(SMSCampaign::class, 'created_by');
    }

    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    public function addPoints(int $points, string $type, ?string $description = null, ?int $orderId = null)
    {
        $this->loyaltyPoints()->create([
            'points'      => $points,
            'type'        => $type,
            'description' => $description,
            'order_id'    => $orderId,
        ]);

        $this->increment('total_points', $points);
    }
    public function updateTier()
    {
        $newTier = 'silver';
        if ($this->total_spent >= 20000) {
            $newTier = 'diamond';
        } elseif ($this->total_spent >= 5000) {
            $newTier = 'gold';
        }

        if ($this->tier !== $newTier) {
            $this->update(['tier' => $newTier]);
        }
    }

    public function nextTierRequirement()
    {
        if ($this->tier === 'silver') {
            return [
                'next_tier' => 'gold',
                'remaining' => max(0, 5000 - $this->total_spent)
            ];
        } elseif ($this->tier === 'gold') {
            return [
                'next_tier' => 'diamond',
                'remaining' => max(0, 20000 - $this->total_spent)
            ];
        }
        
        return null; // Diamond has no next tier
    }
}
