<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

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
        return back()->with('success', 'อัปโหลดสลิปเรียบร้อย รอตรวจสอบ');
    }
}
