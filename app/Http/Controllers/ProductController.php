<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\RecommendationService;
use App\Support\Seo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct(private RecommendationService $recommendations) {}

    public function quickview(string $locale, Product $product): JsonResponse
    {
        abort_unless($product->is_active, 404);

        $product->load([
            'primaryImage',
            'images',
            'variants',
            'colors.images',
            'colors.translations',
            'translations',
            'collection.translations',
        ]);

        $images = $product->images->map(fn ($img) => Storage::url($img->image_path))->values();
        if ($product->primaryImage) {
            $images = $images->prepend(Storage::url($product->primaryImage->image_path))->unique()->values();
        }

        $colors = $product->colors->map(fn ($c) => [
            'name' => $c->localized_name ?? $c->name,
            'slug' => $c->slug,
            'code' => $c->color_code,
        ])->values();

        $sizes = $product->variants
            ->groupBy('size')
            ->map(fn ($variants, $size) => [
                'size' => $size,
                'stock' => $variants->sum('stock'),
            ])->values();

        return response()->json([
            'id' => $product->id,
            'slug' => $product->slug,
            'name' => $product->localized_name,
            'description' => strip_tags($product->localized_description ?? ''),
            'price' => (float) $product->price,
            'sale_price' => $product->is_on_sale ? (float) $product->sale_price : null,
            'display_price' => (float) $product->display_price,
            'is_on_sale' => $product->is_on_sale,
            'collection' => $product->collection ? ($product->collection->localized_name ?? $product->collection->name) : null,
            'images' => $images,
            'colors' => $colors,
            'sizes' => $sizes,
            'url' => route('products.show', ['locale' => $locale, 'product' => $product->slug]),
            'total_stock' => (int) $product->variants->sum('stock'),
        ]);
    }

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

        // Complete the look: featured items from OTHER collections
        $completeTheLook = Product::active()
            ->featured()
            ->where('id', '!=', $product->id)
            ->whereNotIn('id', $related->pluck('id'))
            ->when($product->collection_id, fn ($q) => $q->where('collection_id', '!=', $product->collection_id))
            ->with(['primaryImage', 'images', 'variants', 'translations', 'collection.translations', 'colors.translations'])
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // Fallback if no featured items in other collections
        if ($completeTheLook->isEmpty()) {
            $completeTheLook = Product::active()
                ->where('id', '!=', $product->id)
                ->whereNotIn('id', $related->pluck('id'))
                ->with(['primaryImage', 'images', 'variants', 'translations', 'collection.translations', 'colors.translations'])
                ->inRandomOrder()
                ->limit(4)
                ->get();
        }

        // Frequently bought together (collaborative filtering from orders)
        $frequentlyBought = $this->recommendations->frequentlyBoughtTogether($product->id, 3);

        // Customers also bought
        $customersAlsoBought = $this->recommendations->customersAlsoBought($product->id, 6);

        // Track recently-viewed in session (max 12, dedup)
        $this->trackRecentlyViewed($product->id);

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

        $breadcrumbs = [
            ['name' => $locale === 'en' ? 'Home' : 'หน้าแรก', 'url' => route('home', ['locale' => $locale])],
            ['name' => $locale === 'en' ? 'Shop' : 'ร้านค้า', 'url' => route('shop.index', ['locale' => $locale])],
        ];
        if ($product->collection) {
            $breadcrumbs[] = [
                'name' => $product->collection->localized_name ?? $product->collection->name,
                'url' => route('collections.show', ['locale' => $locale, 'collection' => $product->collection->slug]),
            ];
        }
        $breadcrumbs[] = [
            'name' => $product->localized_name,
            'url' => Seo::canonical(),
        ];

        $jsonLd = [
            Seo::productJsonLd($product, $ogImage),
            Seo::breadcrumbJsonLd($breadcrumbs),
        ];

        $ogType = 'product';

        return view('pages.products.show', compact('product', 'related', 'completeTheLook', 'frequentlyBought', 'customersAlsoBought', 'title', 'description', 'ogImage', 'inWishlist', 'jsonLd', 'ogType'));
    }

    private function trackRecentlyViewed(int $productId): void
    {
        $key = 'recently_viewed_products';
        $list = collect(session($key, []))->reject(fn ($id) => $id === $productId)->values();
        $list->prepend($productId);
        session([$key => $list->take(12)->values()->all()]);
    }
}
