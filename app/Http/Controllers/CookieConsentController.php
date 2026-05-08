<?php

namespace App\Http\Controllers;

use App\Models\CookieConsent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CookieConsentController extends Controller
{
    public function store(Request $request, string $locale)
    {
        $data = $request->validate([
            'categories' => ['required', 'array'],
            'categories.necessary' => ['required', 'boolean'],
            'categories.analytics' => ['nullable', 'boolean'],
            'categories.marketing' => ['nullable', 'boolean'],
            'categories.embeds' => ['nullable', 'boolean'],
        ]);

        $consent = CookieConsent::create([
            'consent_id' => (string) Str::uuid(),
            'locale' => $locale,
            'categories' => array_merge([
                'necessary' => true,
                'analytics' => false,
                'marketing' => false,
                'embeds' => false,
            ], $data['categories']),
            'ip_hash' => $request->ip() ? hash('sha256', $request->ip()) : null,
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
            'consented_at' => now(),
        ]);

        return response()->json(['consent_id' => $consent->consent_id]);
    }
}
