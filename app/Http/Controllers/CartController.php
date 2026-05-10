<?php
namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

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
        return back()->with('success', 'เพิ่มสินค้าลงตะกร้าแล้ว');
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
