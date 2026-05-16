<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [

        'contact_id',

        'campaign_id',

        'mobile',

        'message',

        'direction',

        'status',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}