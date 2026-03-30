<?php
namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index()
    {
        $cart = $this->cartService->getCart();
        $cart->load('items.product.primaryImage', 'items.variant.color');
        return view('pages.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $this->cartService->addItem($request->variant_id, $request->quantity);
        return back()->with('success', 'เพิ่มสินค้าลงตะกร้าแล้ว');
    }

    public function update(Request $request, int $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:0']);
        $this->cartService->updateQuantity($itemId, $request->quantity);
        return back()->with('success', 'อัปเดตตะกร้าแล้ว');
    }

    public function remove(int $itemId)
    {
        $this->cartService->removeItem($itemId);
        return back()->with('success', 'ลบสินค้าออกจากตะกร้าแล้ว');
    }
}
