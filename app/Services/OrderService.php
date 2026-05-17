<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\ShippingSetting;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderService
{
    public function createOrder(array $data, $cart, ?Coupon $coupon = null, int $pointsUsed = 0, array $giftCardCodes = [], string $paymentMethod = 'promptpay_slip', float $codFee = 0.0): Order
    {
        try {
            return DB::transaction(function () use ($data, $cart, $coupon, $pointsUsed, $giftCardCodes, $paymentMethod, $codFee) {
                $cart->load('items.product', 'items.variant.color');

                $lockedVariants = [];
                foreach ($cart->items as $item) {
                    $variant = $item->variant()->lockForUpdate()->first();
                    abort_if($variant->stock < $item->quantity, 422,
                        "สินค้า {$item->product->name} ({$variant->color->name} / {$variant->size}) สต็อกไม่เพียงพอ");
                    $lockedVariants[$item->id] = $variant;
                }

                $subtotal = $cart->items->sum(fn ($item) => $item->product->display_price * $item->quantity);
                $shipping = ShippingSetting::current();
                $shippingFee = $shipping->getShippingFeeFor($subtotal);

                $couponDiscount = 0;
                if ($coupon) {
                    $lockedCoupon = Coupon::whereKey($coupon->getKey())->lockForUpdate()->first();
                    abort_unless($lockedCoupon && $lockedCoupon->isValid($subtotal), 422, 'คูปองไม่ถูกต้องหรือถูกใช้หมดแล้ว');
                    $coupon = $lockedCoupon;
                    $couponDiscount = $coupon->calculateDiscount($subtotal);
                }

                $user = User::query()->whereKey(auth()->id())->lockForUpdate()->firstOrFail();
                $pointsUsed = max(0, min($pointsUsed, (int) $user->points));

                $pointsToBaht = (int) SiteSetting::get('points_to_baht', 10);
                $pointsDiscount = $pointsUsed > 0 ? floor($pointsUsed / $pointsToBaht) : 0;

                $giftWrap = (bool) ($data['gift_wrap'] ?? false);
                $giftWrapFee = $giftWrap ? (float) SiteSetting::get('gift_wrap_fee', 50) : 0;

                $discount = $couponDiscount + $pointsDiscount;
                $preGiftCardTotal = max(0, $subtotal - $discount + $shippingFee + $giftWrapFee + $codFee);
                $giftCards = app(GiftCardService::class)->lockRedeemableCards($giftCardCodes);
                $giftCardDiscount = min($preGiftCardTotal, (float) $giftCards->sum(fn ($card) => (float) $card->balance));
                $total = max(0, $preGiftCardTotal - $giftCardDiscount);

                $orderStatus = $paymentMethod === 'cod' ? 'awaiting_payment' : 'pending';

                $order = Order::create([
                    'user_id' => $user->id,
                    'order_number' => Order::generateOrderNumber(),
                    'status' => $orderStatus,
                    'payment_method' => $paymentMethod,
                    'subtotal' => $subtotal,
                    'shipping_fee' => $shippingFee,
                    'cod_fee' => $codFee,
                    'discount' => $discount,
                    'gift_card_discount' => $giftCardDiscount,
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
                    'gift_wrap' => $giftWrap,
                    'gift_wrap_fee' => $giftWrapFee,
                    'gift_message_to' => $data['gift_message_to'] ?? null,
                    'gift_message_from' => $data['gift_message_from'] ?? null,
                    'gift_message' => $data['gift_message'] ?? null,
                ]);

                foreach ($cart->items as $item) {
                    $order->items()->create([
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'product_name' => $item->product->name,
                        'color_name' => $item->variant->color->name,
                        'size' => $item->variant->size,
                        'custom_options' => $item->custom_options ?: null,
                        'price' => $item->product->display_price,
                        'quantity' => $item->quantity,
                    ]);
                    $lockedVariants[$item->id]->decrement('stock', $item->quantity);
                }

                if ($pointsUsed > 0) {
                    $user->decrement('points', $pointsUsed);
                    $user->pointTransactions()->create([
                        'order_id' => $order->id,
                        'points' => -$pointsUsed,
                        'type' => 'redeem',
                        'description' => "ใช้แต้มสั่งซื้อ {$order->order_number}",
                    ]);
                }

                if ($coupon) {
                    $coupon->increment('used_count');
                }

                if ($giftCardDiscount > 0) {
                    app(GiftCardService::class)->redeemForOrder($order, $giftCards, $giftCardDiscount);
                }

                $cart->items()->delete();

                return $order;
            });
        } catch (Throwable $e) {
            Log::error('OrderService.createOrder failed', [
                'user_id' => auth()->id(),
                'cart_id' => $cart->id ?? null,
                'payment_method' => $paymentMethod,
                'coupon_id' => $coupon?->id,
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
