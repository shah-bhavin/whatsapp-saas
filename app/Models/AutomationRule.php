<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
     protected $fillable = [

        'name',

        'keyword',

        'reply_message',

        'is_active'
    ];
}
