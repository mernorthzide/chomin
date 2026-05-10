<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a specific product.
     */
    public function show(string $locale, Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load([
            'collection',
            'category',
            'translations',
            'collection.translations',
            'category.translations',
            'colors.images',
            'colors.translations',
            'colors.variants',
            'images',
            'primaryImage',
            'variants',
        ]);

        // Related products: same collection, excluding current
        $related = Product::active()
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('collection_id', $product->collection_id)
                    ->orWhere('category_id', $product->category_id);
            })
            ->with(['primaryImage', 'images', 'variants', 'translations', 'collection.translations', 'colors.translations'])
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        $title = ($product->localized('seo_title') ?: $product->localized_name).' | CHOMIN';
        $description = $product->localized('seo_description')
            ?: $product->localized_description
            ?: 'CHO.MIN — เชิ้ตดีไซน์ 50+ สี ไซส์ XS-6XL จัดส่งฟรีทั่วประเทศ';
        $ogImage = $product->primaryImage
            ? url(Storage::url($product->primaryImage->image_path))
            : null;
        $inWishlist = auth()->check()
            ? auth()->user()->wishlists()->where('product_id', $product->id)->exists()
            : false;

        return view('pages.products.show', compact('product', 'related', 'title', 'description', 'ogImage', 'inWishlist'));
    }
}
