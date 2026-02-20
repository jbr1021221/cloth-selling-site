<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SMSCampaign extends Model
{
    protected $fillable = [
        'message',
        'recipients',
        'recipient_count',
        'status',
        'sent_at',
        'created_by',
    ];

    protected $casts = [
        'recipients' => 'array',
        'sent_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
