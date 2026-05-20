<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorUsage extends Model
{
    protected $fillable = [
        'vendor_id',
        'messages_sent',
        'ai_requests',
        'month',
    ];
}
