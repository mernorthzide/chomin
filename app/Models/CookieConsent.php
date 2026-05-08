<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CookieConsent extends Model
{
    protected $fillable = ['consent_id', 'locale', 'categories', 'ip_hash', 'user_agent', 'consented_at'];

    protected $casts = ['categories' => 'array', 'consented_at' => 'datetime'];
}
