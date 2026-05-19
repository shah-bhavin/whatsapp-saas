<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor;

class Contact extends Model
{
    protected $fillable = [
        'vendor_id',
        'name',
        'mobile',
        'email',
        'status',
        'follow_up_at'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class)
            ->withPivot([
                'status',
                'sent_at',
                'error_message',
            ])
            ->withTimestamps();
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
    public function labels()
    {
        return $this->belongsToMany(
            Label::class
        );
    }
    public function notes()
    {
        return $this->hasMany(
            ContactNote::class
        );
    }
}
