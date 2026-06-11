<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Services\AbandonedCartTracker;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private AbandonedCartTracker $abandonedCartTracker,
    ) {}

    public function index()
    {
        $cart = $this->cartService->getCart();
        $cart->load('items.product.primaryImage', 'items.product.translations', 'items.variant.color.translations');

        return view('pages.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $customOptions = config('chomin.custom_options');

        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'custom_options' => 'nullable|array',
            'custom_options.collar' => ['nullable', 'string', Rule::in(array_keys($customOptions['collar']['options']))],
            'custom_options.cuff' => ['nullable', 'string', Rule::in(array_keys($customOptions['cuff']['options']))],
            'custom_options.pocket' => ['nullable', 'string', Rule::in(array_keys($customOptions['pocket']['options']))],
        ]);
        $this->cartService->addItem(
            $request->integer('variant_id'),
            $request->integer('quantity'),
            $request->input('custom_options', []),
        );

        // Track for abandoned cart recovery (no email sent yet — data only)
        $this->abandonedCartTracker->capture($this->cartService->getCart());

        return back()->with('success', 'เพิ่มสินค้าลงตะกร้าแล้ว');
    }

    public function validateCoupon(Request $request, string $locale)
    {
        $data = $request->validate([
            'coupon_code' => ['required', 'string', 'max:80'],
        ]);

        $cart = $this->cartService->getCart();
        $cart->loadMissing('items.product');
        $code = trim($data['coupon_code']);

        if ($cart->items->isEmpty()) {
            return response()->json([
                'ok' => false,
                'message' => $locale === 'en' ? 'Your cart is empty.' : 'ตะกร้าของคุณว่างเปล่า',
            ], 422);
        }

        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon || ! $coupon->isValid($cart->subtotal)) {
            return response()->json([
                'ok' => false,
                'message' => $locale === 'en' ? 'This coupon is not valid for your cart.' : 'คูปองไม่ถูกต้องหรือไม่เข้าเงื่อนไข',
            ], 422);
        }

        $discount = $coupon->calculateDiscount((float) $cart->subtotal);

        return response()->json([
            'ok' => true,
            'code' => $coupon->code,
            'discount' => $discount,
            'formatted_discount' => '฿'.number_format($discount, 0),
            'message' => $locale === 'en' ? 'Coupon applied.' : 'ใช้คูปองได้',
        ]);
    }

    public function update(Request $request, string $locale, int $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:0']);
        $this->cartService->updateQuantity($itemId, $request->quantity);

        return back()->with('success', 'อัปเดตตะกร้าแล้ว');
    }

    public function remove(string $locale, int $itemId)
    {
        $this->cartService->removeItem($itemId);

        return back()->with('success', 'ลบสินค้าออกจากตะกร้าแล้ว');
    }
}
