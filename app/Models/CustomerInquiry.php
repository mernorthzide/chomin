<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerInquiry extends Model
{
    protected $fillable = [
        'type', 'locale', 'name', 'email', 'phone', 'topic', 'message', 'status', 'meta',
    ];

    protected $casts = ['meta' => 'array'];
}
