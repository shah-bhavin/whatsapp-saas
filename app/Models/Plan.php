<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'monthly_message_limit',
        'agent_limit',
        'has_ai_access',
        'has_campaign_access'
    ];

    public function subscriptions()
    {
        return $this->hasMany(
            VendorSubscription::class
        );
    }
}
