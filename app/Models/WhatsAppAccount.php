<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppAccount extends Model
{
    protected $table = 'whats_app_accounts';
    protected $fillable = [
        'vendor_id',
        'name',
        'phone_number',
        'phone_number_id',
        'business_account_id',
        'access_token',
        'verify_token',
        'is_active',
        'is_default',
        'status',
        'quality_rating',
        'last_synced_at',
        'token_expires_at',
    ];
}
