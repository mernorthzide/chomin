<?php

namespace App\Http\Controllers;

use App\Models\BackInStockNotification;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BackInStockController extends Controller
{
    public function store(Request $request, string $locale, Product $product): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:160'],
            'size' => ['nullable', 'string', 'max:24'],
            'color' => ['nullable', 'string', 'max:80'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
        ]);

        BackInStockNotification::updateOrCreate(
            [
                'email' => $data['email'],
                'product_id' => $product->id,
                'size' => $data['size'] ?? null,
                'color' => $data['color'] ?? null,
            ],
            [
                'product_variant_id' => $data['variant_id'] ?? null,
                'user_id' => $request->user()?->id,
                'notified_at' => null,
            ],
        );

        return response()->json([
            'ok' => true,
            'message' => $locale === 'en'
                ? "We'll email you when this is back in stock."
                : 'เราจะแจ้งทางอีเมลเมื่อสินค้านี้กลับมามีสต็อก',
        ]);
    }
}
