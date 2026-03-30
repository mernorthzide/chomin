<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderHistoryController extends Controller
{
    public function index()
    {
        $orders = auth()->user()
            ->orders()
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pages.profile.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $order->load('items.product', 'items.variant.color', 'paymentSlip');
        return view('pages.profile.order-detail', compact('order'));
    }
}
