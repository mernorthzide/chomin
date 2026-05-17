<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function store(Request $request, string $locale, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'body' => ['nullable', 'string', 'max:2000'],
            'name' => ['required_without:user_id', 'nullable', 'string', 'max:120'],
            'email' => ['required_without:user_id', 'nullable', 'email', 'max:160'],
            'photos' => ['nullable', 'array', 'max:4'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $user = $request->user();

        $verifiedOrder = $user ? Order::query()
            ->where('user_id', $user->id)
            ->whereIn('status', OrderStatus::paidStatuses())
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->latest()
            ->first() : null;

        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('reviews/'.$product->id, 'public');
            }
        }

        ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'order_id' => $verifiedOrder?->id,
            'name' => $user?->name ?? $data['name'] ?? null,
            'email' => $user?->email ?? $data['email'] ?? null,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'] ?? null,
            'photos' => $photoPaths ?: null,
            'is_verified_purchase' => (bool) $verifiedOrder,
            'status' => 'pending',
        ]);

        return back()->with('flash', [
            'type' => 'success',
            'message' => $locale === 'en'
                ? 'Thanks for your review! It will appear after moderation.'
                : 'ขอบคุณสำหรับรีวิว ระบบจะแสดงรีวิวหลังตรวจสอบ',
        ]);
    }
}
