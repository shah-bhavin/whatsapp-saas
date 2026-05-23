<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppTemplate extends Model
{
    protected $fillable = [
        'vendor_id',
        'whats_app_account_id',
        'template_name',
        'meta_template_id',
        'category',
        'language',
        'status',
        'header_text',
        'body_text',
        'footer_text',
        'buttons',
        'variables',
        'has_media',
        'media_type',
    ];

    protected $casts = [
        'buttons'   => 'array',
        'variables' => 'array',
    ];
    public function whatsAppAccount(){
        return $this->belongsTo(
            \App\Models\WhatsAppAccount::class
        );
    }
}
