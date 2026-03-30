<?php
namespace App\Http\Controllers;

use App\Mail\NewSlipNotification;
use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentSlipController extends Controller
{
    public function store(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless(in_array($order->status, ['pending', 'awaiting_payment']), 422, 'ไม่สามารถแนบสลิปได้');

        $request->validate(['slip' => 'required|image|max:5120']);

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

        // Notify admin
        $adminEmail = SiteSetting::get('site_email');
        if ($adminEmail) {
            $order->load('user', 'paymentSlip');
            Mail::to($adminEmail)->send(new NewSlipNotification($order));
        }

        return back()->with('success', 'อัปโหลดสลิปเรียบร้อย รอตรวจสอบ');
    }
}
