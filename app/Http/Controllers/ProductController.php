<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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
            'variants',
        ]);

        // Related products: same collection, excluding current
        $related = Product::active()
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('collection_id', $product->collection_id)
                    ->orWhere('category_id', $product->category_id);
            })
            ->with(['primaryImage', 'images', 'variants', 'translations', 'colors.translations'])
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('pages.products.show', compact('product', 'related'));
    }
}
