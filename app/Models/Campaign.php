<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor;

class Campaign extends Model
{
    protected $fillable = [
        'vendor_id',
        'title',
        'message',
        'status',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class)
            ->withPivot([
                'status',
                'sent_at',
                'error_message',
            ])
            ->withTimestamps();
    }

    public function template()
    {
        return $this->belongsTo(
            WhatsAppTemplate::class,
            'whats_app_template_id'
        );
    }
}