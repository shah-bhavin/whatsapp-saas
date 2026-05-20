<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorSubscription extends Model
{
    protected $fillable = [
        'vendor_id',
        'plan_id',
        'starts_at',
        'expires_at',
        'is_active'
    ];

    public function vendor()
    {
        return $this->belongsTo(
            Vendor::class
        );
    }

    public function plan()
    {
        return $this->belongsTo(
            Plan::class
        );
    }
}
