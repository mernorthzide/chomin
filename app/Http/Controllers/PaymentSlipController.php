<?php

namespace App\Http\Controllers;

use App\Mail\NewSlipNotification;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Support\SafeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentSlipController extends Controller
{
    public function store(Request $request, string $locale, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless(in_array($order->status, ['pending', 'awaiting_payment']), 422, 'ไม่สามารถแนบสลิปได้');

        $request->validate([
            'slip' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $path = $request->file('slip')->store('payment-slips', 'public');

        $order->paymentSlip()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'image_path' => $path,
                'uploaded_at' => now(),
                'confirmed_at' => null,
                'confirmed_by' => null,
                'rejection_reason' => null,
            ]
        );

        $order->update(['status' => 'awaiting_payment']);

        Log::info('Payment slip uploaded', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
        ]);

        // Notify admin
        $adminEmail = SiteSetting::get('site_email');
        if ($adminEmail) {
            $order->load('user', 'paymentSlip');
            SafeMail::queue($adminEmail, new NewSlipNotification($order));
        }

        return back()->with('success', 'อัปโหลดสลิปเรียบร้อย รอตรวจสอบ');
    }
}
