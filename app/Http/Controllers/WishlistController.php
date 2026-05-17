<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WishlistController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Lazily generate share token on first view
        if (! $user->wishlist_share_token) {
            $user->wishlist_share_token = Str::random(32);
            $user->save();
        }

        $wishlists = $user
            ->wishlists()
            ->with('product.primaryImage', 'product.translations', 'product.variants', 'product.collection.translations', 'product.colors.translations')
            ->get();

        $shareUrl = route('wishlists.shared', ['locale' => app()->getLocale(), 'token' => $user->wishlist_share_token]);

        return view('pages.profile.wishlist', compact('wishlists', 'shareUrl'));
    }

    public function shared(string $locale, string $token)
    {
        $user = User::where('wishlist_share_token', $token)->firstOrFail();

        $wishlists = $user->wishlists()
            ->with('product.primaryImage', 'product.translations', 'product.variants', 'product.collection.translations', 'product.colors.translations')
            ->get()
            ->filter(fn ($w) => $w->product && $w->product->is_active)
            ->values();

        return view('pages.profile.wishlist-shared', [
            'wishlists' => $wishlists,
            'ownerName' => $user->name,
        ]);
    }

    public function regenerateShareToken(Request $request)
    {
        $user = auth()->user();
        $user->wishlist_share_token = Str::random(32);
        $user->save();

        return back()->with('flash', [
            'type' => 'success',
            'message' => app()->getLocale() === 'en' ? 'Share link regenerated.' : 'สร้างลิงก์ใหม่แล้ว',
        ]);
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
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
            ]);
            $message = 'เพิ่มใน Wishlist แล้ว';
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'in_wishlist' => ! $existing]);
        }

        return back()->with('success', $message);
    }
}
