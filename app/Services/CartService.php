<?php
namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()], ['session_id' => null]);
        }
        return Cart::firstOrCreate(['session_id' => session()->getId()], ['user_id' => null]);
    }

    public function addItem(int $variantId, int $quantity = 1): CartItem
    {
        $cart = $this->getCart();
        $variant = ProductVariant::findOrFail($variantId);
        $existing = $cart->items()->where('product_variant_id', $variantId)->first();

        if ($existing) {
            $newQty = $existing->quantity + $quantity;
            abort_if($newQty > $variant->stock, 422, 'สินค้าในสต็อกไม่เพียงพอ');
            $existing->update(['quantity' => $newQty]);
            return $existing->fresh();
        }

        abort_if($quantity > $variant->stock, 422, 'สินค้าในสต็อกไม่เพียงพอ');
        return $cart->items()->create([
            'product_id' => $variant->product_id,
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
        ]);
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        $cart = $this->getCart();
        $item = $cart->items()->findOrFail($itemId);
        if ($quantity <= 0) { $item->delete(); return; }
        abort_if($quantity > $item->variant->stock, 422, 'สินค้าในสต็อกไม่เพียงพอ');
        $item->update(['quantity' => $quantity]);
    }

    public function removeItem(int $itemId): void
    {
        $this->getCart()->items()->findOrFail($itemId)->delete();
    }

    public function mergeSessionCart(): void
    {
        if (!Auth::check()) return;
        $sessionCart = Cart::where('session_id', session()->getId())->first();
        if (!$sessionCart || $sessionCart->items->isEmpty()) return;
        $userCart = Cart::firstOrCreate(['user_id' => Auth::id()], ['session_id' => null]);
        foreach ($sessionCart->items as $item) {
            $existing = $userCart->items()->where('product_variant_id', $item->product_variant_id)->first();
            if ($existing) {
                $existing->update(['quantity' => min($existing->quantity + $item->quantity, $item->variant->stock)]);
            } else {
                $item->update(['cart_id' => $userCart->id]);
            }
        }
        $sessionCart->items()->delete();
        $sessionCart->delete();
    }

    public function clear(): void
    {
        $this->getCart()->items()->delete();
    }

    public function getItemCount(): int
    {
        return $this->getCart()->items->sum('quantity');
    }
}
