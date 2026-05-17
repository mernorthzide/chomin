<?php

namespace App\Http\Controllers;

use App\Models\CustomerInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GiftCardPurchaseController extends Controller
{
    public const DENOMINATIONS = [500, 1000, 2000, 5000];

    public function index()
    {
        return view('pages.gift-cards', ['denominations' => self::DENOMINATIONS]);
    }

    public function store(Request $request, string $locale): RedirectResponse
    {
        if ($request->filled('company')) {
            return back();
        }

        $data = $request->validate([
            'amount' => ['required', 'integer', 'in:'.implode(',', self::DENOMINATIONS)],
            'buyer_name' => ['required', 'string', 'max:120'],
            'buyer_email' => ['required', 'email', 'max:160'],
            'buyer_phone' => ['nullable', 'string', 'max:40'],
            'recipient_name' => ['required', 'string', 'max:120'],
            'recipient_email' => ['nullable', 'email', 'max:160'],
            'message' => ['nullable', 'string', 'max:500'],
            'deliver_on' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        CustomerInquiry::create([
            'type' => 'gift_card',
            'locale' => $locale,
            'name' => $data['buyer_name'],
            'email' => $data['buyer_email'],
            'phone' => $data['buyer_phone'] ?? null,
            'topic' => "Gift card ฿{$data['amount']} for {$data['recipient_name']}",
            'message' => $data['message'] ?? '',
            'status' => 'new',
            'meta' => [
                'amount' => (int) $data['amount'],
                'recipient_name' => $data['recipient_name'],
                'recipient_email' => $data['recipient_email'] ?? null,
                'deliver_on' => $data['deliver_on'] ?? null,
            ],
        ]);

        return back()->with('flash', [
            'type' => 'success',
            'message' => $locale === 'en'
                ? 'Thank you. We will send payment details and the gift card via email shortly.'
                : 'รับคำสั่งซื้อบัตรของขวัญแล้ว ทีมงานจะแจ้งวิธีชำระเงินและส่งบัตรทางอีเมล',
        ]);
    }
}
