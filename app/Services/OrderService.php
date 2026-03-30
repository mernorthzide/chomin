<?php
namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\ShippingSetting;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $data, $cart, ?Coupon $coupon = null, int $pointsUsed = 0): Order
    {
        return DB::transaction(function () use ($data, $cart, $coupon, $pointsUsed) {
            $cart->load('items.product', 'items.variant.color');

            foreach ($cart->items as $item) {
                $variant = $item->variant()->lockForUpdate()->first();
                abort_if($variant->stock < $item->quantity, 422,
                    "สินค้า {$item->product->name} ({$variant->color->name} / {$variant->size}) สต็อกไม่เพียงพอ");
            }

            $subtotal = $cart->items->sum(fn ($item) => $item->product->price * $item->quantity);
            $shipping = ShippingSetting::current();
            $shippingFee = $shipping->getShippingFeeFor($subtotal);

            $couponDiscount = 0;
            if ($coupon && $coupon->isValid($subtotal)) {
                $couponDiscount = $coupon->calculateDiscount($subtotal);
            }

            $pointsToBaht = (int) SiteSetting::get('points_to_baht', 10);
            $pointsDiscount = $pointsUsed > 0 ? floor($pointsUsed / $pointsToBaht) : 0;

            $discount = $couponDiscount + $pointsDiscount;
            $total = max(0, $subtotal - $discount + $shippingFee);

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount' => $discount,
                'total' => $total,
                'points_used' => $pointsUsed,
                'coupon_id' => $coupon?->id,
                'shipping_name' => $data['shipping_name'],
                'shipping_phone' => $data['shipping_phone'],
                'shipping_address' => $data['shipping_address'],
                'shipping_district' => $data['shipping_district'],
                'shipping_province' => $data['shipping_province'],
                'shipping_postal_code' => $data['shipping_postal_code'],
                'note' => $data['note'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product->name,
                    'color_name' => $item->variant->color->name,
                    'size' => $item->variant->size,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                ]);
                $item->variant->decrement('stock', $item->quantity);
            }

            if ($pointsUsed > 0) {
                auth()->user()->decrement('points', $pointsUsed);
                auth()->user()->pointTransactions()->create([
                    'order_id' => $order->id,
                    'points' => -$pointsUsed,
                    'type' => 'redeem',
                    'description' => "ใช้แต้มสั่งซื้อ {$order->order_number}",
                ]);
            }

            if ($coupon) $coupon->increment('used_count');
            $cart->items()->delete();

            return $order;
        });
    }
}
