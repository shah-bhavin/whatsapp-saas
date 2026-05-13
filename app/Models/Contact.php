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
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}