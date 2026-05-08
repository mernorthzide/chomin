<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request, string $locale)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        NewsletterSubscriber::updateOrCreate(
            ['email' => strtolower($data['email'])],
            [
                'locale' => $locale,
                'status' => 'subscribed',
                'source' => $request->input('source', 'footer'),
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
            ],
        );

        return back()->with('success', $locale === 'en' ? 'You are subscribed.' : 'สมัครรับข่าวสารเรียบร้อยแล้ว');
    }
}
