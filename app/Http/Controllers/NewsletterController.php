<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterWelcome;
use App\Models\Coupon;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function store(Request $request, string $locale)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower($data['email']);
        $existing = NewsletterSubscriber::where('email', $email)->first();
        $isNew = ! $existing || $existing->status !== 'subscribed';

        NewsletterSubscriber::updateOrCreate(
            ['email' => $email],
            [
                'locale' => $locale,
                'status' => 'subscribed',
                'source' => $request->input('source', 'footer'),
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
            ],
        );

        // First-time subscribers get a welcome discount coupon
        $coupon = null;
        if ($isNew && $request->boolean('with_coupon')) {
            $coupon = $this->generateWelcomeCoupon();
        }

        if ($isNew) {
            Mail::to($email)->queue(new NewsletterWelcome($email, $locale, $coupon));
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'is_new' => $isNew,
                'coupon' => $coupon?->code,
                'discount' => $coupon ? (int) $coupon->value : null,
                'message' => $coupon
                    ? ($locale === 'en'
                        ? "Welcome! Use code {$coupon->code} for ".(int) $coupon->value.'% off your first order.'
                        : "ยินดีต้อนรับ ใช้รหัส {$coupon->code} รับส่วนลด ".(int) $coupon->value.'%')
                    : ($locale === 'en' ? 'Subscribed.' : 'สมัครเรียบร้อย'),
            ]);
        }

        return back()->with('success', $locale === 'en' ? 'You are subscribed.' : 'สมัครรับข่าวสารเรียบร้อยแล้ว');
    }

    private function generateWelcomeCoupon(): Coupon
    {
        $prefix = 'WELCOME';
        do {
            $code = $prefix.strtoupper(Str::random(6));
        } while (Coupon::where('code', $code)->exists());

        return Coupon::create([
            'code' => $code,
            'type' => 'percentage',
            'value' => (int) config('chomin.newsletter.discount_percent', 10),
            'max_discount' => (float) config('chomin.newsletter.discount_max', 300),
            'min_order_amount' => 0,
            'max_uses' => 1,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addDays((int) config('chomin.newsletter.coupon_valid_days', 30)),
            'is_active' => true,
        ]);
    }
}
