<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'business_name',
        'email',
        'phone',
        'address',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(
            VendorSubscription::class
        );
    }

    public function activeSubscription()
    {
        return $this->hasOne(
            VendorSubscription::class
        )

        ->where('is_active', true)

        ->latest();
    }
}
