<?php

namespace App\Services;

use App\Models\AbandonedCart;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class AbandonedCartTracker
{
    public function capture(Cart $cart): ?AbandonedCart
    {
        $cart->loadMissing(['items.variant.product.translations', 'items.product.translations']);

        if ($cart->items->isEmpty()) {
            return null;
        }

        $user = Auth::user();
        $email = $user?->email;
        $sessionId = $cart->session_id ?: session()->getId();

        $itemsSnapshot = $cart->items->take(30)->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => optional($item->product)->localized_name ?? optional($item->product)->name,
                'variant_id' => $item->product_variant_id,
                'size' => optional($item->variant)->size,
                'quantity' => $item->quantity,
                'price' => (float) (optional($item->variant)->price ?? optional($item->product)->display_price ?? 0),
                'custom_options' => $item->custom_options,
            ];
        })->values()->all();

        $total = collect($itemsSnapshot)->sum(fn ($i) => $i['price'] * $i['quantity']);

        $lookup = $user ? ['user_id' => $user->id, 'recovered_at' => null] : ['session_id' => $sessionId, 'recovered_at' => null];

        return AbandonedCart::updateOrCreate(
            $lookup,
            [
                'user_id' => $user?->id,
                'cart_id' => $cart->id,
                'email' => $email,
                'session_id' => $sessionId,
                'items_snapshot' => $itemsSnapshot,
                'total' => $total,
            ],
        );
    }

    public function markRecovered(Cart $cart): void
    {
        $user = Auth::user();
        $sessionId = $cart->session_id ?: session()->getId();

        AbandonedCart::query()
            ->whereNull('recovered_at')
            ->where(function ($q) use ($user, $sessionId) {
                if ($user) {
                    $q->where('user_id', $user->id);
                } else {
                    $q->where('session_id', $sessionId);
                }
            })
            ->update(['recovered_at' => now()]);
    }
}
