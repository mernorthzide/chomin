<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = auth()->user()
            ->wishlists()
            ->with('product.primaryImage')
            ->get();

        return view('pages.profile.wishlist', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'ลบออกจาก Wishlist แล้ว';
        } else {
            Wishlist::create([
                'user_id'    => auth()->id(),
                'product_id' => $request->product_id,
            ]);
            $message = 'เพิ่มใน Wishlist แล้ว';
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'in_wishlist' => !$existing]);
        }

        return back()->with('success', $message);
    }
}
