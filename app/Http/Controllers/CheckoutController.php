<?php
namespace App\Http\Controllers;

use App\Mail\NewOrderNotification;
use App\Mail\OrderCreated;
use App\Models\Coupon;
use App\Models\SiteSetting;
use App\Services\CartService;
use App\Services\GiftCardService;
use App\Services\OrderService;
use App\Support\SafeMail;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService,
    ) {}

    public function index()
    {
        $cart = $this->cartService->getCart();
        $cart->load('items.product.translations', 'items.product.primaryImage', 'items.variant.color.translations');
        abort_if($cart->items->isEmpty(), 404, 'ตะกร้าว่าง');
        $addresses = auth()->user()->addresses()->orderByDesc('is_default')->get();
        return view('pages.checkout', compact('cart', 'addresses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_district' => 'required|string|max:255',
            'shipping_province' => 'required|string|max:255',
            'shipping_postal_code' => 'required|string|max:10',
            'coupon_code' => 'nullable|string',
            'points_used' => 'nullable|integer|min:0',
            'gift_card_codes' => 'nullable|array',
            'gift_card_codes.*' => 'nullable|string|max:80',
        ]);

        $cart = $this->cartService->getCart();
        abort_if($cart->items->isEmpty(), 422, 'ตะกร้าว่าง');

        $coupon = null;
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            abort_unless($coupon && $coupon->isValid($cart->subtotal), 422, 'คูปองไม่ถูกต้อง');
        }

        $giftCardCodes = $request->input('gift_card_codes', []);
        $giftCardErrors = app(GiftCardService::class)->validationErrors($giftCardCodes);
        if ($giftCardErrors !== []) {
            throw ValidationException::withMessages($giftCardErrors);
        }

        $pointsUsed = min($request->points_used ?? 0, auth()->user()->points);

        $order = $this->orderService->createOrder(
            $request->only(['shipping_name', 'shipping_phone', 'shipping_address', 'shipping_district', 'shipping_province', 'shipping_postal_code', 'note']),
            $cart, $coupon, $pointsUsed, $giftCardCodes,
        );

        // Dispatch email notifications
        $order->load('user', 'items.product', 'items.variant');
        SafeMail::queue($order->user->email, new OrderCreated($order));
        $adminEmail = SiteSetting::get('site_email');
        SafeMail::queue($adminEmail, new NewOrderNotification($order));

        return redirect()->route('checkout.success', $order)->with('success', 'สั่งซื้อสำเร็จ');
    }

    public function success(string $locale, \App\Models\Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $promptpay = [
            'id' => SiteSetting::get('promptpay_id'),
            'name' => SiteSetting::get('promptpay_name'),
            'qr' => SiteSetting::get('promptpay_qr'),
        ];
        return view('pages.checkout-success', compact('order', 'promptpay'));
    }
}
